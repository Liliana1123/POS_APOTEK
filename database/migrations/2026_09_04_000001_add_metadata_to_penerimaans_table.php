<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penerimaans', function (Blueprint $table) {
            $table->string('telepon_supplier')->nullable()->after('supplier_id');
            $table->text('keterangan')->nullable()->after('telepon_supplier');
            $table->date('jatuh_tempo')->nullable()->after('lunas');
            $table->softDeletes();
        });

        Schema::table('detail_penerimaans', function (Blueprint $table) {
            $table->string('no_rak')->nullable()->after('expired_date');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('penerimaans', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['telepon_supplier', 'keterangan', 'jatuh_tempo']);
        });

        Schema::table('detail_penerimaans', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('no_rak');
        });
    }
};
