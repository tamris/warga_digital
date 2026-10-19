# ARSITEKTUR & SPESIFIKASI SISTEM: MODUL IURAN & KAS KEUANGAN RT/RW

**Tech Stack:** Laravel 11/12, Livewire 3, Tailwind CSS 3/4, Alpine.js  
**Lingkup Wilayah:** Multi-Tenant Tingkat Lingkungan (1 RW membawahi banyak RT)  
**Filosofi UI/UX:** **Modern Minimalist Functional** (Sleek ala SaaS modern, anti-AI slop, tombol berlabel kata utuh, kontras tinggi, sangat ramah pengurus usia 40–65 tahun).

---

## 1. STRUKTUR PERAN LENGKAP & PEMISAHAN DOMPET KAS (ISOLASI KEUANGAN)

Dalam lingkungan nyata, kas RT dan kas RW adalah entitas yang terpisah secara pembukuan (*segregated ledgers*).

```text
[ TINGKAT RW (rt_id = NULL) ]
  ├── Ketua RW      : Monitoring agregat seluruh RT, pengesahan kebijakan
  ├── Sekretaris RW : Administrasi & persuratan tingkat RW
  └── Bendahara RW  : Kelola Buku Kas Induk RW (Setoran wajib RT, donasi, dana kelurahan)
          │
          ▼ Setoran Wajib Bulanan RT ke RW
[ TINGKAT RT (rt_id = NOT NULL) ]
  ├── RT 001 ──> Bendahara RT 01 (Buku Iuran Rumah RT 01 & Kas Operasional RT 01)
  ├── RT 002 ──> Bendahara RT 02 (Buku Iuran Rumah RT 02 & Kas Operasional RT 02)
  └── RT 003 ──> Bendahara RT 03 (Buku Iuran Rumah RT 03 & Kas Operasional RT 03)
```

### A. Matriks Kewenangan Keuangan (Finance RBAC)

| Peran (Role) | rt_id | Kelola Iuran Rumah | Buku Kas RT | Buku Kas RW | Lihat Neraca RW |
| :--- | :---: | :---: | :---: | :---: | :---: |
| `bendahara_rt` | Wajib Ada | **Penuh (Input/Tagih)** | **Penuh (Masuk/Keluar)** | Tidak Bisa Akses | Tidak Bisa |
| `ketua_rt` | Wajib Ada | Pantau & Validasi | Pantau & Validasi | Tidak Bisa Akses | Tidak Bisa |
| `sekretaris_rt` | Wajib Ada | Hanya Lihat | Hanya Lihat | Tidak Bisa Akses | Tidak Bisa |
| `bendahara_rw` | **NULL** | Pantau Agregat | Pantau per RT | **Penuh (Kelola Kas RW)** | **Penuh** |
| `ketua_rw` | **NULL** | Pantau Agregat | Pantau per RT | Pantau & Approval | **Penuh** |
| `sekretaris_rw` | **NULL** | Pantau Agregat | Pantau per RT | Hanya Lihat | Ringkasan |

---

## 2. SKEMA DATABASE DETAIL (MIGRATIONS)

Tabel ini memperbarui tabel `users` dan menambahkan tabel master tarif, buku iuran per rumah, serta buku kas umum.

