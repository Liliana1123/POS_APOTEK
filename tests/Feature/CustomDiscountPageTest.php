<?php

namespace Tests\Feature;

use App\Models\CustomDiscount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomDiscountPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_discount_index_renders_for_an_authorized_admin(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-discount@test.local',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'aktif' => true,
        ]);

        CustomDiscount::create([
            'nama' => 'Promo Uji Tampilan',
            'persentase' => 10,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'aktif' => true,
            'cakupan' => 'semua',
        ]);

        $this->actingAs($admin)
            ->get(route('custom-discount.index'))
            ->assertOk()
            ->assertSee('Manajemen Custom Discount (Promo)')
            ->assertSee('Nama Promo')
            ->assertSee('Status Promo')
            ->assertSee('Promo Uji Tampilan')
            ->assertSee('10%')
            ->assertSee('custom-discount');
    }

    public function test_custom_discount_index_remains_protected_by_view_permission(): void
    {
        $cashier = User::create([
            'name' => 'Kasir Test',
            'email' => 'kasir-discount@test.local',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'aktif' => true,
        ]);

        $this->actingAs($cashier)
            ->get(route('custom-discount.index'))
            ->assertForbidden();
    }
}
