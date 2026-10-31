<?php

namespace Tests\Feature;

use App\Models\InfoApotek;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengaturanBackgroundTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database with default permissions if available
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    public function test_admin_and_superadmin_can_view_pengaturan_apotek_with_background_section(): void
    {
        $superadmin = User::where('role', 'superadmin')->first() ?? User::factory()->create([
            'role' => 'superadmin',
            'aktif' => true,
        ]);

        $response = $this->actingAs($superadmin)->get(route('pengaturan.index'));

        $response->assertOk();
        $response->assertSee('Latar Belakang Layout & Dashboard', false);
        $response->assertSee('Abu-Abu Polos (Default)');
        $response->assertSee('Motif Batik Elegan (Bawaan)');
        $response->assertSee('Kelembutan / Transparansi Latar Belakang (Dissolve)');
        $response->assertSee('Upload Gambar / Pola Latar Belakang');
    }

    public function test_custom_uploaded_background_displays_custom_labels(): void
    {
        $superadmin = User::where('role', 'superadmin')->first() ?? User::factory()->create([
            'role' => 'superadmin',
            'aktif' => true,
        ]);

        $apotek = InfoApotek::singleton();
        $apotek->update([
            'background_type' => 'custom',
            'background_image' => 'info-apotek/sample.jpg',
        ]);

        $response = $this->actingAs($superadmin)->get(route('pengaturan.index'));

        $response->assertOk();
        $response->assertSee('Gambar / Pola Kustom');
        $response->assertSee('Gambar Kustom Aktif');
        $response->assertSee('Mode: Gambar Kustom');
    }

    public function test_superadmin_can_update_background_to_custom_and_change_opacity(): void
    {
        $superadmin = User::where('role', 'superadmin')->first() ?? User::factory()->create([
            'role' => 'superadmin',
            'aktif' => true,
        ]);

        $apotek = InfoApotek::singleton();

        $response = $this->actingAs($superadmin)->put(route('pengaturan.update'), [
            'nama_apotek' => 'Apotek Sehat Bahagia',
            'background_type' => 'custom',
            'background_opacity' => 45,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $apotek->refresh();
        $this->assertEquals('custom', $apotek->background_type);
        $this->assertEquals(45, $apotek->background_opacity);
    }

    public function test_superadmin_can_upload_custom_background_image(): void
    {
        Storage::fake('public');

        $superadmin = User::where('role', 'superadmin')->first() ?? User::factory()->create([
            'role' => 'superadmin',
            'aktif' => true,
        ]);

        $apotek = InfoApotek::singleton();
        $file = UploadedFile::fake()->create('custom_pattern.jpg', 500, 'image/jpeg');

        $response = $this->actingAs($superadmin)->put(route('pengaturan.update'), [
            'nama_apotek' => 'Apotek Sehat Bahagia',
            'background_type' => 'custom',
            'background_opacity' => 30,
            'background_image' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $apotek->refresh();
        $this->assertEquals('custom', $apotek->background_type);
        $this->assertEquals(30, $apotek->background_opacity);
        $this->assertNotNull($apotek->background_image);
        Storage::disk('public')->assertExists($apotek->background_image);
    }

    public function test_superadmin_can_switch_back_to_default_background(): void
    {
        $superadmin = User::where('role', 'superadmin')->first() ?? User::factory()->create([
            'role' => 'superadmin',
            'aktif' => true,
        ]);

        $apotek = InfoApotek::singleton();
        $apotek->update([
            'background_type' => 'custom',
            'background_opacity' => 50,
        ]);

        $response = $this->actingAs($superadmin)->put(route('pengaturan.update'), [
            'nama_apotek' => 'Apotek Sehat Bahagia',
            'background_type' => 'default',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $apotek->refresh();
        $this->assertEquals('default', $apotek->background_type);
    }

    public function test_superadmin_can_reset_to_default_batik_motif(): void
    {
        Storage::fake('public');

        $superadmin = User::where('role', 'superadmin')->first() ?? User::factory()->create([
            'role' => 'superadmin',
            'aktif' => true,
        ]);

        $apotek = InfoApotek::singleton();
        $apotek->update([
            'background_type' => 'custom',
            'background_image' => 'info-apotek/old_uploaded.jpg',
            'background_opacity' => 50,
        ]);

        Storage::disk('public')->put('info-apotek/old_uploaded.jpg', 'dummy');

        $response = $this->actingAs($superadmin)->put(route('pengaturan.update'), [
            'nama_apotek' => 'Apotek Sehat Bahagia',
            'background_type' => 'custom',
            'reset_background_default' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $apotek->refresh();
        $this->assertEquals('custom', $apotek->background_type);
        $this->assertNull($apotek->background_image);
        $this->assertStringContainsString('batik-pattern.png', $apotek->background_url);
        Storage::disk('public')->assertMissing('info-apotek/old_uploaded.jpg');
    }

    public function test_non_admin_cannot_update_background_settings(): void
    {
        $kasir = User::create([
            'name' => 'Kasir Test',
            'email' => 'kasir-test@local.test',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'aktif' => true,
        ]);

        $apotek = InfoApotek::singleton();
        $apotek->update([
            'background_type' => 'default',
            'background_opacity' => 40,
        ]);

        // Kasir does not have permission to manage pengaturan, so it gets 403
        $response = $this->actingAs($kasir)->put(route('pengaturan.update'), [
            'nama_apotek' => 'Apotek Hack',
            'background_type' => 'custom',
            'background_opacity' => 60,
        ]);

        $response->assertForbidden();

        $apotek->refresh();
        $this->assertEquals('default', $apotek->background_type);
        $this->assertEquals(40, $apotek->background_opacity);
    }
}
