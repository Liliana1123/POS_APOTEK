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
        Schema::create('riwayat_penerimaans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('penerimaan_id')
                ->constrained('penerimaans')
                ->cascadeOnDelete();

            $table->foreignId('detail_pesanan_penerimaan_id')
                ->constrained('detail_pesanan_penerimaans')
                ->cascadeOnDelete();

            $table->foreignId('detail_penerimaan_id')
                ->nullable()
                ->constrained('detail_penerimaans')
                ->nullOnDelete();

            $table->string('jenis', 20);

            $table->integer('jumlah');

            $table->date('tanggal');

            $table->text('keterangan')->nullable();

            $table->foreignId('user_id')
                ->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_penerimaans');
    }
};
