<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'nama' => 'PT Enseval Putera Megatrading Tbk',
                'telepon' => '021-4603777',
                'alamat' => 'Jl. Pulogadung No. 9, Kawasan Industri Pulogadung, Jakarta Timur 13920',
                'pic' => 'apt. Rendra Kurniawan, S.Farm',
            ],
            [
                'nama' => 'PT Anugerah Pharmindo Lestari',
                'telepon' => '021-80639999',
                'alamat' => 'Menara FIF Lantai 10, Jl. TB Simatupang Kav. 1, Jakarta Selatan 12560',
                'pic' => 'apt. Sari Dewi Lestari, M.Farm',
            ],
            [
                'nama' => 'PT Bina San Prima',
                'telepon' => '021-39721900',
                'alamat' => 'Jalur Sutera Barat Kav. 11-12, Tangerang, Banten 15143',
                'pic' => 'apt. Andre Wijaya, S.Farm',
            ],
            [
                'nama' => 'PT Parit Padang Global (PPG)',
                'telepon' => '021-4603244',
                'alamat' => 'Jl. Pulogadung Raya No. 23, Jakarta Timur 13920',
                'pic' => 'apt. Melati Puspita, S.Farm',
            ],
            [
                'nama' => 'PT Zenith Alliances Indonesia',
                'telepon' => '021-29653599',
                'alamat' => 'Jl. Raya Bogor Km. 29 No. 19, Cimanggis, Depok 16953',
                'pic' => 'apt. Yudha Pratama, M.Farm',
            ],
            [
                'nama' => 'PT Millenium Pharmacon International',
                'telepon' => '021-45848989',
                'alamat' => 'Jl. Raya Jembatan No. 8, Cawang, Jakarta Timur 13630',
                'pic' => 'apt. Nadia Safitri, S.Farm',
            ],
            [
                'nama' => 'PT Kimia Farma Trading & Distribution',
                'telepon' => '021-3847709',
                'alamat' => 'Jl. Veteran No. 9, Gambir, Jakarta Pusat 10110',
                'pic' => 'apt. Budi Raharjo, S.Farm',
            ],
            [
                'nama' => 'PT Merapi Utama Pharma',
                'telepon' => '0274-547885',
                'alamat' => 'Jl. Godean Km. 3 No. 45, Sinduadi, Mlati, Sleman, DIY 55284',
                'pic' => 'apt. Rizky Amalia, S.Farm',
            ],
            [
                'nama' => 'PT Penta Arana',
                'telepon' => '021-4603780',
                'alamat' => 'Jl. Raya Bekasi Km. 19 No. 8, Jakarta Timur 13910',
                'pic' => 'apt. Farhan Maulana, M.Farm',
            ],
            [
                'nama' => 'PT Royal Medicalindo',
                'telepon' => '021-29660188',
                'alamat' => 'Jl. Palem Manis I No. 15, Tangerang 15122',
                'pic' => 'apt. Intan Permata Sari, S.Farm',
            ],
        ];

        foreach ($suppliers as $data) {
            Supplier::updateOrCreate(
                ['nama' => $data['nama']],
                [
                    'telepon' => $data['telepon'],
                    'alamat' => $data['alamat'],
                    'pic' => $data['pic'],
                ]
            );
        }
    }
}
