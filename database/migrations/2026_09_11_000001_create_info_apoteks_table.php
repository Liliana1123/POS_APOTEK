<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('info_apoteks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_apotek');
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('no_izin_sia')->nullable();
            $table->string('nama_apoteker_pj')->nullable();
            $table->string('no_sipa')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('info_apoteks');
    }
};