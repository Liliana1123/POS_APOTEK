<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('pic', 255)->nullable()->after('alamat');
        });

        Schema::table('pabriks', function (Blueprint $table) {
            $table->dropColumn('pic');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('pic');
        });

        Schema::table('pabriks', function (Blueprint $table) {
            $table->string('pic', 255)->nullable()->after('alamat');
        });
    }
};
