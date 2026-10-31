<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\DetailPenerimaan;
use App\Models\DetailPenjualan;
use App\Models\DiscountUsage;
use App\Models\Kategori;
use App\Models\Pabrik;
use App\Models\Pelanggan;
use App\Models\Penerimaan;
use App\Models\Penjualan;
use App\Models\Rusak;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanFilterAndPaginationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Kategori $kategori;
    private Satuan $satuan;
    private Supplier $supplier;
    private Barang $barang1;
    private Barang $barang2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Apotek',
            'email' => 'admin@apotek.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'aktif' => true,
        ]);

        $this->kategori = Kategori::create(['nama' => 'Obat Bebas']);
        $this->satuan = Satuan::create(['nama' => 'Strip']);
        $this->supplier = Supplier::create([
            'nama' => 'PT Kimia Sehat',
            'alamat' => 'Jl. Farmasi No. 1',
            'telepon' => '08123456789',
        ]);

        $pabrik = Pabrik::create(['nama' => 'Pabrik Kalbe']);

        $this->barang1 = Barang::create([
            'kategori_id' => $this->kategori->id,
            'satuan_id' => $this->satuan->id,
            'pabrik_id' => $pabrik->id,
            'nama' => 'Paracetamol 500mg',
            'stok_minimum' => 10,
        ]);

        $this->barang2 = Barang::create([
            'kategori_id' => $this->kategori->id,
            'satuan_id' => $this->satuan->id,
            'pabrik_id' => $pabrik->id,
            'nama' => 'Amoxicillin 500mg',
            'stok_minimum' => 5,
        ]);
    }

    public function test_laporan_stok_does_not_use_date_filter_and_shows_all_stock_without_double_pagination(): void
    {
        // 1 batch from 2 months ago, 1 batch from this month
        $penerimaanLama = Penerimaan::create([
            'user_id' => $this->admin->id,
            'supplier_id' => $this->supplier->id,
            'tanggal' => now()->subMonths(2)->startOfMonth()->toDateString(),
            'no_faktur' => 'FAK-OLD-01',
            'lunas' => true,
        ]);

        $penerimaanBaru = Penerimaan::create([
            'user_id' => $this->admin->id,
            'supplier_id' => $this->supplier->id,
            'tanggal' => now()->startOfMonth()->toDateString(),
            'no_faktur' => 'FAK-NEW-01',
            'lunas' => true,
        ]);

        DetailPenerimaan::create([
            'penerimaan_id' => $penerimaanLama->id,
            'barang_id' => $this->barang1->id,
            'no_batch' => 'BATCH-OLD',
            'harga_beli' => 5000,
            'harga_jual' => 8000,
            'expired_date' => now()->addYear()->toDateString(),
            'jumlah' => 50,
            'stok' => 50,
            'aktif' => true,
        ]);

        DetailPenerimaan::create([
            'penerimaan_id' => $penerimaanBaru->id,
            'barang_id' => $this->barang2->id,
            'no_batch' => 'BATCH-NEW',
            'harga_beli' => 10000,
            'harga_jual' => 15000,
            'expired_date' => now()->addYears(2)->toDateString(),
            'jumlah' => 30,
            'stok' => 30,
            'aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('laporan.stok'));

        $response->assertOk();
        // Pastikan tidak ada input tanggal awal / tanggal akhir
        $response->assertDontSee('name="dari"', false);
        $response->assertDontSee('name="sampai"', false);

        // Kedua batch (lama dan baru) harus tampil
        $response->assertSee('BATCH-OLD');
        $response->assertSee('BATCH-NEW');
        $response->assertSee('Paracetamol 500mg');
        $response->assertSee('Amoxicillin 500mg');

        // Pastikan teks pagination TIDAK duplikat dalam area pagination
        $content = $response->getContent();
        $this->assertEquals(1, substr_count($content, 'Menampilkan'));
        $this->assertStringContainsString('dari', $content);
        $this->assertStringContainsString('data', $content);

        // Pastikan tfoot total yang diminta sudah hilang
        $response->assertDontSee('Total (Ringkasan Data Tampil):');
    }

    public function test_laporan_penerimaan_uses_date_filter_defaults_to_current_month_without_double_pagination(): void
    {
        // 1 penerimaan bulan lalu, 1 penerimaan bulan ini
        $penerimaanLalu = Penerimaan::create([
            'user_id' => $this->admin->id,
            'supplier_id' => $this->supplier->id,
            'tanggal' => now()->subMonth()->startOfMonth()->toDateString(),
            'no_faktur' => 'FAK-PREV-01',
            'lunas' => true,
        ]);
        DetailPenerimaan::create([
            'penerimaan_id' => $penerimaanLalu->id,
            'barang_id' => $this->barang1->id,
            'no_batch' => 'BATCH-PREV',
            'harga_beli' => 5000,
            'harga_jual' => 8000,
            'expired_date' => now()->addYear()->toDateString(),
            'jumlah' => 10,
            'stok' => 10,
            'aktif' => true,
        ]);

        $penerimaanIni = Penerimaan::create([
            'user_id' => $this->admin->id,
            'supplier_id' => $this->supplier->id,
            'tanggal' => now()->startOfMonth()->toDateString(),
            'no_faktur' => 'FAK-CURR-01',
            'lunas' => true,
        ]);
        DetailPenerimaan::create([
            'penerimaan_id' => $penerimaanIni->id,
            'barang_id' => $this->barang2->id,
            'no_batch' => 'BATCH-CURR',
            'harga_beli' => 10000,
            'harga_jual' => 15000,
            'expired_date' => now()->addYear()->toDateString(),
            'jumlah' => 20,
            'stok' => 20,
            'aktif' => true,
        ]);

        // Request default (bulan berjalan)
        $response = $this->actingAs($this->admin)->get(route('laporan.penerimaan'));
        $response->assertOk();

        // Memiliki input tanggal awal & akhir
        $response->assertSee('name="dari"', false);
        $response->assertSee('name="sampai"', false);
        $response->assertSee(now()->startOfMonth()->toDateString());
        $response->assertSee(now()->endOfMonth()->toDateString());

        // Hanya penerimaan bulan berjalan yang tampil
        $response->assertSee('FAK-CURR-01');
        $response->assertDontSee('FAK-PREV-01');

        // Pastikan pagination hanya muncul 1 kali
        $content = $response->getContent();
        $this->assertEquals(1, substr_count($content, 'Menampilkan'));

        // Pastikan tfoot Total Nilai Penerimaan sudah hilang
        $response->assertDontSee('Total Nilai Penerimaan:');

        // Uji tanggal tidak valid (dari > sampai)
        $invalidResponse = $this->actingAs($this->admin)->get(route('laporan.penerimaan', [
            'dari' => now()->endOfMonth()->toDateString(),
            'sampai' => now()->startOfMonth()->toDateString(),
        ]));
        $invalidResponse->assertOk();
        $invalidResponse->assertSee('Tanggal awal tidak boleh lebih besar daripada tanggal akhir.');
        $invalidResponse->assertDontSee('FAK-CURR-01');
    }

    public function test_laporan_penjualan_uses_date_filter_defaults_to_current_month_without_double_pagination(): void
    {
        $penjualanLalu = Penjualan::create([
            'user_id' => $this->admin->id,
            'tanggal' => now()->subMonth()->startOfMonth()->toDateString(),
            'no_faktur' => 'INV-OLD-001',
            'total' => 50000,
        ]);

        $penjualanIni = Penjualan::create([
            'user_id' => $this->admin->id,
            'tanggal' => now()->startOfMonth()->toDateString(),
            'no_faktur' => 'INV-NOW-001',
            'total' => 75000,
        ]);

        // Buka default bulan berjalan
        $response = $this->actingAs($this->admin)->get(route('laporan.penjualan'));
        $response->assertOk();

        $response->assertSee('name="dari"', false);
        $response->assertSee('name="sampai"', false);
        $response->assertSee('INV-NOW-001');
        $response->assertDontSee('INV-OLD-001');

        // Pagination hanya muncul 1 kali
        $content = $response->getContent();
        $this->assertEquals(1, substr_count($content, 'Menampilkan'));

        // Footer total pada tabel web sudah dihilangkan
        $response->assertDontSee('Total Akumulasi');

        // Uji validasi tanggal
        $invalidResponse = $this->actingAs($this->admin)->get(route('laporan.penjualan', [
            'dari' => '2026-12-31',
            'sampai' => '2026-01-01',
        ]));
        $invalidResponse->assertOk();
        $invalidResponse->assertSee('Tanggal awal tidak boleh lebih besar daripada tanggal akhir.');
    }

    public function test_laporan_rusak_uses_date_filter_defaults_to_current_month_without_double_pagination(): void
    {
        $penerimaan = Penerimaan::create([
            'user_id' => $this->admin->id,
            'supplier_id' => $this->supplier->id,
            'tanggal' => now()->subMonths(3)->toDateString(),
            'no_faktur' => 'FAK-RUSAK-01',
            'lunas' => true,
        ]);
        $detail = DetailPenerimaan::create([
            'penerimaan_id' => $penerimaan->id,
            'barang_id' => $this->barang1->id,
            'no_batch' => 'BATCH-RSK',
            'harga_beli' => 5000,
            'harga_jual' => 8000,
            'expired_date' => now()->addYear()->toDateString(),
            'jumlah' => 50,
            'stok' => 50,
            'aktif' => true,
        ]);

        Rusak::create([
            'detail_penerimaan_id' => $detail->id,
            'tanggal' => now()->subMonth()->toDateString(),
            'jumlah' => 2,
            'keterangan' => 'Pecah bulan lalu',
        ]);

        Rusak::create([
            'detail_penerimaan_id' => $detail->id,
            'tanggal' => now()->startOfMonth()->toDateString(),
            'jumlah' => 3,
            'keterangan' => 'Kadaluarsa bulan ini',
        ]);

        $response = $this->actingAs($this->admin)->get(route('laporan.rusak'));
        $response->assertOk();

        $response->assertSee('name="dari"', false);
        $response->assertSee('name="sampai"', false);
        $response->assertSee('Kadaluarsa bulan ini');
        $response->assertDontSee('Pecah bulan lalu');

        $content = $response->getContent();
        $this->assertEquals(1, substr_count($content, 'Menampilkan'));
        $response->assertDontSee('Total Kerugian Finansial:');
    }

    public function test_laporan_diskon_uses_date_filter_defaults_to_current_month_without_double_pagination(): void
    {
        $penerimaan = Penerimaan::create([
            'user_id' => $this->admin->id,
            'supplier_id' => $this->supplier->id,
            'tanggal' => now()->subMonths(2)->toDateString(),
            'no_faktur' => 'FAK-DISKON',
            'lunas' => true,
        ]);
        $detailPenerimaan = DetailPenerimaan::create([
            'penerimaan_id' => $penerimaan->id,
            'barang_id' => $this->barang1->id,
            'no_batch' => 'BATCH-DSK',
            'harga_beli' => 5000,
            'harga_jual' => 10000,
            'expired_date' => now()->addYear()->toDateString(),
            'jumlah' => 100,
            'stok' => 100,
            'aktif' => true,
        ]);

        $penjualanLalu = Penjualan::create([
            'user_id' => $this->admin->id,
            'tanggal' => now()->subMonth()->toDateString(),
            'no_faktur' => 'INV-DSK-OLD',
            'total' => 18000,
        ]);
        $detailPenjualanLalu = DetailPenjualan::create([
            'penjualan_id' => $penjualanLalu->id,
            'detail_penerimaan_id' => $detailPenerimaan->id,
            'jumlah' => 2,
            'harga_beli' => 5000,
            'harga_jual' => 10000,
            'diskon' => 2000,
            'subtotal' => 18000,
        ]);
        DiscountUsage::create([
            'penjualan_id' => $penjualanLalu->id,
            'detail_penjualan_id' => $detailPenjualanLalu->id,
            'barang_id' => $this->barang1->id,
            'barang_nama' => 'Paracetamol 500mg',
            'jenis' => 'member',
            'persentase' => 10,
            'nominal' => 2000,
        ]);

        $penjualanIni = Penjualan::create([
            'user_id' => $this->admin->id,
            'tanggal' => now()->startOfMonth()->toDateString(),
            'no_faktur' => 'INV-DSK-NOW',
            'total' => 27000,
        ]);
        $detailPenjualanIni = DetailPenjualan::create([
            'penjualan_id' => $penjualanIni->id,
            'detail_penerimaan_id' => $detailPenerimaan->id,
            'jumlah' => 3,
            'harga_beli' => 5000,
            'harga_jual' => 10000,
            'diskon' => 3000,
            'subtotal' => 27000,
        ]);
        DiscountUsage::create([
            'penjualan_id' => $penjualanIni->id,
            'detail_penjualan_id' => $detailPenjualanIni->id,
            'barang_id' => $this->barang1->id,
            'barang_nama' => 'Paracetamol 500mg',
            'jenis' => 'member',
            'persentase' => 10,
            'nominal' => 3000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('laporan.diskon'));
        $response->assertOk();

        $response->assertSee('name="dari"', false);
        $response->assertSee('name="sampai"', false);
        $response->assertSee('INV-DSK-NOW');
        $response->assertDontSee('INV-DSK-OLD');

        $content = $response->getContent();
        $this->assertEquals(1, substr_count($content, 'Menampilkan'));
        $response->assertDontSee('Total Diskon:');
    }
}
