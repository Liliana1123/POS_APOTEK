<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\Pabrik;
use App\Models\User;
use App\Models\Penerimaan;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LaporanStokTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_stok_csv_requires_admin_role(): void
    {
        // Guest
        $response = $this->get(route('laporan.stok', ['export' => 'csv']));
        $response->assertRedirect(route('login'));

        // Non-admin user (e.g. kasir)
        $kasir = User::create([
            'name' => 'Kasir',
            'email' => 'kasir@apotek.test',
            'password' => Hash::make('password'),
            'role' => 'kasir',
            'aktif' => true,
        ]);
        
        $response = $this->actingAs($kasir)->get(route('laporan.stok', ['export' => 'csv']));
        $response->assertStatus(403);
    }

    public function test_export_stok_csv_returns_correct_data(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@apotek.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'aktif' => true,
        ]);

        $kategori = Kategori::create(['nama' => 'Analgesik']);
        $satuan = Satuan::create(['nama' => 'Tablet']);
        $pabrik = Pabrik::create(['nama' => 'Bio Farma']);
        $supplier = Supplier::create(['nama' => 'Kimia Farma', 'telepon' => '081', 'alamat' => 'Bandung']);

        // Barang 1: Stok aman
        $barang1 = Barang::create([
            'nama' => 'Paracetamol 500mg',
            'kategori_id' => $kategori->id,
            'satuan_id' => $satuan->id,
            'pabrik_id' => $pabrik->id,
            'barcode' => '11111',
            'butuh_resep' => false,
            'stok_minimum' => 10,
            'aktif' => true,
        ]);

        // Barang 2: Stok menipis
        $barang2 = Barang::create([
            'nama' => 'Amoxicillin 500mg',
            'kategori_id' => $kategori->id,
            'satuan_id' => $satuan->id,
            'pabrik_id' => $pabrik->id,
            'barcode' => '22222',
            'butuh_resep' => true,
            'stok_minimum' => 50,
            'aktif' => true,
        ]);

        // Add some batch stok via Penerimaan
        $penerimaan = Penerimaan::create([
            'no_faktur' => 'FAK-1234',
            'tanggal' => now(),
            'supplier_id' => $supplier->id,
            'user_id' => $admin->id,
            'lunas' => true,
        ]);

        // Batch for Barang 1 (stok: 30 > 10, Aman)
        $penerimaan->detail()->create([
            'barang_id' => $barang1->id,
            'no_batch' => 'BATCH-001',
            'harga_beli' => 1000,
            'harga_jual' => 1500,
            'expired_date' => now()->addDays(120),
            'jumlah' => 30,
            'stok' => 30,
            'aktif' => true,
        ]);

        // Batch for Barang 2 (stok: 20 <= 50, Menipis)
        $penerimaan->detail()->create([
            'barang_id' => $barang2->id,
            'no_batch' => 'BATCH-002',
            'harga_beli' => 2000,
            'harga_jual' => 2500,
            'expired_date' => now()->addDays(60),
            'jumlah' => 20,
            'stok' => 20,
            'aktif' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('laporan.stok', ['export' => 'csv']));
        $response->assertStatus(200);
        $response->assertHeader('Content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        
        // Assert headers and rows exist (fputcsv quotes fields with spaces)
        $this->assertStringContainsString('"Nama Barang",Kategori,"Stok Saat Ini","Stok Minimum",Status', $content);
        $this->assertStringContainsString('"Paracetamol 500mg",Analgesik,30,10,Aman', $content);
        $this->assertStringContainsString('"Amoxicillin 500mg",Analgesik,20,50,Menipis', $content);
    }
}
