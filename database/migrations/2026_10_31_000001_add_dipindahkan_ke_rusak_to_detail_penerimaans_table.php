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
        if (!Schema::hasColumn('detail_penerimaans', 'dipindahkan_ke_rusak')) {
            Schema::table('detail_penerimaans', function (Blueprint $table) {
                $table->boolean('dipindahkan_ke_rusak')->default(false)->after('aktif');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('detail_penerimaans', 'dipindahkan_ke_rusak')) {
            Schema::table('detail_penerimaans', function (Blueprint $table) {
                $table->dropColumn('dipindahkan_ke_rusak');
            });
        }
    }
};

