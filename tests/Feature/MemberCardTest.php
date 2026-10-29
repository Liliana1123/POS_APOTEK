<?php

namespace Tests\Feature;

use App\Models\InfoApotek;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_pelanggan_page_contains_redesigned_member_card_components(): void
    {
        // Seed InfoApotek
        InfoApotek::create([
            'nama_apotek' => 'Apotek Mandiri Sehat',
            'telepon' => '08123456789',
            'email' => 'mandiri@apotek.test',
            'alamat_jalan' => 'Jl. Farmasi Sehat No. 10',
            'kota' => 'Jakarta Selatan',
        ]);

        // Create test user with permissions
        $user = User::create([
            'name' => 'Admin Member Card',
            'email' => 'admin-card@test.local',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'aktif' => true,
        ]);

        // Create test members for each category
        Pelanggan::create([
            'nama' => 'Budi Santoso',
            'member_id' => 'MBR-000001',
            'is_member' => true,
            'member_aktif' => true,
            'status_member' => 'Member Pelanggan Tetap',
            'custom_discount_percentage' => 10.0,
        ]);

        Pelanggan::create([
            'nama' => 'dr. Siti Rahma',
            'member_id' => 'MBR-000002',
            'is_member' => true,
            'member_aktif' => true,
            'status_member' => 'Member Keluarga Nakes',
            'custom_discount_percentage' => 15.0,
        ]);

        Pelanggan::create([
            'nama' => 'Joko Anwar',
            'member_id' => 'MBR-000003',
            'is_member' => true,
            'member_aktif' => true,
            'status_member' => 'Member Only',
            'custom_discount_percentage' => 5.0,
        ]);

        $response = $this->actingAs($user)->get(route('pelanggan.index'));

        $response->assertStatus(200);

        // Verify dynamic pharmacy info appears in card
        $response->assertSee('Apotek Mandiri Sehat');
        $response->assertSee('08123456789');
        $response->assertSee('mandiri@apotek.test');

        // Verify front and back card structural elements
        $response->assertSee('id="modal-card"', false);
        $response->assertSee('id="card-print-container"', false);
        $response->assertSee('id="card-wrapper-front"', false);
        $response->assertSee('id="card-wrapper-back"', false);
        $response->assertSee('id="card-name"', false);
        $response->assertSee('id="card-id"', false);
        $response->assertSee('id="card-discount"', false);
        $response->assertSee('id="card-barcode-svg"', false);
        $response->assertSee('id="card-barcode-text"', false);

        // Verify @page rule uses exact card dimensions (root-cause fix: was 'size: auto')
        $response->assertSee('86mm 54mm landscape', false);
        // Verify each card column triggers its own page via break-after
        $response->assertSee('break-after: page', false);
        $response->assertSee('break-after: avoid', false);

        // Verify preview labels "Depan" and "Belakang"
        $response->assertSee('Depan');
        $response->assertSee('Belakang');

        // Verify Filter Toggle Buttons
        $response->assertSee('Kedua Sisi');
        $response->assertSee('Depan Saja');
        $response->assertSee('Belakang Saja');

        // Verify Data-* attributes on row print button
        $response->assertSee('data-nama="Budi Santoso"', false);
        $response->assertSee('data-member-id="MBR-000001"', false);
        $response->assertSee('openCardModalFromBtn', false);

        // Verify Code 128 function is present
        $response->assertSee('generateCode128Svg', false);
        $response->assertSee('card-terms-ol', false);
    }
}
