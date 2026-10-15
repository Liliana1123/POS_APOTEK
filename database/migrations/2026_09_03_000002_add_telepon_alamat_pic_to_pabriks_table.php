<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pabriks', function (Blueprint $table) {
            $table->string('telepon')->nullable()->after('nama');
            $table->text('alamat')->nullable()->after('telepon');
            $table->string('pic')->nullable()->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('pabriks', function (Blueprint $table) {
            $table->dropColumn(['telepon', 'alamat', 'pic']);
        });
    }
};