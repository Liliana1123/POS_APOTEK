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
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PenerimaanImportObatTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

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
    }

    public function test_penerimaan_index_modal_renders_import_excel_button_and_modal(): void
    {
        $response = $this->actingAs($this->admin)->get(route('penerimaan.index'));

        $response->assertOk();
        $content = $response->getContent();

        // 1. Cek tombol Import Excel di modal tambah
        $this->assertStringContainsString('id="btn-import-excel-tambah"', $content);
        $this->assertStringContainsString('Import Excel', $content);

        // 2. Cek urutan tombol: [Import Excel] di sebelah kiri [Tambah Item Barang]
        $posImportBtn = strpos($content, 'id="btn-import-excel-tambah"');
        $posTambahBtn = strpos($content, 'id="btn-tambah-item-tambah"');
        $this->assertNotFalse($posImportBtn);
        $this->assertNotFalse($posTambahBtn);
        $this->assertTrue($posImportBtn < $posTambahBtn, 'Tombol Import Excel harus berada di sebelah kiri tombol Tambah Item Barang');

        // 3. Cek keberadaan Modal Import Obat
        $this->assertStringContainsString('id="modal-import-obat"', $content);
        $this->assertStringContainsString('id="dropzone-import-obat"', $content);
        $this->assertStringContainsString('id="import-preview-rows"', $content);
    }

    public function test_penerimaan_import_template_download(): void
    {
        $response = $this->actingAs($this->admin)->get(route('penerimaan.import-template'));

        $response->assertOk();
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class, $response->baseResponse);
        $contentDisposition = $response->headers->get('content-disposition');
        $this->assertStringContainsString('template-import-penerimaan-obat.xlsx', $contentDisposition);

        // Ambil isi binary output streamed response
        ob_start();
        $response->sendContent();
        $binaryContent = ob_get_clean();

        $tempPath = tempnam(sys_get_temp_dir(), 'test_tpl_') . '.xlsx';
        file_put_contents($tempPath, $binaryContent);

        // 1. Baca dengan OpenSpout Reader untuk memastikan struktur baris
        $reader = new \OpenSpout\Reader\XLSX\Reader();
        $reader->open($tempPath);

        $rows = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = array_map(fn ($c) => $c->getValue(), $row->getCells());
            }
        }
        $reader->close();

        // 2. Pastikan file HANYA memiliki 1 baris (Header saja, tanpa baris data dummy)
        $this->assertCount(1, $rows, 'Template Excel yang diunduh harus benar-benar kosong selain baris header (hanya 1 baris).');

        // 3. Pastikan 9 kolom header sesuai dengan ketentuan
        $expectedHeaders = [
            'Nama Barang',
            'Barcode',
            'Nomor Batch',
            'Expired Date',
            'Harga Beli',
            'Harga Jual',
            'Jumlah Dipesan',
            'Jumlah Diterima',
            'Nomor Rak',
        ];
        $this->assertEquals($expectedHeaders, $rows[0]);

        // 4. Pastikan tidak ada data dummy obat (Paracetamol, Amoxicillin, BATCH-001, dsb.)
        $this->assertStringNotContainsString('Paracetamol', $binaryContent);
        $this->assertStringNotContainsString('Amoxicillin', $binaryContent);
        $this->assertStringNotContainsString('Vitamin C', $binaryContent);
        $this->assertStringNotContainsString('BATCH-001', $binaryContent);

        // 5. Periksa struktur OpenXML di dalam file zip: lebar kolom dan style format
        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($tempPath));
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $stylesXml = $zip->getFromName('xl/styles.xml');
        $zip->close();

        $this->assertNotEmpty($sheetXml);
        $this->assertNotEmpty($stylesXml);

        // Lebar kolom custom
        $this->assertStringContainsString('width="28"', $sheetXml); // Nama Barang
        $this->assertStringContainsString('width="18"', $sheetXml); // Barcode & Batch
        $this->assertStringContainsString('width="16"', $sheetXml); // Expired, Harga, Qty
        $this->assertStringContainsString('width="14"', $sheetXml); // Nomor Rak

        // Format tanggal yyyy-mm-dd
        $this->assertStringContainsString('yyyy-mm-dd', $stylesXml);

        @unlink($tempPath);
    }

    public function test_penerimaan_import_parse_csv_file(): void
    {
        $csvContent = "Nama Obat,No Batch,Expired Date,Harga Beli,Harga Jual,Dipesan,Diterima,Rak\n" .
                      "Paracetamol 500mg,BATCH-001,2027-12-31,10000,12000,10,10,A-01\n" .
                      "Amoxicillin 500mg,BATCH-002,2028-06-30,20000,24000,5,5,B-02\n";

        $file = UploadedFile::fake()->createWithContent('faktur-supplier.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('penerimaan.import-parse'), [
            'file' => $file,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'total_rows' => 2,
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['file_cache_id']);
        $this->assertContains('Nama Obat', $data['headers']);
        $this->assertContains('No Batch', $data['headers']);
        $this->assertArrayHasKey('suggested_mapping', $data);
    }

    public function test_penerimaan_import_validate_and_matching_master_barang(): void
    {
        $kategori = Kategori::create(['nama' => 'Obat Bebas']);
        $satuan = Satuan::create(['nama' => 'Strip']);
        $pabrik = Pabrik::create(['nama' => 'Kimia Farma']);

        $barang1 = Barang::create([
            'nama' => 'Paracetamol 500mg',
            'barcode' => '8991234567890',
            'kategori_id' => $kategori->id,
            'satuan_id' => $satuan->id,
            'pabrik_id' => $pabrik->id,
            'harga_beli' => 10000,
            'harga_jual' => 12000,
            'stok' => 50,
            'stok_minimum' => 5,
            'aktif' => true,
        ]);

        $barang2 = Barang::create([
            'nama' => 'Amoxicillin 500mg',
            'barcode' => '8999876543210',
            'kategori_id' => $kategori->id,
            'satuan_id' => $satuan->id,
            'pabrik_id' => $pabrik->id,
            'harga_beli' => 20000,
            'harga_jual' => 25000,
            'stok' => 20,
            'stok_minimum' => 5,
            'aktif' => true,
        ]);

        // 1. Upload dan parse CSV
        $csvContent = "Barcode,Nama Barang,No Batch,Expired Date,Harga Beli,Harga Jual,Jumlah Dipesan,Jumlah Diterima,No Rak\n" .
                      "8991234567890,Paracetamol 500mg,BATCH-001,2027-12-31,10000,12000,10,10,A-01\n" .
                      ",Amoxicillin 500mg,BATCH-002,2028-06-30,20000,,5,5,B-02\n"; // Harga jual kosong -> status warning/perlu dilengkapi

        $file = UploadedFile::fake()->createWithContent('faktur.csv', $csvContent);

        $parseResponse = $this->actingAs($this->admin)->post(route('penerimaan.import-parse'), [
            'file' => $file,
        ]);
        $parseResponse->assertOk();
        $cacheId = $parseResponse->json('file_cache_id');

        // 2. Validasi dengan mapping
        $mapping = [
            'barcode' => 'Barcode',
            'nama_barang' => 'Nama Barang',
            'no_batch' => 'No Batch',
            'expired_date' => 'Expired Date',
            'harga_beli' => 'Harga Beli',
            'harga_jual' => 'Harga Jual',
            'jumlah_dipesan' => 'Jumlah Dipesan',
            'jumlah_diterima' => 'Jumlah Diterima',
            'no_rak' => 'No Rak',
        ];

        $validateResponse = $this->actingAs($this->admin)->postJson(route('penerimaan.import-validate'), [
            'file_cache_id' => $cacheId,
            'mapping' => $mapping,
            'tanggal_penerimaan' => '2026-11-03',
        ]);

        $validateResponse->assertOk();
        $valData = $validateResponse->json();

        $this->assertTrue($valData['success']);
        $this->assertEquals(2, $valData['summary']['total_rows']);
        $this->assertEquals(1, $valData['summary']['valid_count']);
        $this->assertEquals(1, $valData['summary']['warning_count']);
        $this->assertEquals(0, $valData['summary']['invalid_count']);

        // Baris 1: Cocok dengan Paracetamol lewat barcode
        $this->assertEquals($barang1->id, $valData['rows'][0]['barang_id']);
        $this->assertEquals('valid', $valData['rows'][0]['status']);

        // Baris 2: Cocok dengan Amoxicillin lewat nama, warning harga jual kosong
        $this->assertEquals($barang2->id, $valData['rows'][1]['barang_id']);
        $this->assertEquals('warning', $valData['rows'][1]['status']);
    }

    public function test_penerimaan_import_with_revised_template_auto_mapping(): void
    {
        $kategori = Kategori::create(['nama' => 'Antibiotik']);
        $satuan = Satuan::create(['nama' => 'Tablet']);
        $pabrik = Pabrik::create(['nama' => 'Sanbe']);

        $amox = Barang::create([
            'nama' => 'Amoxicillin 500mg',
            'barcode' => '8999876543210',
            'kategori_id' => $kategori->id,
            'satuan_id' => $satuan->id,
            'pabrik_id' => $pabrik->id,
            'harga_beli' => 20000,
            'harga_jual' => 25000,
            'stok' => 10,
            'stok_minimum' => 5,
            'aktif' => true,
        ]);

        // Simulasikan file template yang baru diisi dari baris 2
        $csvContent = "Nama Barang,Barcode,Nomor Batch,Expired Date,Harga Beli,Harga Jual,Jumlah Dipesan,Jumlah Diterima,Nomor Rak\n" .
                      "Amoxicillin 500mg,8999876543210,AMX-REV-01,2028-12-31,20000,25000,10,10,RAK-01\n";

        $file = UploadedFile::fake()->createWithContent('template-filled.csv', $csvContent);

        $parseResponse = $this->actingAs($this->admin)->post(route('penerimaan.import-parse'), [
            'file' => $file,
        ]);
        $parseResponse->assertOk();

        $suggested = $parseResponse->json('suggested_mapping');
        // Pastikan nama kolom baru otomatis terpetakan tanpa perlu mapping manual
        $this->assertEquals('Nama Barang', $suggested['nama_barang']);
        $this->assertEquals('Barcode', $suggested['barcode']);
        $this->assertEquals('Nomor Batch', $suggested['no_batch']);
        $this->assertEquals('Expired Date', $suggested['expired_date']);
        $this->assertEquals('Harga Beli', $suggested['harga_beli']);
        $this->assertEquals('Harga Jual', $suggested['harga_jual']);
        $this->assertEquals('Jumlah Dipesan', $suggested['jumlah_dipesan']);
        $this->assertEquals('Jumlah Diterima', $suggested['jumlah_diterima']);
        $this->assertEquals('Nomor Rak', $suggested['no_rak']);

        $validateResponse = $this->actingAs($this->admin)->postJson(route('penerimaan.import-validate'), [
            'file_cache_id' => $parseResponse->json('file_cache_id'),
            'mapping' => $suggested,
        ]);

        $validateResponse->assertOk();
        $this->assertEquals(1, $validateResponse->json('summary.valid_count'));
        $this->assertEquals('valid', $validateResponse->json('rows.0.status'));
        $this->assertEquals($amox->id, $validateResponse->json('rows.0.barang_id'));
    }

    public function test_penerimaan_import_validate_detects_unmatched_and_invalid_data(): void
    {
        $csvContent = "Nama Barang,No Batch,Expired Date,Harga Beli,Harga Jual,Jumlah Dipesan,Jumlah Diterima\n" .
                      "Obat Antah Berantah XYZ,BATCH-999,2027-12-31,10000,12000,10,10\n" . // Tidak ada di master
                      "Paracetamol,,2027-12-31,10000,12000,10,10\n"; // Batch kosong

        $file = UploadedFile::fake()->createWithContent('faktur-invalid.csv', $csvContent);

        $parseResponse = $this->actingAs($this->admin)->post(route('penerimaan.import-parse'), [
            'file' => $file,
        ]);
        $parseResponse->assertOk();
        $cacheId = $parseResponse->json('file_cache_id');

        $mapping = [
            'nama_barang' => 'Nama Barang',
            'no_batch' => 'No Batch',
            'expired_date' => 'Expired Date',
            'harga_beli' => 'Harga Beli',
            'harga_jual' => 'Harga Jual',
            'jumlah_dipesan' => 'Jumlah Dipesan',
            'jumlah_diterima' => 'Jumlah Diterima',
        ];

        $validateResponse = $this->actingAs($this->admin)->postJson(route('penerimaan.import-validate'), [
            'file_cache_id' => $cacheId,
            'mapping' => $mapping,
        ]);

        $validateResponse->assertOk();
        $valData = $validateResponse->json();

        $this->assertEquals(2, $valData['summary']['invalid_count']);
        $this->assertNotEmpty($valData['rows'][0]['errors']);
        $this->assertStringContainsString('tidak ditemukan di Data Master', $valData['rows'][0]['errors'][0]);
        $this->assertNotEmpty($valData['rows'][1]['errors']);
    }

    public function test_penerimaan_store_saves_1_manual_item(): void
    {
        $kategori = Kategori::create(['nama' => 'Antibiotik']);
        $satuan = Satuan::create(['nama' => 'Strip']);
        $pabrik = Pabrik::create(['nama' => 'Kalbe']);
        $supplier = Supplier::create(['nama' => 'PT Farmasi Utama', 'telepon' => '08123456789']);

        $barang = Barang::create([
            'nama' => 'Amoxilin 500mg',
            'barcode' => '899123400001',
            'kategori_id' => $kategori->id,
            'satuan_id' => $satuan->id,
            'pabrik_id' => $pabrik->id,
            'harga_beli' => 10000,
            'harga_jual' => 15000,
            'stok' => 0,
            'stok_minimum' => 5,
            'aktif' => true,
        ]);

        $payload = [
            'supplier_id' => $supplier->id,
            'tanggal' => '2026-11-03',
            'tanggal_faktur' => '2026-11-03',
            'no_faktur' => 'FAK-001',
            'keterangan' => 'Penerimaan 1 item manual',
            'pembayaran_pertama' => 0,
            'jatuh_tempo' => '2026-12-03',
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'no_batch' => 'BATCH-MANUAL-1',
                    'expired_date' => '2027-11-03',
                    'harga_beli' => 10000,
                    'harga_jual' => 15000,
                    'no_rak' => 'A-01',
                    'jumlah_dipesan' => 10,
                    'jumlah_diterima' => 10,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->postJson(route('penerimaan.store'), $payload);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('penerimaans', ['no_faktur' => 'FAK-001']);
        $this->assertDatabaseCount('detail_penerimaans', 1);
        $this->assertDatabaseCount('riwayat_penerimaans', 1);
    }

    public function test_penerimaan_store_saves_3_batches(): void
    {
        $kategori = Kategori::create(['nama' => 'Antibiotik']);
        $satuan = Satuan::create(['nama' => 'Strip']);
        $pabrik = Pabrik::create(['nama' => 'Kalbe']);
        $supplier = Supplier::create(['nama' => 'PT Farmasi Utama', 'telepon' => '08123456789']);

        $barang = Barang::create([
            'nama' => 'Amoxilin 500mg',
            'barcode' => '899123400002',
            'kategori_id' => $kategori->id,
            'satuan_id' => $satuan->id,
            'pabrik_id' => $pabrik->id,
            'harga_beli' => 10000,
            'harga_jual' => 15000,
            'stok' => 0,
            'stok_minimum' => 5,
            'aktif' => true,
        ]);

        $payload = [
            'supplier_id' => $supplier->id,
            'tanggal' => '2026-11-03',
            'tanggal_faktur' => '2026-11-03',
            'no_faktur' => 'FAK-003',
            'keterangan' => 'Penerimaan 3 batch',
            'pembayaran_pertama' => 0,
            'jatuh_tempo' => '2026-12-03',
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'no_batch' => 'BATCH-3-A',
                    'expired_date' => '2027-11-03',
                    'harga_beli' => 10000,
                    'harga_jual' => 15000,
                    'no_rak' => 'A-01',
                    'jumlah_dipesan' => 5,
                    'jumlah_diterima' => 5,
                ],
                [
                    'barang_id' => $barang->id,
                    'no_batch' => 'BATCH-3-B',
                    'expired_date' => '2027-12-03',
                    'harga_beli' => 10000,
                    'harga_jual' => 15000,
                    'no_rak' => 'A-02',
                    'jumlah_dipesan' => 10,
                    'jumlah_diterima' => 10,
                ],
                [
                    'barang_id' => $barang->id,
                    'no_batch' => 'BATCH-3-C',
                    'expired_date' => '2028-01-03',
                    'harga_beli' => 10000,
                    'harga_jual' => 15000,
                    'no_rak' => 'A-03',
                    'jumlah_dipesan' => 15,
                    'jumlah_diterima' => 15,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->postJson(route('penerimaan.store'), $payload);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $penerimaan = Penerimaan::where('no_faktur', 'FAK-003')->firstOrFail();
        $this->assertEquals(3, DetailPenerimaan::where('penerimaan_id', $penerimaan->id)->count());
        $this->assertEquals(3, RiwayatPenerimaan::where('penerimaan_id', $penerimaan->id)->count());

        // Total belanja: (5*10000) + (10*10000) + (15*10000) = 300,000
        // PPN (11%): 33,000
        // Total tagihan: 333,000
        $this->assertEquals(33000, (float) $penerimaan->ppn);
        $this->assertEquals(333000, (float) $penerimaan->totalTagihan());
    }

    public function test_penerimaan_store_saves_100_batches(): void
    {
        $kategori = Kategori::create(['nama' => 'Antibiotik']);
        $satuan = Satuan::create(['nama' => 'Strip']);
        $pabrik = Pabrik::create(['nama' => 'Kalbe']);
        $supplier = Supplier::create(['nama' => 'PT Farmasi Utama', 'telepon' => '08123456789']);

        $barang = Barang::create([
            'nama' => 'Amoxilin 500mg',
            'barcode' => '899123400100',
            'kategori_id' => $kategori->id,
            'satuan_id' => $satuan->id,
            'pabrik_id' => $pabrik->id,
            'harga_beli' => 10000,
            'harga_jual' => 15000,
            'stok' => 0,
            'stok_minimum' => 5,
            'aktif' => true,
        ]);

        $items = [];
        for ($i = 1; $i <= 100; $i++) {
            $items[] = [
                'barang_id' => $barang->id,
                'no_batch' => sprintf('BATCH-100-%03d', $i),
                'expired_date' => '2028-01-01',
                'harga_beli' => 10000,
                'harga_jual' => 15000,
                'no_rak' => 'A-01',
                'jumlah_dipesan' => 5,
                'jumlah_diterima' => 5,
            ];
        }

        $payload = [
            'supplier_id' => $supplier->id,
            'tanggal' => '2026-11-03',
            'tanggal_faktur' => '2026-11-03',
            'no_faktur' => 'FAK-100',
            'keterangan' => 'Penerimaan 100 batch',
            'pembayaran_pertama' => 0,
            'jatuh_tempo' => '2026-12-03',
            'items' => $items,
        ];

        $response = $this->actingAs($this->admin)->postJson(route('penerimaan.store'), $payload);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $penerimaan = Penerimaan::where('no_faktur', 'FAK-100')->firstOrFail();
        $this->assertEquals(100, DetailPenerimaan::where('penerimaan_id', $penerimaan->id)->count());
        $this->assertEquals(100, RiwayatPenerimaan::where('penerimaan_id', $penerimaan->id)->count());
    }

    public function test_penerimaan_store_saves_1000_batches_amoxilin_via_json(): void
    {
        $kategori = Kategori::create(['nama' => 'Antibiotik']);
        $satuan = Satuan::create(['nama' => 'Strip']);
        $pabrik = Pabrik::create(['nama' => 'Kalbe']);
        $supplier = Supplier::create(['nama' => 'PT Farmasi Besar Indonesia', 'telepon' => '08123456789']);

        $amoxilin = Barang::create([
            'nama' => 'Amoxilin 500mg',
            'barcode' => '899123401000',
            'kategori_id' => $kategori->id,
            'satuan_id' => $satuan->id,
            'pabrik_id' => $pabrik->id,
            'harga_beli' => 12000,
            'harga_jual' => 16000,
            'stok' => 0,
            'stok_minimum' => 50,
            'aktif' => true,
        ]);

        $items = [];
        for ($i = 1; $i <= 1000; $i++) {
            $items[] = [
                'barang_id' => $amoxilin->id,
                'no_batch' => sprintf('AMX-BATCH-%04d', $i),
                'expired_date' => '2028-12-31',
                'harga_beli' => 12000,
                'harga_jual' => 16000,
                'no_rak' => sprintf('RAK-%02d', ($i % 20) + 1),
                'jumlah_dipesan' => 10,
                'jumlah_diterima' => 10,
            ];
        }

        $payload = [
            'supplier_id' => $supplier->id,
            'tanggal' => '2026-11-03',
            'tanggal_faktur' => '2026-11-03',
            'no_faktur' => 'FAK-AMX-1000',
            'keterangan' => 'Import 1000 batch Amoxilin',
            'pembayaran_pertama' => 0,
            'jatuh_tempo' => '2026-12-03',
            'items' => $items,
        ];

        $response = $this->actingAs($this->admin)->postJson(route('penerimaan.store'), $payload);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Penerimaan barang berhasil disimpan.',
        ]);

        $penerimaan = Penerimaan::where('no_faktur', 'FAK-AMX-1000')->firstOrFail();

        // 1. Verifikasi seluruh 1.000 batch tersimpan lengkap di detail penerimaan
        $this->assertEquals(1000, DetailPenerimaan::where('penerimaan_id', $penerimaan->id)->count());

        // 2. Verifikasi 1.000 riwayat penerimaan tercatat
        $this->assertEquals(1000, RiwayatPenerimaan::where('penerimaan_id', $penerimaan->id)->count());

        // 3. Verifikasi perhitungan total:
        // Total belanja = 1000 * (12000 * 10) = 120,000,000
        // PPN (11%) = 13,200,000
        // Total tagihan = 133,200,000
        $this->assertEquals(13200000, (float) $penerimaan->ppn);
        $this->assertEquals(133200000, (float) $penerimaan->totalTagihan());
        $this->assertFalse((bool) $penerimaan->lunas);
    }

    public function test_penerimaan_store_returns_422_json_on_invalid_data(): void
    {
        $kategori = Kategori::create(['nama' => 'Antibiotik']);
        $satuan = Satuan::create(['nama' => 'Strip']);
        $pabrik = Pabrik::create(['nama' => 'Kalbe']);
        $supplier = Supplier::create(['nama' => 'PT Farmasi Utama', 'telepon' => '08123456789']);

        $barang = Barang::create([
            'nama' => 'Amoxilin 500mg',
            'barcode' => '899123400422',
            'kategori_id' => $kategori->id,
            'satuan_id' => $satuan->id,
            'pabrik_id' => $pabrik->id,
            'harga_beli' => 20000,
            'harga_jual' => 25000,
            'stok' => 0,
            'stok_minimum' => 5,
            'aktif' => true,
        ]);

        // Harga jual lebih kecil dari harga beli -> validasi backend harus tolak
        $payload = [
            'supplier_id' => $supplier->id,
            'tanggal' => '2026-11-03',
            'tanggal_faktur' => '2026-11-03',
            'no_faktur' => 'FAK-ERR-422',
            'pembayaran_pertama' => 0,
            'jatuh_tempo' => '2026-12-03',
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'no_batch' => 'BATCH-ERR-1',
                    'expired_date' => '2027-11-03',
                    'harga_beli' => 20000,
                    'harga_jual' => 15000, // Invalid: < harga_beli
                    'no_rak' => 'A-01',
                    'jumlah_dipesan' => 10,
                    'jumlah_diterima' => 10,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->postJson(route('penerimaan.store'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['items.0.harga_jual']);
        $this->assertDatabaseMissing('penerimaans', ['no_faktur' => 'FAK-ERR-422']);
    }
}

