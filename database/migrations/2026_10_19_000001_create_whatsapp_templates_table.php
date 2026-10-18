<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('whatsapp_templates')) {
            Schema::create('whatsapp_templates', function (Blueprint $table) {
                $table->id();
                $table->string('nama', 150);
                $table->string('kategori', 50); // pengingat_piutang, promosi_diskon, ucapan_apresiasi, informasi_produk, informasi_pengumuman, pesan_custom
                $table->text('konten');
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });

            // Seed default templates
            $defaultTemplates = [
                // A. Pengingat Piutang
                [
                    'nama' => 'Pengingat Piutang Belum Lunas',
                    'kategori' => 'pengingat_piutang',
                    'konten' => "Yth. Bapak/Ibu {nama_member},\n\nKami dari {nama_apotek} ingin menginformasikan bahwa masih terdapat tagihan piutang sebesar {total_piutang} dengan tanggal jatuh tempo {tanggal_jatuh_tempo}.\n\nMohon kesediaannya untuk melakukan pembayaran sesuai ketentuan yang berlaku.\nJika pembayaran telah dilakukan, silakan abaikan pesan ini atau hubungi petugas apotek untuk konfirmasi.\n\nTerima kasih atas perhatian dan kerja samanya.\n\nSalam,\n{nama_apotek}",
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Pengingat Jatuh Tempo Tagihan',
                    'kategori' => 'pengingat_piutang',
                    'konten' => "Yth. Bapak/Ibu {nama_member},\n\nKami dari {nama_apotek} mengingatkan bahwa tagihan piutang Anda sebesar {total_piutang} telah memasuki batas waktu jatuh tempo ({tanggal_jatuh_tempo}).\n\nMohon untuk segera melakukan penyelesaian pembayaran. Terima kasih atas pengertian dan kerja sama Bapak/Ibu.\n\nSalam hormat,\n{nama_apotek}",
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // B. Promosi & Diskon
                [
                    'nama' => 'Diskon Terbaru & Promo Spesial',
                    'kategori' => 'promosi_diskon',
                    'konten' => "Halo Bapak/Ibu {nama_member},\n\nKabar gembira! Nikmati penawaran diskon spesial dan potongan harga menarik untuk berbagai produk kesehatan pilihan hanya di {nama_apotek}.\n\nKunjungi apotek kami sekarang juga dan jangan lewatkan kesempatan hemat ini!\n\nSalam hangat,\n{nama_apotek}",
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Promo Khusus Member',
                    'kategori' => 'promosi_diskon',
                    'konten' => "Halo {nama_member},\n\nSebagai member setia {nama_apotek}, Anda berhak menikmati promo khusus member minggu ini dengan potongan harga istimewa.\n\nTunjukkan kartu member atau pesan ini kepada kasir kami saat berbelanja. Terima kasih telah menjadi bagian dari keluarga {nama_apotek}!\n\nSalam sehat,\n{nama_apotek}",
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Promo Akhir Bulan',
                    'kategori' => 'promosi_diskon',
                    'konten' => "Halo Bapak/Ibu {nama_member},\n\nPromo Akhir Bulan hadir kembali di {nama_apotek}! Dapatkan harga hemat untuk kebutuhan vitamin dan suplemen keluarga.\n\nPromo berlaku hingga akhir bulan ini. Segera kunjungi {nama_apotek} sebelum kehabisan!\n\nSalam sehat,\n{nama_apotek}",
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Paket Hemat Kesehatan',
                    'kategori' => 'promosi_diskon',
                    'konten' => "Halo {nama_member},\n\nJaga kesehatan keluarga dengan Paket Hemat dari {nama_apotek}! Dapatkan kombinasi produk perlindungan kesehatan dengan harga yang lebih hemat.\n\nInfo selengkapnya hubungi apoteker kami atau kunjungi langsung {nama_apotek}.\n\nSalam hangat,\n{nama_apotek}",
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // C. Ucapan & Apresiasi
                [
                    'nama' => 'Ucapan Selamat Ulang Tahun',
                    'kategori' => 'ucapan_apresiasi',
                    'konten' => "Selamat Ulang Tahun Bapak/Ibu {nama_member}! 🎂🎉\n\nSemoga senantiasa diberikan kesehatan, kebahagiaan, dan keberkahan. Dapatkan penawaran istimewa khusus hari ulang tahun Anda saat berbelanja di {nama_apotek}.\n\nSalam hangat dari seluruh tim {nama_apotek}.",
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Ucapan Hari Raya',
                    'kategori' => 'ucapan_apresiasi',
                    'konten' => "Selamat Hari Raya untuk Bapak/Ibu {nama_member} dan seluruh keluarga.\n\nSemoga kedamaian, kesehatan, dan keberkahan senantiasa menyertai kita semua. Mohon maaf lahir dan batin.\n\nSalam hangat,\n{nama_apotek}",
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Ucapan Terima Kasih Berbelanja',
                    'kategori' => 'ucapan_apresiasi',
                    'konten' => "Terima kasih Bapak/Ibu {nama_member} telah berbelanja di {nama_apotek}.\n\nKesehatan dan kepuasan Anda adalah prioritas utama kami. Semoga lekas pulih dan sehat selalu!\n\nSalam,\n{nama_apotek}",
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Apresiasi Pelanggan Setia',
                    'kategori' => 'ucapan_apresiasi',
                    'konten' => "Yth. Bapak/Ibu {nama_member},\n\nTerima kasih atas kepercayaan dan loyalitas Anda menjadi member setia di {nama_apotek}.\n\nKami terus berupaya memberikan pelayanan farmasi terbaik bagi Anda dan keluarga.\n\nSalam hormat,\n{nama_apotek}",
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // D. Informasi Produk
                [
                    'nama' => 'Informasi Produk Tersedia Kembali',
                    'kategori' => 'informasi_produk',
                    'konten' => "Halo Bapak/Ibu {nama_member},\n\nProduk yang Anda nantikan kini telah tersedia kembali di {nama_apotek}.\n\nKunjungi apotek kami segera atau hubungi kami untuk konfirmasi ketersediaan.\n\nSalam sehat,\n{nama_apotek}",
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Informasi Produk Baru',
                    'kategori' => 'informasi_produk',
                    'konten' => "Halo Bapak/Ibu {nama_member},\n\nTelah hadir produk kesehatan terbaru di {nama_apotek} untuk mendukung daya tahan tubuh dan kebugaran Anda.\n\nKonsultasikan kebutuhan produk kesehatan Anda bersama apoteker kami.\n\nSalam,\n{nama_apotek}",
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // E. Informasi & Pengumuman
                [
                    'nama' => 'Informasi Libur Apotek',
                    'kategori' => 'informasi_pengumuman',
                    'konten' => "Pemberitahuan kepada pelanggan setia {nama_apotek}:\n\nDalam rangka memperingati hari libur, {nama_apotek} akan libur operasional sementara pada [Hari, Tanggal] dan akan kembali beroperasi normal pada [Hari, Tanggal].\n\nPastikan stok obat rutin Anda tercukupi. Terima kasih atas pengertiannya.\n\nSalam,\n{nama_apotek}",
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Perubahan Jam Operasional',
                    'kategori' => 'informasi_pengumuman',
                    'konten' => "Pemberitahuan kepada seluruh pelanggan setia {nama_apotek}:\n\nTerhitung mulai tanggal [Tanggal], jam operasional {nama_apotek} disesuaikan menjadi pukul [Jam Buka] s/d [Jam Tutup].\n\nTerima kasih atas perhatian dan kerja sama Anda.\n\nSalam sehat,\n{nama_apotek}",
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // F. Pesan Custom
                [
                    'nama' => 'Template Pesan Kustom Bebas',
                    'kategori' => 'pesan_custom',
                    'konten' => "Halo Bapak/Ibu {nama_member},\n\n[Tuliskan isi pesan Anda di sini]\n\nSalam,\n{nama_apotek}",
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('whatsapp_templates')->insert($defaultTemplates);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_templates');
    }
};
