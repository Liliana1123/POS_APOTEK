<?php

namespace Database\Seeders;

use App\Models\Pabrik;
use Illuminate\Database\Seeder;

class PabrikSeeder extends Seeder
{
    public function run(): void
    {
        $pabriks = [
            [
                'id' => 1,
                'nama' => 'Kimia Farma',
                'telepon' => '021-3847722',
                'alamat' => 'Jl. Veteran No. 9, Gambir, Jakarta Pusat, DKI Jakarta 10110',
                'pic' => 'apt. Budi Santoso, S.Farm',
            ],
            [
                'id' => 2,
                'nama' => 'Kalbe Farma',
                'telepon' => '021-42873888',
                'alamat' => 'Kawasan Industri Delta Silicon, Jl. MH. Thamrin Blok A3-1, Lippo Cikarang, Bekasi 17550',
                'pic' => 'apt. Dewi Anggraini, M.Farm',
            ],
            [
                'id' => 3,
                'nama' => 'Sanbe Farma',
                'telepon' => '022-6015696',
                'alamat' => 'Jl. Tamansari No. 10, Bandung, Jawa Barat 40116',
                'pic' => 'apt. Hendra Gunawan, S.Farm',
            ],
            [
                'id' => 4,
                'nama' => 'Dexa Medica',
                'telepon' => '021-7454111',
                'alamat' => 'Titan Center Lt. 3, Jl. Boulevard Bintaro Sektor 7, Tangerang Selatan 15224',
                'pic' => 'apt. Rian Pratama, M.Si',
            ],
            [
                'id' => 39,
                'nama' => 'Phapros',
                'telepon' => '024-7625484',
                'alamat' => 'Jl. Simongan No. 131, Semarang, Jawa Tengah 50148',
                'pic' => 'apt. Eko Supriyadi, S.Farm',
            ],
            [
                'id' => 41,
                'nama' => 'Tempo Scan Pacific',
                'telepon' => '021-29208888',
                'alamat' => 'Tempo Scan Tower Lt. 16, Jl. H.R. Rasuna Said Kav. 3-4, Jakarta Selatan 12950',
                'pic' => 'apt. Ratna Sari, S.Farm',
            ],
            [
                'id' => 42,
                'nama' => 'Mahakam Beta Farma',
                'telepon' => '021-8710311',
                'alamat' => 'Jl. Pulo Kambing II No. 20, Kawasan Industri Pulogadung, Jakarta Timur 13930',
                'pic' => 'apt. Faisal Akbar, S.Farm',
            ],
            [
                'id' => 43,
                'nama' => 'Novartis',
                'telepon' => '021-2520888',
                'alamat' => 'Cyber 2 Tower Lt. 27, Jl. H.R. Rasuna Said Blok X-5 Kav. 13, Jakarta Selatan 12950',
                'pic' => 'apt. Sarah Amalia, M.Farm',
            ],
            [
                'id' => 44,
                'nama' => 'Darya-Varia',
                'telepon' => '021-5203410',
                'alamat' => 'South Quarter Tower C Lt. 18-20, Jl. RA Kartini Kav. 8, Cilandak Barat, Jakarta Selatan 12430',
                'pic' => 'apt. Dimas Prasetyo, S.Farm',
            ],
            [
                'id' => 45,
                'nama' => 'Ifars',
                'telepon' => '0271-714444',
                'alamat' => 'Jl. Raya Solo-Sragen Km. 14.9, Kebakkramat, Karanganyar, Jawa Tengah 57762',
                'pic' => 'apt. Wahyu Hidayat, S.Farm',
            ],
            [
                'id' => 46,
                'nama' => 'Soho Industri Pharmasi',
                'telepon' => '021-4605550',
                'alamat' => 'Jl. Pulogadung No. 6, Kawasan Industri Pulogadung, Jakarta Timur 13920',
                'pic' => 'apt. Maya Kusuma, M.Farm',
            ],
            [
                'id' => 47,
                'nama' => 'Johnson & Johnson',
                'telepon' => '021-57900180',
                'alamat' => 'K-Link Tower Lt. 12, Jl. Gatot Subroto Kav. 59A, Setiabudi, Jakarta Selatan 12950',
                'pic' => 'apt. Aditya Pratama, S.Farm',
            ],
            [
                'id' => 48,
                'nama' => 'Combiphar',
                'telepon' => '021-53676888',
                'alamat' => 'Office 8 Lt. 26-27, SCBD Lot 28, Jl. Jend. Sudirman Kav. 52-53, Jakarta Selatan 12190',
                'pic' => 'apt. Citra Anindya, S.Farm',
            ],
            [
                'id' => 49,
                'nama' => 'Sterling Products Indonesia',
                'telepon' => '021-5250408',
                'alamat' => 'Menara Standard Chartered Lt. 22, Jl. Prof. Dr. Satrio No. 164, Jakarta Selatan 12930',
                'pic' => 'apt. Joko Susilo, S.Farm',
            ],
            [
                'id' => 50,
                'nama' => 'Medikon Prima',
                'telepon' => '021-5818980',
                'alamat' => 'Jl. Angsana Raya Blok A8 No. 1, Delta Silicon 1, Cikarang, Bekasi 17550',
                'pic' => 'apt. Nurul Hasanah, S.Farm',
            ],
            [
                'id' => 51,
                'nama' => 'Sido Muncul',
                'telepon' => '024-76928811',
                'alamat' => 'Jl. Soekarno Hatta Km. 28, Bergas, Klepu, Semarang, Jawa Tengah 50552',
                'pic' => 'apt. Irwan Hidayat, S.Farm',
            ],
            [
                'nama' => 'Bio Farma',
                'telepon' => '022-2033755',
                'alamat' => 'Jl. Pasteur No. 28, Pasteur, Sukajadi, Bandung, Jawa Barat 40161',
                'pic' => 'apt. Agus Setiawan, M.Farm',
            ],
            [
                'nama' => 'Konimex',
                'telepon' => '0271-719966',
                'alamat' => 'Desa Sanggrahan, Kec. Grogol, Kab. Sukoharjo, Jawa Tengah 57552',
                'pic' => 'apt. Rudi Hartono, S.Farm',
            ],
            [
                'nama' => 'Pharos Indonesia',
                'telepon' => '021-7200981',
                'alamat' => 'Jl. Limo No. 40, Permata Hijau, Kebayoran Lama, Jakarta Selatan 12220',
                'pic' => 'apt. Andi Wijaya, S.Farm',
            ],
            [
                'nama' => 'Novell Pharmaceutical',
                'telepon' => '021-5355555',
                'alamat' => 'Jl. Pos Pengumben Raya No. 40, Kebon Jeruk, Jakarta Barat 11560',
                'pic' => 'apt. Dian Puspitasari, M.Farm',
            ],
            [
                'nama' => 'Interbat',
                'telepon' => '021-4244244',
                'alamat' => 'Jl. Cempaka Putih Barat No. 26, Jakarta Pusat, DKI Jakarta 10520',
                'pic' => 'apt. Bayu Nugroho, S.Farm',
            ],
            [
                'nama' => 'Bernofarm',
                'telepon' => '031-8661175',
                'alamat' => 'Jl. Raya Juanda No. 8, Sedati, Sidoarjo, Jawa Timur 61253',
                'pic' => 'apt. Linda Kusumawati, S.Farm',
            ],
            [
                'nama' => 'Tropica Mas Pharmaceuticals',
                'telepon' => '021-5901234',
                'alamat' => 'Kawasan Industri Manis, Jl. Manis Raya No. 18, Curug, Tangerang 15810',
                'pic' => 'apt. Siti Rahmawati, S.Farm',
            ],
            [
                'nama' => 'Gratia Husada Farma (HUFA)',
                'telepon' => '022-5205555',
                'alamat' => 'Jl. Cisirung No. 99, Dayeuhkolot, Bandung, Jawa Barat 40258',
                'pic' => 'apt. Bambang Irawan, S.Farm',
            ],
        ];

        foreach ($pabriks as $data) {
            if (isset($data['id'])) {
                $pabrik = Pabrik::find($data['id']);
                if ($pabrik) {
                    $pabrik->update([
                        'nama' => $data['nama'],
                        'telepon' => $data['telepon'],
                        'alamat' => $data['alamat'],
                        'pic' => $data['pic'],
                    ]);
                    continue;
                }
            }

            Pabrik::updateOrCreate(
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
