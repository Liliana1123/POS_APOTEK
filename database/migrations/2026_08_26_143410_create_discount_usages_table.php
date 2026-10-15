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
        Schema::create('discount_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualans')->cascadeOnDelete();
            $table->foreignId('detail_penjualan_id')->constrained('detail_penjualans')->cascadeOnDelete();
            $table->foreignId('barang_id')->nullable()->constrained('barangs')->nullOnDelete();
            $table->string('barang_nama');
            $table->string('jenis'); // 'member' or 'custom'
            $table->foreignId('custom_discount_id')->nullable()->constrained('custom_discounts')->nullOnDelete();
            $table->string('custom_discount_nama')->nullable();
            $table->integer('persentase'); // Actual applied percentage
            $table->decimal('nominal', 12, 2); // Actual applied nominal discount
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_usages');
    }
};
