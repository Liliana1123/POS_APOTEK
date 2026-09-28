<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\DetailPenerimaan;
use App\Models\Kategori;
use App\Models\Pabrik;
use App\Models\Penerimaan;
use App\Models\Penjualan;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenjualanResepTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(): array
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'aktif' => true,
        ]);

        $kategori = Kategori::create(['nama' => 'Obat Bebas']);
        $satuan = Satuan::create(['nama' => 'Strip']);
        $pabrik = Pabrik::create(['nama' => 'Kalbe', 'telepon' => '021', 'alamat' => 'Jakarta']);
        $supplier = Supplier::create(['nama' => 'PT Distributor', 'telepon' => '021', 'alamat' => 'Jakarta']);

        $barang = Barang::create([
            'nama' => 'Paracetamol 500mg',
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
            'tanggal' => now()->format('Y-m-d'),
            'no_faktur' => 'RCV-001',
            'lunas' => true,
        ]);

        DetailPenerimaan::create([
            'penerimaan_id' => $penerimaan->id,
            'barang_id' => $barang->id,
            'no_batch' => 'BATCH-01',
            'harga_beli' => 5000,
            'harga_jual' => 8000,
            'expired_date' => now()->addYear()->format('Y-m-d'),
            'jumlah' => 100,
            'stok' => 100,
            'aktif' => true,
        ]);

        return [$user, $barang];
    }

    public function test_halaman_transaksi_baru_dapat_diakses_dan_memiliki_elemen_sinkronisasi(): void
    {
        [$user] = $this->setupData();

        $response = $this->actingAs($user)->get(route('penjualan.create'));

        $response->assertStatus(200);

        // Verifikasi elemen radio kiri
        $response->assertSee('id="jenis-non-resep-kiri"', false);
        $response->assertSee('id="jenis-resep-kiri"', false);
        $response->assertSee('name="jenis_transaksi_kiri"', false);

        // Verifikasi elemen radio kanan & hidden input
        $response->assertSee('id="jenis-non-resep-kanan"', false);
        $response->assertSee('id="jenis-resep-kanan"', false);
        $response->assertSee('name="jenis_transaksi_kanan"', false);
        $response->assertSee('id="jenis-transaksi-value"', false);

        // Verifikasi form data resep tersembunyi
        $response->assertSee('id="form-data-resep"', false);

        // Verifikasi komponen pencarian pelanggan kembali ada
        $response->assertSee('id="pencarian-pelanggan"', false);

        // Verifikasi script sinkronkanJenisTransaksi ada
        $response->assertSee('sinkronkanJenisTransaksi', false);
        $response->assertSee('renderDaftarBarang', false);
    }

    public function test_transaksi_non_resep_berhasil_disimpan_tanpa_data_dokter(): void
    {
        [$user, $barang] = $this->setupData();

        $payload = [
            'tanggal' => now()->format('Y-m-d'),
            'no_faktur' => 'INV-TEST-001',
            'jenis_transaksi' => 'non_resep',
            'nama_dokter' => '',
            'id_dokter' => '',
            'alamat_lembaga' => '',
            'metode_pembayaran' => 'cash',
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 2,
                ]
            ],
        ];

        $response = $this->actingAs($user)->post(route('penjualan.store'), $payload);

        $penjualan = Penjualan::where('no_faktur', 'INV-TEST-001')->first();
        $this->assertNotNull($penjualan);
        $response->assertRedirect(route('penjualan.show', $penjualan));

        $this->assertDatabaseHas('penjualans', [
            'no_faktur' => 'INV-TEST-001',
            'jenis_transaksi' => 'non_resep',
            'nama_dokter' => null,
            'id_dokter' => null,
            'alamat_lembaga' => null,
        ]);
    }

    public function test_transaksi_resep_berhasil_disimpan_dengan_data_dokter(): void
    {
        [$user, $barang] = $this->setupData();

        $payload = [
            'tanggal' => now()->format('Y-m-d'),
            'no_faktur' => 'INV-TEST-002',
            'jenis_transaksi' => 'resep',
            'nama_dokter' => 'dr. Tirta Mandira',
            'id_dokter' => 'DOC-09921',
            'alamat_lembaga' => 'Klinik Sehat Sentosa',
            'metode_pembayaran' => 'cash',
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 3,
                ]
            ],
        ];

        $response = $this->actingAs($user)->post(route('penjualan.store'), $payload);

        $penjualan = Penjualan::where('no_faktur', 'INV-TEST-002')->first();
        $this->assertNotNull($penjualan);
        $response->assertRedirect(route('penjualan.show', $penjualan));

        $this->assertDatabaseHas('penjualans', [
            'no_faktur' => 'INV-TEST-002',
            'jenis_transaksi' => 'resep',
            'nama_dokter' => 'dr. Tirta Mandira',
            'id_dokter' => 'DOC-09921',
            'alamat_lembaga' => 'Klinik Sehat Sentosa',
        ]);
    }
}
