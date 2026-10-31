<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'Nama_lengkap')) {
            // Pindahkan Nama_lengkap ke name jika name masih placeholder 'aku admin'
            DB::table('users')
                ->where('name', 'aku admin')
                ->whereNotNull('Nama_lengkap')
                ->where('Nama_lengkap', '!=', '')
                ->update(['name' => DB::raw('`Nama_lengkap`')]);

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('Nama_lengkap');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('users', 'Nama_lengkap')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('Nama_lengkap')->nullable()->after('name');
            });
        }
    }
};

