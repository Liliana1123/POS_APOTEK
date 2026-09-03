<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->string('kode_apotek', 50)->nullable()->unique()->after('id');
            $table->string('kode_kfa', 100)->nullable()->after('kode_apotek');
            $table->string('merk', 255)->nullable()->after('nama');
        });

        // Tabel untuk mencatat urutan nomor global kode_apotek agar tidak pernah reuse meski barang dihapus
        if (!Schema::hasTable('barang_kode_sequences')) {
            Schema::create('barang_kode_sequences', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->primary();
                $table->unsignedBigInteger('last_number')->default(0);
                $table->timestamps();
            });

            DB::table('barang_kode_sequences')->insert([
                'id' => 1,
                'last_number' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['kode_apotek', 'kode_kfa', 'merk']);
        });

        Schema::dropIfExists('barang_kode_sequences');
    }
};
