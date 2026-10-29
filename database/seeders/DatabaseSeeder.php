<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Pabrik;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun awal untuk login pertama kali. firstOrCreate supaya seeder
        // boleh dijalankan ulang di database yang sudah berisi data.
        foreach ([
            ['Superadmin', 'superadmin@apotek.test', 'superadmin'],
            ['Admin', 'admin@apotek.test', 'admin'],
            ['Kasir', 'kasir@apotek.test', 'kasir'],
        ] as [$name, $email, $role]) {
            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => $role,
                    'aktif' => true,
                ]
            );
        }

        // Kategori umum di apotek
        foreach (['Obat bebas', 'Obat bebas terbatas', 'Obat keras', 'Alat kesehatan', 'Vitamin & suplemen'] as $nama) {
            Kategori::firstOrCreate(['nama' => $nama]);
        }

        // Satuan yang umum dipakai
        foreach (['Tablet', 'Strip', 'Botol', 'Box', 'Sachet', 'Tube', 'Pcs'] as $nama) {
            Satuan::firstOrCreate(['nama' => $nama]);
        }

        // Data pabrik farmasi
        $this->call(PabrikSeeder::class);

        // Data supplier / distributor farmasi
        $this->call(SupplierSeeder::class);
    }
}
