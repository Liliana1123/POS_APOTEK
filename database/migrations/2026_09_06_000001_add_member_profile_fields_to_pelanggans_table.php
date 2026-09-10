<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->text('alamat')->nullable()->after('telepon');
            $table->date('tanggal_lahir')->nullable()->after('alamat');
            $table->text('keterangan')->nullable()->after('tanggal_lahir');
        });
    }

    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropColumn(['alamat', 'tanggal_lahir', 'keterangan']);
        });
    }
};