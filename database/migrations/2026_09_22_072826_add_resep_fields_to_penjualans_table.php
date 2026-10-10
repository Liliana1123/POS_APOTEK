<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->string('jenis_transaksi')
                ->default('non_resep');

            $table->string('nama_dokter')->nullable();
            $table->string('id_dokter')->nullable();
            $table->string('alamat_lembaga')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_transaksi',
                'nama_dokter',
                'id_dokter',
                'alamat_lembaga',
            ]);
        });
    }
};