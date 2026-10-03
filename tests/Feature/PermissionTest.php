<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    private function buatUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $role.'@test.local',
            'password' => bcrypt('password'),
            'role' => $role,
            'aktif' => true,
        ]);
    }

    private function setIzin(string $role, string $slug, bool $view, bool $manage): void
    {
        $perm = Permission::where('slug', $slug)->firstOrFail();

        \DB::table('role_permissions')->updateOrInsert(
            ['role' => $role, 'permission_id' => $perm->id],
            ['view' => $view, 'manage' => $manage]
        );
    }

    public function test_default_matrix_memberi_admin_seluruh_izin(): void
    {
        $admin = $this->buatUser('admin');

        $this->assertTrue($admin->hasPermission('master.barang'));
        $this->assertTrue($admin->canManage('master.barang'));
        $this->assertTrue($admin->hasPermission('laporan.laba-rugi'));
        $this->assertTrue($admin->canManage('sistem.kelola-user'));
    }

    public function test_kasir_hanya_boleh_penjualan(): void
    {
        $kasir = $this->buatUser('kasir');

        $this->assertTrue($kasir->canManage('transaksi.penjualan'));
        $this->assertFalse($kasir->hasPermission('master.barang'));
        $this->assertFalse($kasir->hasPermission('laporan.penjualan'));
        $this->assertFalse($kasir->hasPermission('sistem.pengaturan'));
    }

    public function test_kasir_tidak_bisa_akses_halaman_laporan(): void
    {
        $kasir = $this->buatUser('kasir');

        $this->actingAs($kasir)->get(route('laporan.penjualan'))->assertForbidden();
    }

    public function test_kasir_tidak_bisa_akses_kelola_user(): void
    {
        $kasir = $this->buatUser('kasir');

        $this->actingAs($kasir)->get(route('user.index'))->assertForbidden();
    }

    public function test_izin_dimatikan_langsung_blokir_halunya(): void
    {
        $admin = $this->buatUser('admin');
        $this->setIzin('admin', 'transaksi.penjualan', false, false);

        $this->actingAs($admin)->get(route('penjualan.index'))->assertForbidden();
    }

    public function test_view_saja_membuka_halaman_nilai_tapi_bukan_tambah(): void
    {
        $apoteker = $this->buatUser('apoteker');
        $this->setIzin('apoteker', 'master.kategori', true, false);

        $this->actingAs($apoteker)->get(route('kategori.index'))->assertOk();
        $this->actingAs($apoteker)->get(route('kategori.create'))->assertForbidden();
    }

    public function test_superadmin_bypass_walau_izin_dimatikan_semua(): void
    {
        $super = $this->buatUser('superadmin');

        $this->setIzin('superadmin', 'transaksi.penjualan', false, false);

        $this->assertTrue($super->hasPermission('transaksi.penjualan'));
        $this->assertTrue($super->canManage('transaksi.penjualan'));
    }

    public function test_admin_biasa_tidak_bisa_buat_user_superadmin(): void
    {
        $admin = $this->buatUser('admin');

        $this->actingAs($admin)->post(route('user.store'), [
            'name' => 'Calon Super',
            'email' => 'calon@test.local',
            'password' => 'password123',
            'role' => 'superadmin',
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'calon@test.local']);
    }

    public function test_superadmin_tidak_bisa_dinonaktifkan_oleh_admin(): void
    {
        $admin = $this->buatUser('admin');
        $super = $this->buatUser('superadmin');

        $this->actingAs($admin)->patch(route('user.toggle', $super))->assertForbidden();

        $this->assertTrue($super->refresh()->aktif);
    }

    public function test_admin_biasa_tidak_bisa_ubah_role_superadmin(): void
    {
        $admin = $this->buatUser('admin');
        $super = $this->buatUser('superadmin');

        $this->actingAs($admin)->put(route('user.update', $super), [
            'name' => $super->name,
            'email' => $super->email,
            'role' => 'admin',
        ])->assertForbidden();

        $this->assertSame('superadmin', $super->refresh()->role);
    }

    public function test_halaman_izin_akses_hanya_untuk_superadmin(): void
    {
        $admin = $this->buatUser('admin');
        $super = $this->buatUser('superadmin');

        $this->actingAs($admin)->get(route('permission.index'))->assertForbidden();
        $this->actingAs($super)->get(route('permission.index'))->assertOk();
    }

    public function test_toggle_izin_dari_halaman_izin_akses_berpengaruh(): void
    {
        $super = $this->buatUser('superadmin');
        $kasir = $this->buatUser('kasir');
        $perm = Permission::where('slug', 'laporan.stok')->firstOrFail();

        $this->actingAs($super)->post(route('permission.update'), [
            'view' => [
                'kasir' => [$perm->id => 1],
            ],
        ])->assertRedirect();

        $this->assertTrue($kasir->refresh()->hasPermission('laporan.stok'));
        $this->actingAs($kasir)->get(route('laporan.stok'))->assertOk();
    }

    public function test_manage_mengaktifkan_view_secara_otomatis(): void
    {
        $super = $this->buatUser('superadmin');
        $kasir = $this->buatUser('kasir');
        $perm = Permission::where('slug', 'laporan.stok')->firstOrFail();

        $this->actingAs($super)->post(route('permission.update'), [
            'manage' => [
                'kasir' => [$perm->id => 1],
            ],
        ])->assertRedirect();

        $this->assertTrue($kasir->refresh()->hasPermission('laporan.stok'));
    }

    public function test_reset_mengembalikan_ke_default(): void
    {
        $super = $this->buatUser('superadmin');
        $apoteker = $this->buatUser('apoteker');
        $this->setIzin('apoteker', 'laporan.stok', true, true);

        $this->actingAs($super)->post(route('permission.reset'))->assertRedirect();

        $apoteker->refresh();
        $this->assertFalse($apoteker->hasPermission('laporan.stok'));
        $this->assertTrue($apoteker->hasPermission('transaksi.penjualan'));
    }

    public function test_tamu_tidak_bisa_akses_halaman_yang_dilindungi(): void
    {
        $this->get(route('user.index'))->assertRedirect(route('login'));
        $this->get(route('permission.index'))->assertRedirect(route('login'));
    }

    public function test_toggle_izin_terlihat_dan_tidak_memicu_submit_otomatis(): void
    {
        $super = $this->buatUser('superadmin');

        $html = $this->actingAs($super)->get(route('permission.index'))->assertOk()->getContent();

        $toggle = preg_match_all('/name="(?:view|manage)\[[a-z]+\]\[\d+\]"/', $html);
        $this->assertSame(
            Permission::count() * (count(config('permission.roles')) - 1) * 2,
            $toggle
        );

        $this->assertStringNotContainsString('sr-only', $html);
        $this->assertStringNotContainsString('peer-checked', $html);

        $this->assertStringNotContainsString('onchange=', $html);
    }

    public function test_simpan_izin_lalu_kembali_ke_halaman_tetap_terbuka(): void
    {
        $super = $this->buatUser('superadmin');

        $response = $this->actingAs($super)
            ->from(route('permission.index'))
            ->post(route('permission.update'), [
                'view' => ['kasir' => [Permission::where('slug', 'laporan.stok')->value('id') => 1]],
            ]);

        $response->assertRedirect(route('permission.index'));
        $response->assertSessionHas('success');

        $this->actingAs($super)
            ->get(route('permission.index'))
            ->assertOk()
            ->assertSee('Izin akses berhasil disimpan');
    }
}
