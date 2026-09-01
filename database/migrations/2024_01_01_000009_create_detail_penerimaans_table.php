<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penerimaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penerimaan_id')->constrained('penerimaans')->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barangs');
            $table->string('no_batch');
            $table->decimal('harga_beli', 12, 2);
            $table->decimal('harga_jual', 12, 2);
            $table->date('expired_date');
            $table->integer('jumlah'); // jumlah diterima awal
            $table->integer('stok'); // sisa stok batch ini (dipakai untuk transaksi)
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->index(['barang_id', 'expired_date']); // buat query FEFO
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penerimaans');
    }
};
