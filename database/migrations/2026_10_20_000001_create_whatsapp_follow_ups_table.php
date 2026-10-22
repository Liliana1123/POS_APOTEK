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
        Schema::table('whatsapp_logs', function (Blueprint $table) {
            $table->string('status_follow_up', 50)->default('belum_ditindaklanjuti')->after('status');
            $table->text('catatan_follow_up')->nullable()->after('status_follow_up');
            $table->dateTime('waktu_follow_up')->nullable()->after('catatan_follow_up');
            $table->foreignId('follow_up_user_id')->nullable()->after('waktu_follow_up')->constrained('users')->nullOnDelete();

            $table->index('status_follow_up');
            $table->index('waktu_follow_up');
        });

        Schema::create('whatsapp_follow_up_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_log_id')->constrained('whatsapp_logs')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status_sebelum', 50)->nullable();
            $table->string('status_sesudah', 50);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('whatsapp_log_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_follow_up_histories');

        Schema::table('whatsapp_logs', function (Blueprint $table) {
            $table->dropForeign(['follow_up_user_id']);
            $table->dropIndex(['status_follow_up']);
            $table->dropIndex(['waktu_follow_up']);
            $table->dropColumn([
                'status_follow_up',
                'catatan_follow_up',
                'waktu_follow_up',
                'follow_up_user_id',
            ]);
        });
    }
};
