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

    public function test_custom_discount_store_returns_json_when_requested(): void
    {
        $superadmin = User::create([
            'name' => 'Superadmin Test',
            'email' => 'superadmin-discount@test.local',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'aktif' => true,
        ]);

        $response = $this->actingAs($superadmin)
            ->postJson(route('custom-discount.store'), [
                'nama' => 'Promo AJAX Test',
                'persentase' => 15,
                'tanggal_mulai' => now()->toDateString(),
                'tanggal_selesai' => now()->addDays(5)->toDateString(),
                'aktif' => 1,
                'cakupan' => 'semua',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Custom discount berhasil ditambahkan.',
            ]);

        $this->assertDatabaseHas('custom_discounts', [
            'nama' => 'Promo AJAX Test',
            'persentase' => 15,
        ]);
    }

    public function test_custom_discount_update_returns_json_when_requested(): void
    {
        $superadmin = User::create([
            'name' => 'Superadmin Test 2',
            'email' => 'superadmin2-discount@test.local',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'aktif' => true,
        ]);

        $discount = CustomDiscount::create([
            'nama' => 'Promo Awal',
            'persentase' => 10,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(3)->toDateString(),
            'aktif' => 1,
            'cakupan' => 'semua',
        ]);

        $response = $this->actingAs($superadmin)
            ->putJson(route('custom-discount.update', $discount), [
                'nama' => 'Promo Diperbarui',
                'persentase' => 20,
                'tanggal_mulai' => now()->toDateString(),
                'tanggal_selesai' => now()->addDays(3)->toDateString(),
                'aktif' => 1,
                'cakupan' => 'semua',
            ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Custom discount berhasil diperbarui.',
            ]);

        $this->assertDatabaseHas('custom_discounts', [
            'id' => $discount->id,
            'nama' => 'Promo Diperbarui',
            'persentase' => 20,
        ]);
    }

    public function test_custom_discount_conflict_returns_422_json_when_requested(): void
    {
        $superadmin = User::create([
            'name' => 'Superadmin Test 3',
            'email' => 'superadmin3-discount@test.local',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'aktif' => true,
        ]);

        CustomDiscount::create([
            'nama' => 'Promo Eksisting',
            'persentase' => 10,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(10)->toDateString(),
            'aktif' => 1,
            'cakupan' => 'semua',
        ]);

        $response = $this->actingAs($superadmin)
            ->postJson(route('custom-discount.store'), [
                'nama' => 'Promo Konflik',
                'persentase' => 25,
                'tanggal_mulai' => now()->addDays(1)->toDateString(),
                'tanggal_selesai' => now()->addDays(5)->toDateString(),
                'aktif' => 1,
                'cakupan' => 'semua',
            ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['conflict']]);
    }
}
