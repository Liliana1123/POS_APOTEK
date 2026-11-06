<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_ppns', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->decimal('tarif_persen', 5, 2);
            $table->enum('metode_perhitungan', ['standar', 'dpp_nilai_lain'])->default('standar');
            $table->decimal('persentase_dpp', 7, 4)->default(100.0000);
            $table->enum('tipe_perlakuan_harga', ['eksklusif', 'inklusif'])->default('eksklusif');
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_ppns');
    }
};

