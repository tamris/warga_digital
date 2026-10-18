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
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained('houses')->cascadeOnDelete();
            $table->char('nik', 16)->unique();
            $table->char('no_kk', 16);
            $table->string('nama_lengkap', 150);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']);
            $table->string('pekerjaan', 100);
            $table->enum('status_keluarga', ['kepala_keluarga', 'istri', 'anak', 'famili_lain']);
            $table->enum('status_kependudukan', ['ktp_setempat', 'domisili_luar'])->default('ktp_setempat');
            $table->string('no_wa', 20)->nullable();
            $table->enum('status_warga', ['aktif', 'pindah', 'meninggal'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