```sql
-- 1. Pembaruan Kolom Role pada Tabel users
ALTER TABLE users MODIFY COLUMN role ENUM(
    'ketua_rw',
    'sekretaris_rw',
    'bendahara_rw',
    'ketua_rt',
    'sekretaris_rt',
    'bendahara_rt'
) NOT NULL;

-- 2. Master Tarif Iuran Bulanan per RT
-- Setiap RT memiliki otonomi menentukan nominal iuran masing-masing kategori rumah
CREATE TABLE fee_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rt_id BIGINT UNSIGNED NOT NULL,
    kategori_tarif ENUM('standar', 'usaha', 'kosong') NOT NULL,
    nominal DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    keterangan VARCHAR(100) NULL, -- Contoh: "Termasuk sampah & ronda"
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (rt_id) REFERENCES rts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_fee_per_rt_category (rt_id, kategori_tarif)
);

-- 3. Buku Tagihan & Riwayat Pembayaran Iuran Rumah (Monthly Dues)
CREATE TABLE house_dues (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    house_id BIGINT UNSIGNED NOT NULL,
    rt_id BIGINT UNSIGNED NOT NULL,
    bulan TINYINT UNSIGNED NOT NULL, -- 1 s/d 12
    tahun YEAR NOT NULL,
    nominal DECIMAL(12, 2) NOT NULL,
    status ENUM('lunas', 'belum_lunas') DEFAULT 'belum_lunas',
    tanggal_bayar DATE NULL,
    metode_bayar ENUM('tunai', 'transfer', 'qris') DEFAULT 'tunai',
    received_by_user_id BIGINT UNSIGNED NULL, -- Bendahara/Ketua penerima
    catatan VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE,
    FOREIGN KEY (rt_id) REFERENCES rts(id) ON DELETE CASCADE,
    FOREIGN KEY (received_by_user_id) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_due_house_month_year (house_id, bulan, tahun)
);

-- 4. Buku Kas Operasional (Pemasukan & Pengeluaran)
-- Berlaku ganda: Kas RT (jika rt_id terisi) atau Kas RW (jika rt_id NULL)
CREATE TABLE cash_ledgers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rt_id BIGINT UNSIGNED NULL, -- NULL = Kas Tingkat RW, Ada Nilai = Kas Milik RT Bersangkutan
    user_id BIGINT UNSIGNED NOT NULL, -- Pengurus pencatat transaksi
    house_due_id BIGINT UNSIGNED NULL, -- Relasi otomatis jika kas masuk berasal dari iuran warga
    jenis_transaksi ENUM('masuk', 'keluar') NOT NULL,
    kategori VARCHAR(50) NOT NULL, 
    -- Contoh Kategori Kas RT: 'iuran_warga', 'gaji_keamanan', 'angkut_sampah', 'lampu_jalan', 'kas_sosial', 'setor_ke_rw'
    -- Contoh Kategori Kas RW: 'setoran_rt', 'donasi_luar', 'bantuan_pemerintah', 'acara_lingkungan', 'operasional_rw'
    nominal DECIMAL(12, 2) NOT NULL,
    tanggal DATE NOT NULL,
    keterangan TEXT NOT NULL,
    bukti_foto VARCHAR(255) NULL, -- Path kuitansi, struk belanja, nota bon
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (rt_id) REFERENCES rts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (house_due_id) REFERENCES house_dues(id) ON DELETE SET NULL
);
```

---

## 3. RELASI ELOQUENT & SCOPING DATA KEUANGAN

```php
// App\Models\User.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public function isRwLevel(): bool
    {
        return in_array($this->role, ['ketua_rw', 'sekretaris_rw', 'bendahara_rw']);
    }

    public function isRtLevel(): bool
    {
        return in_array($this->role, ['ketua_rt', 'sekretaris_rt', 'bendahara_rt']);
    }

    public function canManageRtFinance(): bool
    {
        return in_array($this->role, ['bendahara_rt', 'ketua_rt']);
    }

    public function canManageRwFinance(): bool
    {
        return in_array($this->role, ['bendahara_rw', 'ketua_rw']);
    }
}
```

```php
// App\Models\CashLedger.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CashLedger extends Model
{
    protected $guarded = ['id'];

    public function rt() { return $this->belongsTo(Rt::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function houseDue() { return $this->belongsTo(HouseDue::class); }

    /**
     * Scope Isolasi Kas:
     * - Pengurus RT hanya melihat transaksi kas dengan rt_id miliknya
     * - Bendahara/Ketua RW dapat melihat kas RW (rt_id IS NULL) atau memfilter kas per-RT
     */
    public function scopeAccessibleBy(Builder $query, User $user, ?string $scopeType = 'rw', ?int $targetRtId = null): Builder
    {
        if ($user->isRwLevel()) {
            if ($scopeType === 'rw') {
                return $query->whereNull('rt_id'); // Khusus Kas Induk RW
            }
            return $targetRtId ? $query->where('rt_id', $targetRtId) : $query->whereNotNull('rt_id');
        }

        // Pengurus RT dikunci mutlak ke RT-nya
        return $query->where('rt_id', $user->rt_id);
    }
}
```

