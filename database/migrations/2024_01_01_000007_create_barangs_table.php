<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('kategori_id')->constrained('kategoris');
            $table->foreignId('satuan_id')->constrained('satuans');
            $table->foreignId('pabrik_id')->constrained('pabriks');
            $table->string('barcode')->nullable()->unique();
            $table->boolean('butuh_resep')->default(false);
            $table->integer('stok_minimum')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
