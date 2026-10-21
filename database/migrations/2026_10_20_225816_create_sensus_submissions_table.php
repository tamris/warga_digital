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
        Schema::create('sensus_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rt_id')->constrained('rts')->cascadeOnDelete();
            $table->string('kode_tiket', 30)->unique();
            $table->string('nomor_rumah', 50);
            $table->enum('status_huni', ['dihuni_pemilik', 'kontrak', 'kosong'])->default('dihuni_pemilik');
            $table->enum('kategori_tarif', ['standar', 'usaha', 'kosong'])->default('standar');
            $table->text('catatan_rumah')->nullable();
            $table->char('no_kk', 16);
            $table->string('nama_pengirim', 150);
            $table->string('no_wa_pengirim', 20);
            $table->json('data_keluarga');
            $table->enum('status', ['menunggu_verifikasi', 'disetujui', 'ditolak'])->default('menunggu_verifikasi');
            $table->text('catatan_verifikasi')->nullable();
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensus_submissions');
    }
};
