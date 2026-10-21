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
        Schema::create('letter_types', function (Blueprint $table) {
            $table->id();
            $table->string('nama_keperluan', 150);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rt_id')->constrained('rts')->cascadeOnDelete();
            $table->foreignId('resident_id')->nullable()->constrained('residents')->nullOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users')->restrictOnDelete();

            // Identitas Penomoran Surat
            $table->unsignedInteger('nomor_urut');
            $table->string('nomor_surat', 100);
            $table->year('tahun');

            // Keperluan & Keterangan
            $table->string('jenis_keperluan', 150);
            $table->text('keterangan_keperluan')->nullable();

            // Snapshot Data Warga Saat Surat Dibuat
            $table->string('snapshot_nama', 150);
            $table->char('snapshot_nik', 16);
            $table->char('snapshot_no_kk', 16);
            $table->string('snapshot_ttl', 150);
            $table->enum('snapshot_jenis_kelamin', ['L', 'P']);
            $table->string('snapshot_agama', 30);
            $table->string('snapshot_pekerjaan', 100);
            $table->text('snapshot_alamat');

            // Pejabat Penandatangan
            $table->string('nama_ketua_rt', 100);
            $table->string('nama_ketua_rw', 100)->nullable();

            $table->date('tanggal_surat');
            $table->enum('status', ['terbit', 'dibatalkan'])->default('terbit');
            $table->text('alasan_batal')->nullable();

            $table->timestamps();

            $table->unique(['rt_id', 'nomor_urut', 'tahun'], 'unique_no_surat_per_rt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
        Schema::dropIfExists('letter_types');
    }
};
