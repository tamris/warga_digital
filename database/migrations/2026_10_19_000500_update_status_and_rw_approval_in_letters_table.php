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
        Schema::table('letters', function (Blueprint $table) {
            $table->string('status', 30)->default('menunggu_rw')->change();
            $table->timestamp('signed_by_rt_at')->nullable()->after('tanggal_surat');
            $table->timestamp('signed_by_rw_at')->nullable()->after('signed_by_rt_at');
            $table->text('alasan_tolak')->nullable()->after('alasan_batal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn(['signed_by_rt_at', 'signed_by_rw_at', 'alasan_tolak']);
            $table->enum('status', ['terbit', 'dibatalkan'])->default('terbit')->change();
        });
    }
};
