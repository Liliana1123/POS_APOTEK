<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\DetailPenerimaan;
use App\Models\Kategori;
use App\Models\Pabrik;
use App\Models\Pelanggan;
use App\Models\Penerimaan;
use App\Models\Penjualan;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CetakStrukTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(): array
    {
        $user = User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir@test.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'aktif' => true,
        ]);

        $satuan = Satuan::create(['nama' => 'Strip']);
        $kategori = Kategori::create(['nama' => 'Obat Bebas']);
        $pabrik = Pabrik::create(['nama' => 'Kalbe', 'telepon' => '021', 'alamat' => 'Jakarta']);
        $supplier = Supplier::create([
            'nama' => 'PT Kimia Farma',
            'kontak' => '081234567890',
            'alamat' => 'Jakarta',
        ]);

        $barang = Barang::create([
            'kode_apotek' => 'OBT-001',
            'nama' => 'Paracetamol 500mg',
            'satuan_id' => $satuan->id,
            'kategori_id' => $kategori->id,
            'pabrik_id' => $pabrik->id,
            'stok_minimum' => 10,
            'butuh_resep' => false,
            'aktif' => true,
        ]);

        $penerimaan = Penerimaan::create([
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'tanggal' => now()->format('Y-m-d'),
            'no_faktur' => 'RCV-001',
            'lunas' => true,
        ]);

        DetailPenerimaan::create([
            'penerimaan_id' => $penerimaan->id,
            'barang_id' => $barang->id,
            'no_batch' => 'BATCH-001',
            'expired_date' => now()->addYear()->format('Y-m-d'),
            'jumlah' => 20,
            'stok' => 20,
            'harga_beli' => 2500,
            'harga_jual' => 5000,
            'subtotal' => 50000,
            'aktif' => true,
        ]);

        return [$user, $barang];
    }

    public function test_transaksi_berhasil_disimpan_dan_diarahkan_ke_halaman_detail_struk(): void
    {
        [$user, $barang] = $this->setupData();

        $payload = [
            'tanggal' => now()->format('Y-m-d'),
            'no_faktur' => 'INV-STRUK-001',
            'jenis_transaksi' => 'non_resep',
            'metode_pembayaran' => 'cash',
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 2,
                ]
            ],
        ];

        $response = $this->actingAs($user)->post(route('penjualan.store'), $payload);

        $penjualan = Penjualan::where('no_faktur', 'INV-STRUK-001')->first();
        $this->assertNotNull($penjualan);
        $response->assertRedirect(route('penjualan.show', $penjualan));

        // Mengakses halaman detail struk yang diarahkan
        $strukResponse = $this->actingAs($user)->get(route('penjualan.show', $penjualan));
        $strukResponse->assertStatus(200);

        // Data transaksi dari database
        $strukResponse->assertSee('INV-STRUK-001');
        $strukResponse->assertSee('Paracetamol 500mg');
        $strukResponse->assertSee('Print Struk');
        $strukResponse->assertSee('Kembali');
        $strukResponse->assertSee(route('penjualan.create')); // Tombol kembali mengarah ke kasir

        // Validasi CSS & opsi ukuran kertas EPPOS thermal (58mm dan 80mm)
        $strukResponse->assertSee('Thermal 58 mm (EPPOS)');
        $strukResponse->assertSee('Thermal 80 mm (EPPOS)');
        $strukResponse->assertSee('window.print()', false);
        $strukResponse->assertSee('@media print', false);
        $strukResponse->assertSee('58mm', false);
        $strukResponse->assertSee('80mm', false);
    }

    public function test_tombol_kembali_mengarahkan_ke_riwayat_bila_dibuka_dari_index(): void
    {
        [$user, $barang] = $this->setupData();

        $penjualan = Penjualan::create([
            'user_id' => $user->id,
            'tanggal' => now()->format('Y-m-d'),
            'no_faktur' => 'INV-STRUK-002',
            'total' => 10000,
            'metode_pembayaran' => 'cash',
            'jenis_transaksi' => 'non_resep',
        ]);

        $response = $this->actingAs($user)->get(route('penjualan.show', [
            'penjualan' => $penjualan,
            'from' => 'penjualan_index',
        ]));

        $response->assertStatus(200);
        $response->assertSee(route('penjualan.index'));
    }
}
