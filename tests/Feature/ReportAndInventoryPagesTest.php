<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAndInventoryPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_and_report_pages_render_their_content(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.local',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'aktif' => true,
        ]);

        $pages = [
            'penerimaan.index' => 'Daftar Penerimaan Barang',
            'rusak.index' => 'Daftar Pencatatan Barang Rusak',
            'laporan.penerimaan' => 'Laporan Penerimaan Barang',
            'laporan.rusak' => 'Laporan Barang Rusak',
            'laporan.penjualan' => 'Laporan Penjualan',
            'laporan.stok' => 'Laporan Ketersediaan Stok dan Kadaluarsa',
        ];

        foreach ($pages as $route => $heading) {
            $response = $this->actingAs($admin)->get(route($route));

            $response->assertOk();
            $response->assertSee($heading);
        }
    }
}
