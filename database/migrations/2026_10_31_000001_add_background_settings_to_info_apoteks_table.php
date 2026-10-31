<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('info_apoteks', function (Blueprint $table) {
            $table->string('background_type')->default('default')->after('logo');
            $table->string('background_image')->nullable()->after('background_type');
            $table->unsignedTinyInteger('background_opacity')->default(40)->after('background_image');
        });
    }

    public function down(): void
    {
        Schema::table('info_apoteks', function (Blueprint $table) {
            $table->dropColumn(['background_type', 'background_image', 'background_opacity']);
        });
    }
};

