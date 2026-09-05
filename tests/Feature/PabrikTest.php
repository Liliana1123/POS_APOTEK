<?php

namespace Tests\Feature;

use App\Models\Pabrik;
use App\Models\User;
use Database\Seeders\PabrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PabrikTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

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
    }

    public function test_pabrik_index_displays_seeder_data_properly()
    {
        $this->seed(PabrikSeeder::class);

        $response = $this->actingAs($this->admin)->get(route('pabrik.index'));

        $response->assertStatus(200);
        $response->assertSee('Kimia Farma');
        $response->assertSee('021-3847722');
        $response->assertSee('apt. Budi Santoso, S.Farm');
        $response->assertSee('Kalbe Farma');
    }

    public function test_pabrik_create_and_update()
    {
        $response = $this->actingAs($this->admin)->post(route('pabrik.store'), [
            'nama' => 'Pabrik Uji Coba',
            'telepon' => '021-99887766',
            'alamat' => 'Jl. Pengujian No. 123',
            'pic' => 'apt. Tester, S.Farm',
        ]);

        $response->assertRedirect(route('pabrik.index'));
        $this->assertDatabaseHas('pabriks', [
            'nama' => 'Pabrik Uji Coba',
            'telepon' => '021-99887766',
            'pic' => 'apt. Tester, S.Farm',
        ]);
    }
}
