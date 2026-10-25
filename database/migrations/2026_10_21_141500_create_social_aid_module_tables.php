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
        // 1. Profil Sosial Ekonomi Rumah Tangga (1-on-1 dengan tabel houses)
        Schema::create('house_social_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->unique()->constrained('houses')->cascadeOnDelete();
            $table->enum('kategori_ekonomi', ['mampu', 'menengah', 'rentan_miskin', 'miskin_ekstrem'])->default('menengah');
            $table->enum('rentang_penghasilan', ['di_bawah_1_5_jt', '1_5_sampai_3_jt', 'di_atas_3_jt'])->default('1_5_sampai_3_jt');
            $table->enum('status_dtks', ['terdaftar', 'belum_terdaftar', 'proses_usulan'])->default('belum_terdaftar');
            $table->json('bantuan_rutin_pemerintah')->nullable();
            $table->boolean('is_kepala_keluarga_janda_duda')->default(false);
            $table->boolean('ada_anggota_disabilitas_sakit_kronis')->default(false);
            $table->boolean('kondisi_rumah_tidak_layak')->default(false);
            $table->integer('skor_prioritas')->default(0);
            $table->text('catatan_kondisi')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Master Program Bantuan Sosial (Dibuat oleh Pengurus RW)
        Schema::create('social_aid_programs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_program', 150);
            $table->enum('sumber_dana', ['pemerintah_pusat', 'pemda_kelurahan', 'kas_rw', 'csr_donatur']);
            $table->enum('jenis_bantuan', ['sembako_barang', 'uang_tunai']);
            $table->string('detail_bantuan', 150);
            $table->integer('total_kuota')->default(0);
            $table->date('tanggal_penyaluran');
            $table->string('lokasi_pengambilan', 150)->default('Balai RW / Rumah Ketua RT Masing-masing');
            $table->enum('status_program', ['pendataan_rt', 'verifikasi_rw', 'penyaluran', 'selesai'])->default('pendataan_rt');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 3. Alokasi Jatah Kuota per RT dalam Satu Program Bantuan
        Schema::create('social_aid_quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('social_aid_programs')->cascadeOnDelete();
            $table->foreignId('rt_id')->constrained('rts')->cascadeOnDelete();
            $table->integer('jumlah_kuota')->default(0);
            $table->timestamps();

            $table->unique(['program_id', 'rt_id']);
        });

        // 4. Daftar Penerima Bantuan & Log Penyaluran
        Schema::create('social_aid_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('social_aid_programs')->cascadeOnDelete();
            $table->foreignId('rt_id')->constrained('rts')->cascadeOnDelete();
            $table->foreignId('house_id')->constrained('houses')->cascadeOnDelete();
            $table->foreignId('resident_id')->constrained('residents')->cascadeOnDelete();
            $table->string('nomor_urut_kupon', 20)->nullable();
            $table->enum('status_usulan', ['diusulkan_rt', 'disetujui_rw', 'ditolak_rw', 'diganti'])->default('diusulkan_rt');

            // Kolom Khusus Jika Terjadi Penggantian Penerima (Kasus Data Kelurahan Meninggal/Pindah/Mampu)
            $table->enum('alasan_penggantian', ['meninggal', 'pindah_domisili', 'sudah_mampu', 'data_ganda'])->nullable();
            $table->foreignId('digantikan_oleh_resident_id')->nullable()->constrained('residents')->nullOnDelete();
            $table->text('catatan_penggantian')->nullable();

            // Status Penyerahan Hari-H
            $table->enum('status_penyerahan', ['belum_diambil', 'sudah_diterima', 'diwakilkan'])->default('belum_diambil');
            $table->timestamp('waktu_diterima')->nullable();
            $table->string('penerima_aktual', 100)->nullable();
            $table->string('bukti_foto', 255)->nullable();
            $table->foreignId('diserahkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_aid_recipients');
        Schema::dropIfExists('social_aid_quotas');
        Schema::dropIfExists('social_aid_programs');
        Schema::dropIfExists('house_social_profiles');
    }
};
