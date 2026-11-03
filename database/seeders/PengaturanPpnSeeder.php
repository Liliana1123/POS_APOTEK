<?php

namespace Database\Seeders;

use App\Models\PengaturanPpn;
use App\Models\User;
use Illuminate\Database\Seeder;

class PengaturanPpnSeeder extends Seeder
{
    public function run(): void
    {
        if (PengaturanPpn::count() === 0) {
            $user = User::where('role', 'superadmin')->first() ?? User::first();

            PengaturanPpn::create([
                'nama' => 'PPN Standar 11%',
                'tarif_persen' => 11.00,
                'metode_perhitungan' => 'standar',
                'persentase_dpp' => 100.0000,
                'tipe_perlakuan_harga' => 'eksklusif',
                'tanggal_mulai' => '2024-01-01',
                'tanggal_akhir' => null,
                'is_aktif' => true,
                'keterangan' => 'Tarif PPN standar apotek',
                'created_by' => $user?->id,
            ]);
        }
    }
}

