<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('info_apoteks', function (Blueprint $table) {
            $table->boolean('ppn_aktif')->default(true)->after('logo');
        });
    }

    public function down(): void
    {
        Schema::table('info_apoteks', function (Blueprint $table) {
            $table->dropColumn('ppn_aktif');
        });
    }
};

