<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggans')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('penjualan_id')->nullable()->constrained('penjualans')->nullOnDelete();
            $table->string('nama_member');
            $table->string('nomor_whatsapp', 30);
            $table->string('kategori', 50)->default('pesan_custom');
            $table->string('nama_template')->nullable();
            $table->text('isi_pesan');
            $table->enum('status', ['terkirim', 'menunggu', 'gagal', 'dibatalkan'])->default('terkirim');
            $table->dateTime('waktu_kirim');
            $table->dateTime('waktu_respon')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('faktur_info')->nullable();
            $table->decimal('total_tagihan', 15, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('pelanggan_id');
            $table->index('user_id');
            $table->index('kategori');
            $table->index('status');
            $table->index('waktu_kirim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
