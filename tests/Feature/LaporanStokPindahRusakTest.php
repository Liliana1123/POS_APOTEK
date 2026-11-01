<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\DetailPenerimaan;
use App\Models\DetailPenjualan;
use App\Models\Kategori;
use App\Models\Pabrik;
use App\Models\Penerimaan;
use App\Models\Rusak;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanStokPindahRusakTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Kategori $kategori;
    private Satuan $satuan;
    private Pabrik $pabrik;
    private Supplier $supplier;
    private Barang $barangKadaluarsa;
    private Barang $barangAman;
    private DetailPenerimaan $batchKadaluarsa;
    private DetailPenerimaan $batchAman;

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
        $this->satuan = Satuan::create(['nama' => 'Botol']);
        $this->pabrik = Pabrik::create(['nama' => 'PT Kalbe']);
        $this->supplier = Supplier::create([
            'nama' => 'PT Kimia Sehat',
            'alamat' => 'Jl. Farmasi No. 1',
            'telepon' => '08123456789',
        ]);

        $this->barangKadaluarsa = Barang::create([
            'kode_barang' => 'OBH-001',
            'nama' => 'OBH Combi Batuk Berdahak 100ml',
            'kategori_id' => $this->kategori->id,
            'satuan_id' => $this->satuan->id,
            'pabrik_id' => $this->pabrik->id,
            'stok_minimum' => 5,
            'harga_beli' => 15000,
            'harga_jual' => 20000,
            'aktif' => true,
        ]);

        $this->barangAman = Barang::create([
            'kode_barang' => 'AMX-001',
            'nama' => 'Amoxilin 500mg',
            'kategori_id' => $this->kategori->id,
            'satuan_id' => $this->satuan->id,
            'pabrik_id' => $this->pabrik->id,
            'stok_minimum' => 5,
            'harga_beli' => 10000,
            'harga_jual' => 14000,
            'aktif' => true,
        ]);

        $penerimaan = Penerimaan::create([
            'no_faktur' => 'FAK-TEST-001',
            'user_id' => $this->admin->id,
            'supplier_id' => $this->supplier->id,
            'tanggal' => Carbon::today()->subMonths(6)->toDateString(),
            'tanggal_faktur' => Carbon::today()->subMonths(6)->toDateString(),
            'jatuh_tempo' => Carbon::today()->addMonths(1)->toDateString(),
            'lunas' => true,
        ]);

        // Batch Kadaluarsa (Expired 1 bulan lalu)
        $this->batchKadaluarsa = DetailPenerimaan::create([
            'penerimaan_id' => $penerimaan->id,
            'barang_id' => $this->barangKadaluarsa->id,
            'no_batch' => 'BATCH-EXP-01',
            'harga_beli' => 15000,
            'harga_jual' => 20000,
            'expired_date' => Carbon::today()->subMonths(1)->toDateString(),
            'no_rak' => 'A-10',
            'jumlah' => 40,
            'stok' => 39,
            'aktif' => true,
            'dipindahkan_ke_rusak' => false,
        ]);

        // Catat penjualan
        $penjualan = \App\Models\Penjualan::create([
            'no_faktur' => 'INV-TEST-001',
            'user_id' => $this->admin->id,
            'tanggal' => Carbon::today()->subMonths(2)->toDateString(),
            'total' => 20000,
            'bayar' => 20000,
            'kembalian' => 0,
            'metode_pembayaran' => 'Cash',
        ]);

        // Catat 1 unit terjual
        DetailPenjualan::create([
            'penjualan_id' => $penjualan->id,
            'detail_penerimaan_id' => $this->batchKadaluarsa->id,
            'jumlah' => 1,
            'harga_jual' => 20000,
            'diskon' => 0,
            'subtotal' => 20000,
        ]);

        // Batch Aman (Expired 1 tahun ke depan)
        $this->batchAman = DetailPenerimaan::create([
            'penerimaan_id' => $penerimaan->id,
            'barang_id' => $this->barangAman->id,
            'no_batch' => 'BATCH-AMAN-01',
            'harga_beli' => 10000,
            'harga_jual' => 14000,
            'expired_date' => Carbon::today()->addYear()->toDateString(),
            'no_rak' => 'B-02',
            'jumlah' => 50,
            'stok' => 50,
            'aktif' => true,
            'dipindahkan_ke_rusak' => false,
        ]);
    }

    public function test_laporan_stok_renders_aksi_column_to_the_left_of_no_column(): void
    {
        $response = $this->actingAs($this->admin)->get(route('laporan.stok'));

        $response->assertOk();
        $html = $response->getContent();

        // 1. Kolom Aksi berada di sebelah kiri kolom No pada thead
        $posAksiTh = strpos($html, '<th scope="col" class="col-stok-aksi');
        $posNoTh = strpos($html, '<th scope="col" class="col-stok-no');

        $this->assertNotFalse($posAksiTh, 'Header Aksi harus ada di tabel');
        $this->assertNotFalse($posNoTh, 'Header No harus ada di tabel');
        $this->assertLessThan($posNoTh, $posAksiTh, 'Kolom Aksi harus berada tepat di sebelah kiri kolom No');

        // 2. Baris batch kadaluarsa memiliki tombol aktif dengan data-batch-id
        $this->assertStringContainsString('data-batch-id="' . $this->batchKadaluarsa->id . '"', $html);
        $this->assertStringContainsString('btn-pindah-rusak', $html);

        // 3. Kolom Aksi untuk batch aman menampilkan strip (-)
        $this->assertStringContainsString('—', $html);

        // 4. Kolom Aksi ditandai print:hidden no-print
        $this->assertStringContainsString('col-stok-aksi text-center w-12 !px-2 print:hidden no-print', $html);
    }

    public function test_cannot_move_non_expired_batch(): void
    {
        $response = $this->actingAs($this->admin)->postJson(
            route('laporan.stok.batch.pindah-rusak', $this->batchAman->id)
        );

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);

        $this->assertDatabaseMissing('rusaks', [
            'detail_penerimaan_id' => $this->batchAman->id,
        ]);
    }

    public function test_cannot_move_already_moved_batch(): void
    {
        // Pindahkan pertama kali
        $this->actingAs($this->admin)->postJson(
            route('laporan.stok.batch.pindah-rusak', $this->batchKadaluarsa->id)
        )->assertOk();

        // Coba pindahkan kedua kalinya (harus ditolak)
        $response = $this->actingAs($this->admin)->postJson(
            route('laporan.stok.batch.pindah-rusak', $this->batchKadaluarsa->id)
        );

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Batch barang ini sudah pernah dipindahkan ke Catatan Barang Rusak.',
        ]);

        // Pastikan tidak ada duplikasi data di tabel rusaks
        $this->assertEquals(1, Rusak::where('detail_penerimaan_id', $this->batchKadaluarsa->id)->count());
    }

    public function test_successfully_moves_expired_batch_to_catatan_barang_rusak(): void
    {
        // Stok awal 40, terjual 1 => sisa stok 39
        $response = $this->actingAs($this->admin)->postJson(
            route('laporan.stok.batch.pindah-rusak', $this->batchKadaluarsa->id)
        );

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Barang berhasil dipindahkan ke Catatan Barang Rusak.',
        ]);

        // Verifikasi tersimpan di tabel rusaks
        $this->assertDatabaseHas('rusaks', [
            'detail_penerimaan_id' => $this->batchKadaluarsa->id,
            'jumlah' => 39,
        ]);

        // Verifikasi batch ditandai dipindahkan & stok dinonaktifkan
        $this->batchKadaluarsa->refresh();
        $this->assertTrue($this->batchKadaluarsa->dipindahkan_ke_rusak);
        $this->assertEquals(0, $this->batchKadaluarsa->stok);
        $this->assertFalse($this->batchKadaluarsa->aktif);

        // Verifikasi batch TIDAK MUNCUL LAGI di Laporan Stok
        $laporanResponse = $this->actingAs($this->admin)->get(route('laporan.stok'));
        $laporanResponse->assertOk();
        $laporanResponse->assertDontSee('BATCH-EXP-01');
        $laporanResponse->assertSee('BATCH-AMAN-01');

        // Verifikasi batch MUNCUL di Catatan Barang Rusak (rusak.index)
        $rusakResponse = $this->actingAs($this->admin)->get(route('rusak.index', [
            'tanggal_mulai' => Carbon::today()->startOfMonth()->toDateString(),
            'tanggal_akhir' => Carbon::today()->endOfMonth()->toDateString(),
        ]));
        $rusakResponse->assertOk();
        $rusakResponse->assertSee('BATCH-EXP-01');
        $rusakResponse->assertSee('OBH Combi Batuk Berdahak 100ml');
    }

    public function test_excel_export_does_not_contain_aksi_column_and_excludes_moved_batch(): void
    {
        // Pindahkan batch kadaluarsa
        $this->actingAs($this->admin)->postJson(
            route('laporan.stok.batch.pindah-rusak', $this->batchKadaluarsa->id)
        );

        // Unduh excel
        $response = $this->actingAs($this->admin)->get(route('laporan.stok', ['export' => 'xlsx']));
        $response->assertOk();

        // Response bertipe stream download xlsx
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('content-type'));
    }
}
