<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->boolean('kenakan_ppn')->default(false)->after('total');
            $table->foreignId('ppn_config_id')->nullable()->after('kenakan_ppn')->constrained('pengaturan_ppns')->nullOnDelete();
            $table->decimal('tarif_ppn_persen', 5, 2)->default(0)->after('ppn_config_id');
            $table->string('metode_ppn', 50)->nullable()->after('tarif_ppn_persen');
            $table->decimal('persentase_dpp', 7, 4)->default(100.0000)->after('metode_ppn');
            $table->string('tipe_perlakuan_harga', 20)->default('eksklusif')->after('persentase_dpp');
            $table->decimal('subtotal_bersih', 14, 2)->default(0)->after('tipe_perlakuan_harga');
            $table->decimal('dpp_nominal', 14, 2)->default(0)->after('subtotal_bersih');
            $table->decimal('ppn_nominal', 14, 2)->default(0)->after('dpp_nominal');
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropForeign(['ppn_config_id']);
            $table->dropColumn([
                'kenakan_ppn',
                'ppn_config_id',
                'tarif_ppn_persen',
                'metode_ppn',
                'persentase_dpp',
                'tipe_perlakuan_harga',
                'subtotal_bersih',
                'dpp_nominal',
                'ppn_nominal',
            ]);
        });
    }
};

