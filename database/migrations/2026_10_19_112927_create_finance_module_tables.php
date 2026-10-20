<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Perbarui ENUM role pada tabel users
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin_rw', 'ketua_rw', 'sekretaris_rw', 'bendahara_rw', 'ketua_rt', 'sekretaris_rt', 'bendahara_rt') NOT NULL DEFAULT 'admin_rw'");
        }

        // 2. Master Tarif Iuran Bulanan per RT
        Schema::create('fee_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rt_id')->constrained('rts')->cascadeOnDelete();
            $table->enum('kategori_tarif', ['standar', 'usaha', 'kosong']);
            $table->decimal('nominal', 12, 2)->default(0);
            $table->string('keterangan', 100)->nullable();
            $table->timestamps();

            $table->unique(['rt_id', 'kategori_tarif'], 'unique_rt_kategori_tarif');
        });

        // 3. Buku Tagihan & Riwayat Pembayaran Iuran Rumah (Monthly Dues)
        Schema::create('house_dues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained('houses')->cascadeOnDelete();
            $table->foreignId('rt_id')->constrained('rts')->cascadeOnDelete();
            $table->unsignedTinyInteger('bulan'); // 1 s/d 12
            $table->unsignedSmallInteger('tahun');
            $table->decimal('nominal', 12, 2);
            $table->enum('status', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->date('tanggal_bayar')->nullable();
            $table->enum('metode_bayar', ['tunai', 'transfer', 'qris'])->default('tunai');
            $table->string('nama_pembayar', 100)->nullable();
            $table->foreignId('received_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('catatan', 255)->nullable();
            $table->timestamps();

            $table->unique(['house_id', 'bulan', 'tahun'], 'unique_house_bulan_tahun');
            $table->index(['rt_id', 'tahun', 'bulan']);
            $table->index(['house_id', 'status']);
        });

        // 4. Buku Kas Operasional RT / RW (Pemasukan & Pengeluaran)
        Schema::create('cash_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rt_id')->nullable()->constrained('rts')->cascadeOnDelete(); // NULL = Kas RW, Ada = Kas RT
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('house_due_id')->nullable()->constrained('house_dues')->nullOnDelete();
            $table->enum('jenis_transaksi', ['masuk', 'keluar']);
            $table->string('kategori', 50);
            $table->decimal('nominal', 12, 2);
            $table->date('tanggal');
            $table->text('keterangan');
            $table->string('bukti_foto', 255)->nullable();
            $table->timestamps();

            $table->index(['rt_id', 'tanggal']);
            $table->index(['jenis_transaksi', 'kategori']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_ledgers');
        Schema::dropIfExists('house_dues');
        Schema::dropIfExists('fee_settings');

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin_rw', 'ketua_rt', 'sekretaris_rt', 'bendahara_rt') NOT NULL DEFAULT 'admin_rw'");
        }
    }
};
