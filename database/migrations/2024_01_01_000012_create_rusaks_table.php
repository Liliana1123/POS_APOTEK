<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rusaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detail_penerimaan_id')->constrained('detail_penerimaans');
            $table->date('tanggal');
            $table->integer('jumlah');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rusaks');
    }
};
