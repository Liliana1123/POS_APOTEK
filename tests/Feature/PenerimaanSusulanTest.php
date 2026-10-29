<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\DetailPenerimaan;
use App\Models\DetailPesananPenerimaan;
use App\Models\Kategori;
use App\Models\Pabrik;
use App\Models\Penerimaan;
use App\Models\RiwayatPenerimaan;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenerimaanSusulanTest extends TestCase
{
    use RefreshDatabase;

    private function createIncompletePenerimaan(): array
    {
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-susulan@test.local',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'aktif' => true,
        ]);
        $supplier = Supplier::create([
            'nama' => 'Supplier Test',
            'telepon' => '021',
            'alamat' => 'Jakarta',
        ]);
        $kategori = Kategori::create(['nama' => 'Kategori Test']);
        $satuan = Satuan::create(['nama' => 'Strip']);
        $pabrik = Pabrik::create([
            'nama' => 'Pabrik Test',
            'telepon' => '021',
            'alamat' => 'Jakarta',
        ]);
        $barang = Barang::create([
            'nama' => 'Obat Test',
            'kategori_id' => $kategori->id,
            'satuan_id' => $satuan->id,
            'pabrik_id' => $pabrik->id,
            'butuh_resep' => false,
            'stok_minimum' => 5,
            'aktif' => true,
        ]);
        $penerimaan = Penerimaan::create([
            'user_id' => $user->id,
            'supplier_id' => $supplier->id,
            'tanggal' => '2026-09-24',
            'tanggal_faktur' => '2026-09-24',
            'no_faktur' => 'RCV-SUSULAN-001',
            'lunas' => true,
        ]);
        $detailPesanan = DetailPesananPenerimaan::create([
            'penerimaan_id' => $penerimaan->id,
            'barang_id' => $barang->id,
            'jumlah_dipesan' => 5,
        ]);
        $detailAwal = DetailPenerimaan::create([
            'penerimaan_id' => $penerimaan->id,
            'barang_id' => $barang->id,
            'no_batch' => 'BATCH-AWAL',
            'harga_beli' => 10000,
            'harga_jual' => 15000,
            'expired_date' => '2027-09-24',
            'no_rak' => 'A-01',
            'jumlah' => 2,
            'stok' => 2,
            'aktif' => true,
        ]);
        RiwayatPenerimaan::create([
            'penerimaan_id' => $penerimaan->id,
            'detail_pesanan_penerimaan_id' => $detailPesanan->id,
            'detail_penerimaan_id' => $detailAwal->id,
            'jenis' => 'penerimaan',
            'jumlah' => 2,
            'tanggal' => '2026-09-24',
            'keterangan' => 'Penerimaan awal',
            'user_id' => $user->id,
        ]);

        return compact('user', 'penerimaan', 'detailPesanan');
    }

    private function susulanPayload(DetailPesananPenerimaan $detailPesanan): array
    {
        return [
            'tanggal_terima' => '2026-10-01',
            'jumlah_susulan' => [$detailPesanan->id => 3],
            'jumlah_pembatalan' => [$detailPesanan->id => 0],
            'detail' => [$detailPesanan->id => [
                'no_batch' => 'BATCH-SUSULAN',
                'expired_date' => '2027-10-01',
                'harga_beli' => 11000,
                'harga_jual' => 16000,
                'no_rak' => 'A-02',
            ]],
        ];
    }

    public function test_susulan_saves_stock_and_history(): void
    {
        $data = $this->createIncompletePenerimaan();

        $response = $this->actingAs($data['user'])
            ->postJson(
                route('penerimaan.susulan.store', $data['penerimaan']),
                $this->susulanPayload($data['detailPesanan'])
            );

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Penerimaan susulan berhasil disimpan.',
            ]);

        $this->assertDatabaseHas('detail_penerimaans', [
            'penerimaan_id' => $data['penerimaan']->id,
            'barang_id' => $data['detailPesanan']->barang_id,
            'no_batch' => 'BATCH-SUSULAN',
            'jumlah' => 3,
            'stok' => 3,
        ]);
        $this->assertDatabaseHas('riwayat_penerimaans', [
            'penerimaan_id' => $data['penerimaan']->id,
            'detail_pesanan_penerimaan_id' => $data['detailPesanan']->id,
            'jenis' => 'penerimaan',
            'jumlah' => 3,
            'keterangan' => 'Penerimaan susulan',
        ]);
        $this->assertSame(
            'LENGKAP',
            $data['penerimaan']->fresh()->statusPenerimaan()
        );
    }

    public function test_susulan_rejects_missing_batch_without_writing(): void
    {
        $data = $this->createIncompletePenerimaan();
        $payload = $this->susulanPayload($data['detailPesanan']);
        $payload['detail'][$data['detailPesanan']->id]['no_batch'] = '';

        $this->actingAs($data['user'])
            ->postJson(
                route('penerimaan.susulan.store', $data['penerimaan']),
                $payload
            )
            ->assertUnprocessable()
            ->assertJsonValidationErrors('detail');

        $this->assertDatabaseCount('detail_penerimaans', 1);
        $this->assertDatabaseCount('riwayat_penerimaans', 1);
    }

    public function test_susulan_can_cancel_part_of_the_remaining_quantity(): void
    {
        $data = $this->createIncompletePenerimaan();
        $payload = $this->susulanPayload($data['detailPesanan']);
        $payload['jumlah_susulan'][$data['detailPesanan']->id] = 0;
        $payload['jumlah_pembatalan'][$data['detailPesanan']->id] = 2;
        $payload['keterangan'] = 'Gagal karena tidak jadi dikirim';
        unset($payload['detail']);

        $this->actingAs($data['user'])
            ->postJson(
                route('penerimaan.susulan.store', $data['penerimaan']),
                $payload
            )
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Penerimaan susulan berhasil disimpan.',
            ]);

        $this->assertDatabaseHas('riwayat_penerimaans', [
            'penerimaan_id' => $data['penerimaan']->id,
            'detail_pesanan_penerimaan_id' => $data['detailPesanan']->id,
            'detail_penerimaan_id' => null,
            'jenis' => 'pembatalan',
            'jumlah' => 2,
            'keterangan' => 'Gagal karena tidak jadi dikirim',
        ]);
        $this->assertSame(
            1,
            $data['detailPesanan']->fresh()->kekurangan()
        );
    }
}
