<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_dashboard_renders_executive_layout(): void
    {
        $admin = User::create([
            'name' => 'Admin Apotek',
            'email' => 'admin@apotek.test',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'aktif' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee(route('penjualan.create'));
        $response->assertSee('Buka Kasir');
        $response->assertSee('Transaksi penjualan baru');
        $response->assertSee('Tindak Lanjut');
        $response->assertSee('Omzet Kotor');
        $response->assertSee('Grafik Penjualan dan Omzet');

        $html = $response->getContent();

        // Verifikasi bahwa Buka Kasir muncul SEBELUM Tindak Lanjut di dalam HTML
        $posKasir = strpos($html, 'Buka Kasir');
        $posTindakLanjut = strpos($html, 'Tindak Lanjut');

        $this->assertNotFalse($posKasir, 'Tombol Buka Kasir harus ditemukan di dashboard');
        $this->assertNotFalse($posTindakLanjut, 'Panel Tindak Lanjut harus ditemukan di dashboard');
        $this->assertLessThan(
            $posTindakLanjut,
            $posKasir,
            'Tombol Buka Kasir harus berada di atas panel Tindak Lanjut'
        );

        $this->assertStringContainsString('cashier-card w-full', $html);
    }

    public function test_kasir_dashboard_renders_role_specific_layout(): void
    {
        $kasir = User::create([
            'name' => 'Feriena Kasir',
            'email' => 'kasir@apotek.test',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'aktif' => true,
        ]);

        $response = $this->actingAs($kasir)->get(route('dashboard'));

        $response->assertOk();
        // Kasir melihat Total Member & Buka Kasir & Tindak Lanjut & 10 Obat Terlaris
        $response->assertSee('Total Member');
        $response->assertSee('Buka Kasir');
        $response->assertSee('Transaksi penjualan baru');
        $response->assertSee('Tindak Lanjut');
        $response->assertSee('10 Obat Terlaris');

        // Kasir TIDAK melihat widget yang bukan hak aksesnya
        $response->assertDontSee('Omzet Kotor');
        $response->assertDontSee('Omzet Bersih');
        $response->assertDontSee('Grafik Penjualan dan Omzet');
        $response->assertDontSee('Pemantauan Stok');
        $response->assertDontSee('Kinerja Karyawan');
    }

    public function test_gudang_dashboard_renders_role_specific_layout(): void
    {
        $gudang = User::create([
            'name' => 'Admin Gudang',
            'email' => 'gudang@apotek.test',
            'password' => bcrypt('password'),
            'role' => 'gudang',
            'aktif' => true,
        ]);

        $response = $this->actingAs($gudang)->get(route('dashboard'));

        $response->assertOk();
        // Gudang melihat Tindak Lanjut Gudang & Pemantauan Stok & Barang yang Harus Dibeli
        $response->assertSee('Tindak Lanjut Gudang');
        $response->assertSee('Pemantauan Stok');
        $response->assertSee('Barang yang Harus Dibeli');

        // Gudang TIDAK melihat tombol Buka Kasir widget, KPI Member, Grafik Penjualan, atau 10 Obat Terlaris
        $response->assertDontSee('cashier-card');
        $response->assertDontSee('Transaksi penjualan baru');
        $response->assertDontSee('Total Member');
        $response->assertDontSee('Grafik Penjualan dan Omzet');
        $response->assertDontSee('10 Obat Terlaris');
    }

    public function test_apoteker_dashboard_renders_role_specific_layout(): void
    {
        $apoteker = User::create([
            'name' => 'Apoteker Pengelola',
            'email' => 'apoteker@apotek.test',
            'password' => bcrypt('password'),
            'role' => 'apoteker',
            'aktif' => true,
        ]);

        $response = $this->actingAs($apoteker)->get(route('dashboard'));

        $response->assertOk();
        // Apoteker melihat Total Member, Buka Kasir, Tindak Lanjut, Pemantauan Stok, Obat Terlaris, Dokter Resep
        $response->assertSee('Total Member');
        $response->assertSee('Buka Kasir');
        $response->assertSee('Tindak Lanjut');
        $response->assertSee('Pemantauan Stok');
        $response->assertSee('10 Obat Terlaris');
        $response->assertSee('Dokter Penyumbang Resep');

        // Apoteker TIDAK melihat Omzet Kotor, Omzet Bersih, Grafik, Kinerja Karyawan
        $response->assertDontSee('Omzet Kotor');
        $response->assertDontSee('Grafik Penjualan dan Omzet');
        $response->assertDontSee('Kinerja Karyawan');
    }
}
