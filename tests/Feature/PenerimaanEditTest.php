<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\DetailPenerimaan;
use App\Models\Kategori;
use App\Models\Pabrik;
use App\Models\Penerimaan;
use App\Models\Rusak;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PenerimaanEditTest extends TestCase
{
    use RefreshDatabase;

    private function createPenerimaan(): array
    {
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-penerimaan@test.local',
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
            'no_faktur' => 'RCV-EDIT-001',
            'lunas' => true,
        ]);
        $detail = DetailPenerimaan::create([
            'penerimaan_id' => $penerimaan->id,
            'barang_id' => $barang->id,
            'no_batch' => 'BATCH-EDIT-001',
            'harga_beli' => 10000,
            'harga_jual' => 15000,
            'expired_date' => '2027-09-24',
            'no_rak' => 'A-01',
            'jumlah' => 2,
            'stok' => 2,
            'aktif' => true,
        ]);

        return compact('user', 'supplier', 'barang', 'penerimaan', 'detail');
    }

    private function updatePayload(array $data): array
    {
        return [
            'supplier_id' => $data['supplier']->id,
            'tanggal' => '2026-10-01',
            'tanggal_faktur' => '2026-10-01',
            'no_faktur' => 'RCV-EDIT-001',
            'pembayaran_pertama' => 26640,
            'items' => [[
                'detail_id' => $data['detail']->id,
                'barang_id' => $data['barang']->id,
                'no_batch' => 'BATCH-EDIT-001',
                'harga_beli' => 12000,
                'harga_jual' => 15000,
                'expired_date' => '2027-09-24',
                'no_rak' => 'A-01',
                'jumlah_dipesan' => 2,
                'jumlah_diterima' => 2,
            ]],
        ];
    }

    public function test_update_saves_database_values_and_edit_page_shows_latest_values(): void
    {
        $data = $this->createPenerimaan();
        $payload = $this->updatePayload($data);

        $response = $this->actingAs($data['user'])
            ->post(route('penerimaan.update', $data['penerimaan']), ['_method' => 'PUT'] + $payload, [
                'Accept' => 'application/json',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true);
        $response->assertSessionHas('success', 'Penerimaan berhasil diperbarui.');

        $this->assertDatabaseHas('penerimaans', [
            'id' => $data['penerimaan']->id,
            'tanggal' => '2026-10-01 00:00:00',
        ]);
        $this->assertDatabaseHas('detail_penerimaans', [
            'id' => $data['detail']->id,
            'harga_beli' => 12000,
        ]);

        $this->actingAs($data['user'])
            ->get(route('penerimaan.edit', $data['penerimaan']))
            ->assertOk()
            ->assertSee('value="2026-10-01"', false)
            ->assertSee('value="12000"', false);
    }

    public function test_invalid_item_prices_return_validation_errors_without_saving(): void
    {
        $data = $this->createPenerimaan();
        $payload = $this->updatePayload($data);
        $payload['items'][0]['harga_jual'] = 11000;

        $response = $this->actingAs($data['user'])
            ->putJson(route('penerimaan.update', $data['penerimaan']), $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['items.0.harga_jual']);

        $this->assertDatabaseHas('penerimaans', [
            'id' => $data['penerimaan']->id,
            'tanggal' => '2026-09-24 00:00:00',
        ]);
        $this->assertDatabaseHas('detail_penerimaans', [
            'id' => $data['detail']->id,
            'harga_beli' => 10000,
        ]);
    }

    public function test_edit_is_rejected_when_a_receipt_detail_is_marked_damaged(): void
    {
        $data = $this->createPenerimaan();
        Rusak::create([
            'detail_penerimaan_id' => $data['detail']->id,
            'tanggal' => '2026-09-25',
            'jumlah' => 1,
            'keterangan' => 'Uji pembatasan edit',
        ]);

        $response = $this->actingAs($data['user'])
            ->putJson(route('penerimaan.update', $data['penerimaan']), $this->updatePayload($data));

        $response->assertForbidden()
            ->assertJsonFragment(['message' => 'Penerimaan tidak dapat diedit karena sebagian barang tercatat sebagai barang rusak.']);

        $this->assertDatabaseHas('penerimaans', [
            'id' => $data['penerimaan']->id,
            'tanggal' => '2026-09-24 00:00:00',
        ]);
    }

    public function test_update_batches_relation_reads_for_many_details(): void
    {
        $data = $this->createPenerimaan();
        $details = collect([$data['detail']]);

        for ($index = 2; $index <= 25; $index++) {
            $details->push(DetailPenerimaan::create([
                'penerimaan_id' => $data['penerimaan']->id,
                'barang_id' => $data['barang']->id,
                'no_batch' => sprintf('BATCH-EDIT-%03d', $index),
                'harga_beli' => 10000,
                'harga_jual' => 15000,
                'expired_date' => '2027-09-24',
                'no_rak' => 'A-01',
                'jumlah' => 1,
                'stok' => 1,
                'aktif' => true,
            ]));
        }

        $payload = $this->updatePayload($data);
        $payload['pembayaran_pertama'] = 333000;
        $payload['items'] = $details->map(fn (DetailPenerimaan $detail) => [
            'detail_id' => $detail->id,
            'barang_id' => $data['barang']->id,
            'no_batch' => $detail->no_batch,
            'harga_beli' => 12000,
            'harga_jual' => 15000,
            'expired_date' => '2027-09-24',
            'no_rak' => 'A-01',
            'jumlah_dipesan' => 1,
            'jumlah_diterima' => 1,
        ])->all();
        $relationSelects = [
            'detail_penjualans' => 0,
            'rusaks' => 0,
            'riwayat_penerimaans' => 0,
        ];

        DB::listen(static function (QueryExecuted $query) use (&$relationSelects): void {
            $sql = strtolower(ltrim($query->sql));
            if (!str_starts_with($sql, 'select')) {
                return;
            }

            foreach ($relationSelects as $table => $count) {
                if (str_contains($sql, $table)) {
                    $relationSelects[$table]++;
                }
            }
        });

        $response = $this->actingAs($data['user'])
            ->putJson(route('penerimaan.update', $data['penerimaan']), $payload);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertLessThanOrEqual(2, $relationSelects['detail_penjualans']);
        $this->assertLessThanOrEqual(2, $relationSelects['rusaks']);
        $this->assertLessThanOrEqual(2, $relationSelects['riwayat_penerimaans']);
    }
}