---

## 4. ALUR LOGIKA BISNIS & OTOMASI PEMBAYARAN IURAN

### A. Otomasi Pencatatan Kas Masuk Saat Iuran Dilunasi
Ketika Bendahara RT menandai iuran bulan tertentu menjadi `lunas`:
1. Sistem mengupdate baris `house_dues`:
   * `status = 'lunas'`
   * `tanggal_bayar = date('Y-m-d')`
   * `received_by_user_id = auth()->id()`
2. Sistem secara otomatis menyisipkan 1 baris ke tabel `cash_ledgers`:
   * `rt_id = $house->rt_id`
   * `user_id = auth()->id()`
   * `house_due_id = $due->id`
   * `jenis_transaksi = 'masuk'`
   * `kategori = 'iuran_warga'`
   * `nominal = $due->nominal`
   * `tanggal = $due->tanggal_bayar`
   * `keterangan = "Iuran {$namaBulan} {$tahun} - Rumah {$nomor_rumah} ({$nama_kk})"`

### B. Otomasi Pembatalan / Void Iuran
Jika status lunas dibatalkan (misal karena salah klik), sistem menghapus baris kas masuk terkait di `cash_ledgers` yang berelasi dengan `house_due_id` tersebut agar saldo buku kas tetap akurat.

---

## 5. SPESIFIKASI FORMULIR & TAMPILAN UI/UX (RAMAH PENGURUS SENIOR)

### A. Komponen Matriks Penagihan Iuran (`DuesMatrix.php`)
Tampilan berupa tabel grid matriks 12 bulan (Januari – Desember) per rumah dalam satu tahun berjalan.

* **Filter Bar Atas**:
  * Pemilih Tahun: `[ 2026 ]` (Tombol panah ganti tahun besar).
  * Filter RT (hanya tampil jika level RW).
  * Search Bar: Cari nomor rumah atau nama kepala keluarga.
* **Tabel Grid Matriks**:
  * Kolom: Nomor Rumah | Kepala Keluarga | Kategori Tarif | Jan | Feb | Mar | Apr | Mei | Jun | Jul | Agu | Sep | Okt | Nov | Des | Total Terbayar | Aksi.
  * Setiap sel bulan berupa **Tombol Status Berwarna**:
    * **Hijau Terang (`bg-emerald-100 text-emerald-800 border-emerald-300 font-semibold`)**: Menandakan `LUNAS`. Jika diklik menampilkan detail tanggal bayar & penerima.
    * **Merah Muda Lembut (`bg-rose-50 text-rose-700 border-rose-200`)**: Menandakan `BELUM LUNAS`. Menampilkan nominal tagihan (contoh: `Rp50k`).
* **Fitur Bayar Cepat (One-Click Payment Modal)**:
  * Klik kotak bulan -> Muncul modal konfirmasi ringkas dengan teks besar:
    * *"Terima Iuran Bulan Oktober 2026 dari Rumah Blok B2 No. 05 (Bpk. Mukhid) sebesar Rp50.000?"*
    * Pilihan metode bayar: `[ Tunai ]` atau `[ Transfer / QRIS ]`.
    * Tombol simpan besar hijau: `[ Ya, Tandai Lunas & Simpan ]`.
  * **Tombol Kirim Kuitansi WhatsApp**:
    Setelah pembayaran disimpan, muncul tombol hijau dengan ikon WA yang langsung mengarah ke `https://wa.me/62...`:
    ```text
    Yth. Bapak/Ibu [Nama KK],
    Terima kasih. Pembayaran Iuran RT [03] untuk rumah [Blok B2 No. 05] periode [Oktober 2026] sebesar [Rp50.000] telah kami terima pada [19/10/2026].
    Status: LUNAS.
    - Bendahara RT 03 -
    ```

