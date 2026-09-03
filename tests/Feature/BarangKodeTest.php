<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Pabrik;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BarangKodeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Kategori $kategori;
    private Satuan $satuan;
    private Pabrik $pabrik;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'aktif' => true,
        ]);

        $this->kategori = Kategori::create(['nama' => 'Analgesik']);
        $this->satuan = Satuan::create(['nama' => 'Strip']);
        $this->pabrik = Pabrik::create(['nama' => 'Kimia Farma']);
    }

    public function test_auto_generate_kode_apotek_format_and_sequence()
    {
        $code1 = Barang::generateKodeApotek('Paracetamol 500mg');
        $code2 = Barang::generateKodeApotek('Amoxicillin 500mg');
        $code3 = Barang::generateKodeApotek('5-FU 500mg');
        $code4 = Barang::generateKodeApotek('123 ABC');
        $code5 = Barang::generateKodeApotek('@@@ Panadol');

        $this->assertEquals('P-0001', $code1);
        $this->assertEquals('A-0002', $code2);
        $this->assertEquals('F-0003', $code3);
        $this->assertEquals('A-0004', $code4);
        $this->assertEquals('P-0005', $code5);
    }

    public function test_sequence_is_not_reused_after_deletion()
    {
        $code1 = Barang::generateKodeApotek('Paracetamol');
        $this->assertEquals('P-0001', $code1);

        $b1 = Barang::create([
            'kode_apotek' => $code1,
            'nama' => 'Paracetamol',
            'kategori_id' => $this->kategori->id,
            'satuan_id' => $this->satuan->id,
            'pabrik_id' => $this->pabrik->id,
            'stok_minimum' => 0,
        ]);

        // Delete barang
        $b1->delete();

        // Next code must NOT reuse 0001
        $code2 = Barang::generateKodeApotek('Amoxicillin');
        $this->assertEquals('A-0002', $code2);
    }

    public function test_create_barang_and_immutable_kode_apotek_on_update()
    {
        $response = $this->actingAs($this->admin)->post(route('barang.store'), [
            'nama' => 'Paracetamol 500mg',
            'kode_kfa' => 'KFA-001',
            'merk' => 'Kimia Farma',
            'kategori_id' => $this->kategori->id,
            'satuan_id' => $this->satuan->id,
            'pabrik_id' => $this->pabrik->id,
            'stok_minimum' => 5,
        ]);

        $response->assertRedirect(route('barang.index'));

        $barang = Barang::where('nama', 'Paracetamol 500mg')->first();
        $this->assertNotNull($barang);
        $this->assertEquals('KFA-001', $barang->kode_kfa);
        $this->assertEquals('Kimia Farma', $barang->merk);
        $this->assertNotNull($barang->kode_apotek);
        $this->assertEquals('P-0001', $barang->kode_apotek);

        $initialKodeApotek = $barang->kode_apotek;

        // Update name
        $updateResponse = $this->actingAs($this->admin)->put(route('barang.update', $barang), [
            'nama' => 'Paracetamol 650mg Forte',
            'kode_kfa' => 'KFA-999',
            'merk' => 'Kimia Farma Baru',
            'kategori_id' => $this->kategori->id,
            'satuan_id' => $this->satuan->id,
            'pabrik_id' => $this->pabrik->id,
            'stok_minimum' => 10,
        ]);

        $updateResponse->assertRedirect(route('barang.index'));

        $barang->refresh();
        $this->assertEquals('Paracetamol 650mg Forte', $barang->nama);
        $this->assertEquals('KFA-999', $barang->kode_kfa);
        $this->assertEquals('Kimia Farma Baru', $barang->merk);
        // Kode Apotek MUST remain identical
        $this->assertEquals($initialKodeApotek, $barang->kode_apotek);
    }

    public function test_detail_barang_route_and_json_response()
    {
        $barang = Barang::create([
            'kode_apotek' => 'P-0001',
            'kode_kfa' => 'KFA-100',
            'nama' => 'Paracetamol 500mg',
            'merk' => 'Kimia Farma',
            'kategori_id' => $this->kategori->id,
            'satuan_id' => $this->satuan->id,
            'pabrik_id' => $this->pabrik->id,
            'barcode' => '8991234567890',
            'stok_minimum' => 10,
            'butuh_resep' => false,
            'aktif' => true,
        ]);

        // Test HTML detail page
        $responseHtml = $this->actingAs($this->admin)->get(route('barang.show', $barang));
        $responseHtml->assertOk();
        $responseHtml->assertSee('P-0001');
        $responseHtml->assertSee('KFA-100');
        $responseHtml->assertSee('Paracetamol 500mg');
        $responseHtml->assertSee('Kimia Farma');

        // Test JSON detail endpoint
        $responseJson = $this->actingAs($this->admin)->getJson(route('barang.show', $barang));
        $responseJson->assertOk();
        $responseJson->assertJson([
            'kode_apotek' => 'P-0001',
            'kode_kfa' => 'KFA-100',
            'nama' => 'Paracetamol 500mg',
            'merk' => 'Kimia Farma',
            'kategori' => 'Analgesik',
            'satuan' => 'Strip',
            'pabrik' => 'Kimia Farma',
            'barcode' => '8991234567890',
        ]);
    }

    public function test_search_barang_by_name_kode_kfa_and_merk()
    {
        Barang::create([
            'kode_apotek' => 'A-0001',
            'kode_kfa' => 'KFA-AMOX',
            'nama' => 'Amoxicillin 500mg',
            'merk' => 'Kalbe',
            'kategori_id' => $this->kategori->id,
            'satuan_id' => $this->satuan->id,
            'pabrik_id' => $this->pabrik->id,
            'stok_minimum' => 5,
        ]);

        // Search by merk
        $resMerk = $this->actingAs($this->admin)->get(route('barang.index', ['cari' => 'Kalbe']));
        $resMerk->assertOk();
        $resMerk->assertSee('Amoxicillin 500mg');

        // Search by kode apotek
        $resKode = $this->actingAs($this->admin)->get(route('barang.index', ['cari' => 'A-0001']));
        $resKode->assertOk();
        $resKode->assertSee('Amoxicillin 500mg');

        // Search by KFA
        $resKfa = $this->actingAs($this->admin)->get(route('barang.index', ['cari' => 'KFA-AMOX']));
        $resKfa->assertOk();
        $resKfa->assertSee('Amoxicillin 500mg');
    }

    public function test_import_and_export_barang()
    {
        $csvContent = "nama,kode_kfa,merk,kategori,satuan,pabrik,barcode,stok_minimum,butuh_resep\n" .
                      "Cetirizine 10mg,KFA-777,Sanbe,Antihistamin,Tablet,Sanbe Farma,8991112223334,10,0\n";

        $file = UploadedFile::fake()->createWithContent('test_import.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('barang.import'), [
            'file' => $file,
            'auto_create_master' => 1,
        ]);

        $response->assertRedirect(route('barang.index'));

        $barang = Barang::where('nama', 'Cetirizine 10mg')->first();
        $this->assertNotNull($barang);
        $this->assertEquals('KFA-777', $barang->kode_kfa);
        $this->assertEquals('Sanbe', $barang->merk);
        $this->assertEquals('C-0001', $barang->kode_apotek);

        // Export test
        $exportRes = $this->actingAs($this->admin)->get(route('barang.export'));
        $exportRes->assertOk();
        $exportRes->assertHeader('Content-Disposition');
    }
}
