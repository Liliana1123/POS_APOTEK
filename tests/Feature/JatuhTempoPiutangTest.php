<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\DetailPenerimaan;
use App\Models\Kategori;
use App\Models\Pabrik;
use App\Models\Pelanggan;
use App\Models\PembayaranPiutang;
use App\Models\Penerimaan;
use App\Models\Penjualan;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class JatuhTempoPiutangTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(): array
    {
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@apotek.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'aktif' => true,
        ]);

        $member = Pelanggan::create([
            'nama' => 'Nurul Member',
            'telepon' => '081234567890',
            'alamat' => 'Jl. Mawar No. 10',
            'is_member' => true,
            'member_aktif' => true,
            'member_id' => 'MBR-000001',
            'status_member' => 'Member Pelanggan Tetap',
            'saldo_piutang' => 0,
        ]);

        $umum = Pelanggan::create([
            'nama' => 'Umum',
            'telepon' => null,
            'alamat' => null,
            'is_member' => false,
            'member_aktif' => false,
            'member_id' => null,
            'keterangan' => 'Pelanggan Umum',
            'saldo_piutang' => 0,
        ]);

        $kategori = Kategori::create(['nama' => 'Obat Bebas']);
        $satuan = Satuan::create(['nama' => 'Strip']);
        $pabrik = Pabrik::create(['nama' => 'Kalbe', 'telepon' => '021', 'alamat' => 'Jakarta']);
        $supplier = Supplier::create(['nama' => 'PT Distributor', 'telepon' => '021', 'alamat' => 'Jakarta']);

        $barang = Barang::create([
            'nama' => 'Amoxicillin 500mg',
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
            'no_faktur' => 'RCV-001',
            'lunas' => true,
        ]);

        $batch = DetailPenerimaan::create([
            'penerimaan_id' => $penerimaan->id,
            'barang_id' => $barang->id,
            'no_batch' => 'BATCH-AMX-01',
            'harga_beli' => 10000,
            'harga_jual' => 15000,
            'expired_date' => '2027-09-24',
            'jumlah' => 100,
            'stok' => 100,
            'aktif' => true,
        ]);

        return compact('user', 'member', 'umum', 'barang', 'batch');
    }

    public function test_member_dapat_menggunakan_piutang_dengan_jatuh_tempo_manual(): void
    {
        $data = $this->setupData();

        $response = $this->actingAs($data['user'])->post(route('penjualan.store'), [
            'pelanggan_id' => $data['member']->id,
            'tanggal' => '2026-09-24',
            'no_faktur' => 'INV-20260924-0001',
            'jenis_transaksi' => 'non_resep',
            'metode_pembayaran' => 'piutang',
            'due_date' => '2026-10-15',
            'items' => [
                ['barang_id' => $data['barang']->id, 'jumlah' => 2],
            ],
        ]);

        $response->assertRedirect();

        $penjualan = Penjualan::where('no_faktur', 'INV-20260924-0001')->first();
        $this->assertNotNull($penjualan);
        $this->assertEquals('piutang', $penjualan->metode_pembayaran);
        $this->assertEquals('2026-10-15', $penjualan->due_date->format('Y-m-d'));

        // Member saldo piutang bertambah
        $data['member']->refresh();
        $this->assertEquals($penjualan->total, $data['member']->saldo_piutang);

        // Struk menampilkan Jatuh Tempo
        $strukResponse = $this->actingAs($data['user'])->get(route('penjualan.show', $penjualan));
        $strukResponse->assertStatus(200);
        $strukResponse->assertSee('Jatuh Tempo');
        $strukResponse->assertSee('15 Oct 2026');
    }

    public function test_member_piutang_dengan_jatuh_tempo_kosong_otomatis_plus_1_bulan(): void
    {
        $data = $this->setupData();

        $response = $this->actingAs($data['user'])->post(route('penjualan.store'), [
            'pelanggan_id' => $data['member']->id,
            'tanggal' => '2026-09-24',
            'no_faktur' => 'INV-20260924-0002',
            'jenis_transaksi' => 'non_resep',
            'metode_pembayaran' => 'piutang',
            'due_date' => '',
            'items' => [
                ['barang_id' => $data['barang']->id, 'jumlah' => 1],
            ],
        ]);

        $response->assertRedirect();

        $penjualan = Penjualan::where('no_faktur', 'INV-20260924-0002')->first();
        $this->assertNotNull($penjualan);
        $this->assertEquals('piutang', $penjualan->metode_pembayaran);
        // 2026-09-24 + 1 month = 2026-10-24
        $this->assertEquals('2026-10-24', $penjualan->due_date->format('Y-m-d'));
    }

    public function test_member_cash_tidak_menyimpan_jatuh_tempo_dan_struk_tidak_menampilkan_jatuh_tempo(): void
    {
        $data = $this->setupData();

        $response = $this->actingAs($data['user'])->post(route('penjualan.store'), [
            'pelanggan_id' => $data['member']->id,
            'tanggal' => '2026-09-24',
            'no_faktur' => 'INV-20260924-0003',
            'jenis_transaksi' => 'non_resep',
            'metode_pembayaran' => 'cash',
            'due_date' => '2026-10-20', // Should be ignored/cleared for non-piutang
            'items' => [
                ['barang_id' => $data['barang']->id, 'jumlah' => 1],
            ],
        ]);

        $response->assertRedirect();

        $penjualan = Penjualan::where('no_faktur', 'INV-20260924-0003')->first();
        $this->assertNotNull($penjualan);
        $this->assertEquals('cash', $penjualan->metode_pembayaran);
        $this->assertNull($penjualan->due_date);

        // Struk Cash tidak boleh menampilkan Jatuh Tempo
        $strukResponse = $this->actingAs($data['user'])->get(route('penjualan.show', $penjualan));
        $strukResponse->assertStatus(200);
        $strukResponse->assertDontSee('Jatuh Tempo');
    }

    public function test_pelanggan_umum_ditolak_menggunakan_piutang(): void
    {
        $data = $this->setupData();

        // 1. Pelanggan umum dengan pelanggan_id
        $response = $this->actingAs($data['user'])->post(route('penjualan.store'), [
            'pelanggan_id' => $data['umum']->id,
            'tanggal' => '2026-09-24',
            'no_faktur' => 'INV-20260924-0004',
            'jenis_transaksi' => 'non_resep',
            'metode_pembayaran' => 'piutang',
            'items' => [
                ['barang_id' => $data['barang']->id, 'jumlah' => 1],
            ],
        ]);

        $response->assertSessionHasErrors(['metode_pembayaran']);

        // 2. Pelanggan umum tanpa pelanggan_id (manipulasi request)
        $response2 = $this->actingAs($data['user'])->post(route('penjualan.store'), [
            'pelanggan_id' => null,
            'pelanggan_nama' => 'Pembeli Asing',
            'tanggal' => '2026-09-24',
            'no_faktur' => 'INV-20260924-0005',
            'jenis_transaksi' => 'non_resep',
            'metode_pembayaran' => 'piutang',
            'items' => [
                ['barang_id' => $data['barang']->id, 'jumlah' => 1],
            ],
        ]);

        $response2->assertSessionHasErrors(['metode_pembayaran']);
    }

    public function test_jatuh_tempo_tidak_boleh_lebih_awal_dari_tanggal_transaksi(): void
    {
        $data = $this->setupData();

        $response = $this->actingAs($data['user'])->post(route('penjualan.store'), [
            'pelanggan_id' => $data['member']->id,
            'tanggal' => '2026-09-24',
            'no_faktur' => 'INV-20260924-0006',
            'jenis_transaksi' => 'non_resep',
            'metode_pembayaran' => 'piutang',
            'due_date' => '2026-09-20', // Lebih awal dari 2026-09-24
            'items' => [
                ['barang_id' => $data['barang']->id, 'jumlah' => 1],
            ],
        ]);

        $response->assertSessionHasErrors(['due_date']);
    }

    public function test_tampilan_jatuh_tempo_di_halaman_pelanggan_dan_detail_dan_pembayaran(): void
    {
        $data = $this->setupData();

        // Buat penjualan piutang
        $this->actingAs($data['user'])->post(route('penjualan.store'), [
            'pelanggan_id' => $data['member']->id,
            'tanggal' => '2026-09-24',
            'no_faktur' => 'INV-20260924-0007',
            'jenis_transaksi' => 'non_resep',
            'metode_pembayaran' => 'piutang',
            'due_date' => '2026-10-15',
            'items' => [
                ['barang_id' => $data['barang']->id, 'jumlah' => 2],
            ],
        ]);

        $penjualan = Penjualan::where('no_faktur', 'INV-20260924-0007')->first();

        // 1. Cek view pelanggan.index
        $indexResponse = $this->actingAs($data['user'])->get(route('pelanggan.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Jatuh Tempo');
        $indexResponse->assertSee('15 Oct 2026');

        // 2. Cek detail pelanggan JSON
        $detailResponse = $this->actingAs($data['user'])->getJson(route('pelanggan.show', $data['member']));
        $detailResponse->assertStatus(200);
        $detailResponse->assertJsonPath('pelanggan.jatuh_tempo', '15 Oct 2026');

        // 3. Cek piutang pelanggan JSON
        $piutangResponse = $this->actingAs($data['user'])->getJson(route('pelanggan.piutang', $data['member']));
        $piutangResponse->assertStatus(200);
        $piutangResponse->assertJsonPath('pelanggan.jatuh_tempo', '15 Oct 2026');
        $piutangResponse->assertJsonPath('penjualans.0.due_date_formatted', '15 Oct 2026');

        // 4. Cek detail penjualan JSON
        $penjualanDetailResponse = $this->actingAs($data['user'])->getJson(route('penjualan.detail', $penjualan));
        $penjualanDetailResponse->assertStatus(200);
        $penjualanDetailResponse->assertJsonPath('penjualan.due_date_formatted', '15 Oct 2026');

        // 5. Cek form pembayaran piutang JSON
        $formResponse = $this->actingAs($data['user'])->getJson(route('penjualan.piutang.payments.form', $penjualan));
        $formResponse->assertStatus(200);
        $formResponse->assertJsonPath('penjualan.due_date_formatted', '15 Oct 2026');
    }

    public function test_pembayaran_piutang_sampai_lunas_mengosongkan_jatuh_tempo_aktif(): void
    {
        $data = $this->setupData();

        $this->actingAs($data['user'])->post(route('penjualan.store'), [
            'pelanggan_id' => $data['member']->id,
            'tanggal' => '2026-09-24',
            'no_faktur' => 'INV-20260924-0008',
            'jenis_transaksi' => 'non_resep',
            'metode_pembayaran' => 'piutang',
            'due_date' => '2026-10-15',
            'items' => [
                ['barang_id' => $data['barang']->id, 'jumlah' => 1],
            ],
        ]);

        $penjualan = Penjualan::where('no_faktur', 'INV-20260924-0008')->first();

        // Lakukan pembayaran lunas
        $bayarResponse = $this->actingAs($data['user'])->postJson(route('penjualan.piutang.payments.store', $penjualan), [
            'tanggal_bayar' => '2026-09-25',
            'jumlah' => $penjualan->total,
            'keterangan' => 'Pelunasan piutang',
        ]);

        $bayarResponse->assertStatus(200);
        $bayarResponse->assertJsonPath('sisa_piutang', 0);

        // Setelah lunas, member saldo piutang = 0
        $data['member']->refresh();
        $this->assertEquals(0, (float) $data['member']->saldo_piutang);
        $this->assertNull($data['member']->jatuh_tempo_aktif);

        // Detail pelanggan JSON jatuh_tempo menjadi '-'
        $detailResponse = $this->actingAs($data['user'])->getJson(route('pelanggan.show', $data['member']));
        $detailResponse->assertJsonPath('pelanggan.jatuh_tempo', '-');
    }
}