---

### B. Komponen Buku Kas Operasional (`CashLedgerIndex.php`)
* **Widget Kartu Saldo (Bagian Atas)**:
  * 3 Kartu Berdampingan dengan font besar dan kontras tinggi:
    1. **Total Pemasukan Bulan Ini**: Angka warna hijau pekat (`text-emerald-700 text-2xl font-bold`).
    2. **Total Pengeluaran Bulan Ini**: Angka warna merah pekat (`text-rose-700 text-2xl font-bold`).
    3. **Sisa Saldo Kas Aktif**: Latar abu-abu bersih dengan font ekstra tegas (`text-slate-900 text-3xl font-extrabold`).
* **Tombol Aksi Cepat**:
  * `[ + Catat Kas Masuk Lain ]` (Tombol outline hijau dengan border tegas).
  * `[ - Catat Pengeluaran Kas ]` (Tombol merah solid `bg-rose-600 hover:bg-rose-700 text-white`).
* **Modal Catat Pengeluaran**:
  * Tanggal transaksi.
  * Kategori pengeluaran (Dropdown: Gaji Satpam, Petugas Sampah, Lampu Jalan, Sumbangan Kematian/Sosial, Konsumsi Rapat, Setor ke RW, Lainnya).
  * Nominal rupiah (Input angka format ribuan otomatis).
  * Keterangan detail penggunaan dana.
  * Unggah foto nota/kuitansi fisik (kamera HP langsung atau galeri).

---

### C. Komponen Rekap & Laporan Neraca Bulanan (`FinancialReport.php`)
* Laporan ringkasan kas bulanan yang siap diekspor ke PDF standar A4 untuk ditempel di papan pengumuman RT atau dibagikan ke grup WhatsApp warga.
* Menyajikan ringkasan pemasukan iuran, rincian pengeluaran per pos, dan sisa saldo akhir bulan secara transparan.

---

## 6. SPESIFIKASI KOMPONEN LIVEWIRE

1. **`Finance/DuesMatrix.php`**:
   * Menampilkan grid tahunan iuran rumah sesuai `rt_id` login pengguna.
   * Method: `markAsPaid($houseId, $month, $year, $method)` dan `markAsUnpaid($dueId)`.
2. **`Finance/CashLedgerList.php`**:
   * Menampilkan riwayat transaksi kas masuk dan keluar.
   * Toggle tab: `Kas RT` (jika diizinkan) dan `Kas RW` (khusus pengurus RW).
   * Filter rentang tanggal, kategori, dan jenis transaksi.
3. **`Finance/FeeSettingModal.php`**:
   * Komponen pengaturan nominal tarif iuran per kategori rumah untuk masing-masing RT.

---

## 7. INSTRUKSI EKSEKUSI UNTUK AI AGENT

1. Jalankan modifikasi tabel `users` dan migrasi tabel `fee_settings`, `house_dues`, serta `cash_ledgers` sesuai skema di Bab 2.
2. Buat Model Eloquent berserta relasi dan method `scopeAccessibleBy` sesuai Bab 3.
3. Buat Database Seeder:
   * Pengaturan tarif default untuk RT 001, RT 002, dan RT 003 (Standar: Rp50.000, Usaha: Rp100.000, Kosong: Rp20.000).
   * 1 Akun Bendahara RW (`bendahara_rw`) dan 3 Akun Bendahara RT (`bendahara_rt`).
   * Riwayat data pembayaran iuran beberapa bulan ke belakang untuk simulasi grid.
4. Buat tampilan UI Livewire dengan gaya visual modern minimalis tanpa gradasi neon/AI slop: gunakan target klik tombol minimal 44px, teks hitam pekat `text-slate-900`, dan label teks utuh pada setiap tombol aksi.
```

Dokumen spesifikasi teknis di atas sudah lengkap dan siap Anda berikan langsung ke AI agent Antigravity untuk dieksekusi!