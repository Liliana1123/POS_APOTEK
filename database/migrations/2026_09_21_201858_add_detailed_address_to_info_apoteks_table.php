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
        Schema::table('info_apoteks', function (Blueprint $table) {
            $table->string('alamat_jalan')->nullable()->after('alamat');
            $table->string('kelurahan', 100)->nullable()->after('alamat_jalan');
            $table->string('kecamatan', 100)->nullable()->after('kelurahan');
            $table->string('kota', 100)->nullable()->after('kecamatan');
            $table->string('provinsi', 100)->nullable()->after('kota');
            $table->string('kode_pos', 20)->nullable()->after('provinsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('info_apoteks', function (Blueprint $table) {
            $table->dropColumn([
                'alamat_jalan',
                'kelurahan',
                'kecamatan',
                'kota',
                'provinsi',
                'kode_pos',
            ]);
        });
    }
};
