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
        // Akun awal untuk login pertama kali
        User::create([
            'name' => 'Admin',
            'email' => 'admin@apotek.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'aktif' => true,
        ]);

        User::create([
            'name' => 'Kasir',
            'email' => 'kasir@apotek.test',
            'password' => Hash::make('password'),
            'role' => 'kasir',
            'aktif' => true,
        ]);

        // Kategori umum di apotek
        foreach (['Obat bebas', 'Obat bebas terbatas', 'Obat keras', 'Alat kesehatan', 'Vitamin & suplemen'] as $nama) {
            Kategori::create(['nama' => $nama]);
        }

        // Satuan yang umum dipakai
        foreach (['Tablet', 'Strip', 'Botol', 'Box', 'Sachet', 'Tube', 'Pcs'] as $nama) {
            Satuan::create(['nama' => $nama]);
        }

        // Contoh pabrik (silakan sesuaikan/tambah sesuai kebutuhan)
        foreach (['Kimia Farma', 'Kalbe Farma', 'Sanbe Farma', 'Dexa Medica'] as $nama) {
            Pabrik::create(['nama' => $nama]);
        }
    }
}
