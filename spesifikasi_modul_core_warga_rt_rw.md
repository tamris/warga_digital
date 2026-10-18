# ARSITEKTUR & SPESIFIKASI SISTEM: PORTAL PENGELOLAAN WARGA RW (MULTI-RT)
**Tech Stack:** Laravel 11/12, Livewire 3, Tailwind CSS, Alpine.js  
**Target Pengguna:** Pengurus RW dan Pengurus RT (fokus utama kenyamanan pengguna usia 40-65 tahun)

---

## 1. HIERARKI WILAYAH & PEMBATASAN AKSES (SCOPING)

Sistem berpusat pada tingkat **RW** yang menaungi beberapa **RT** (Multi-Tenant tingkat lingkungan).

```text
[ Pengurus RW (Super Admin / Admin RW) ]
       │
       ├── [ RT 001 ] ──> Houses (Rumah) ──> Residents (Warga)
       ├── [ RT 002 ] ──> Houses (Rumah) ──> Residents (Warga)
       └── [ RT 003 ] ──> Houses (Rumah) ──> Residents (Warga)
```

### Aturan Peran (Role-Based Access Control):
* **Admin RW (`admin_rw`)**:
  * Akses data agregat seluruh RT di lingkungan RW.
  * Memiliki pemilih global (dropdown filter) untuk beralih antar-RT atau melihat seluruh RW.
  * Berhak menambah dan mengelola data master RT serta akun pengurus RT.
* **Pengurus RT (`ketua_rt`, `sekretaris_rt`, `bendahara_rt`)**:
  * Akses terisolasi mutlak hanya untuk data RT masing-masing.
  * Seluruh kueri data rumah dan warga dibatasi otomatis dengan `where('rt_id', auth()->user()->rt_id)`.
  * Tidak dapat melihat, mencari, atau mengedit data milik RT tetangga.

---

## 2. PANDUAN DESAIN ANTARMUKA (ANTI-AI SLOP / RAMAH PENGURUS SENIOR)

* **Kontras Tinggi & Warna Lugas**: Gunakan dasar putih bersih (`bg-white` / `bg-slate-50`), teks abu gelap/hitam pekat (`text-slate-900`). Hindari warna gradasi modern, efek kaca (glassmorphism), animasi melayang liar, atau teks abu muda berukuran kecil.
* **Tipografi & Ukuran Tombol**: Teks isi minimal 16px (`text-base`), label formulir tebal (`font-bold text-slate-800`), dan tombol aksi tinggi minimal 48px (`h-12`) dengan padding lega.
* **Tombol Berlabel Jelas**: Jangan hanya memakai ikon kecil. Sertakan teks label yang jelas (contoh: `[+ Tambah Warga]`, `[Simpan Data]`, `[Tandai Pindah]`).
* **Pemberitahuan Status Jelas**: Setiap kali data tersimpan, terhapus, atau gagal validasi, tampilkan spanduk pesan peringatan sederhana di bagian atas halaman dengan warna tegas (Hijau untuk sukses, Merah untuk gagal).
* **Tata Letak Navigasi**: Gunakan bilah menu atas (topbar) sederhana. Hindari menu sidebar bertingkat yang membingungkan.

---

## 3. SKEMA DATABASE DETAIL (MIGRATIONS)

```sql
-- 1. Master Data RT di Bawah RW
CREATE TABLE rts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nomor_rt VARCHAR(5) NOT NULL, -- Contoh: '001', '002'
    nama_ketua VARCHAR(100) NULL,
    no_wa_ketua VARCHAR(20) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 2. Akun Pengurus Sistem
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rt_id BIGINT UNSIGNED NULL, -- NULL khusus untuk admin_rw
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin_rw', 'ketua_rt', 'sekretaris_rt', 'bendahara_rt') NOT NULL,
    no_wa VARCHAR(20) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (rt_id) REFERENCES rts(id) ON DELETE SET NULL
);

-- 3. Unit Hunian / Rumah (Induk Tagihan & Domisili)
CREATE TABLE houses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rt_id BIGINT UNSIGNED NOT NULL,
    nomor_rumah VARCHAR(50) NOT NULL, -- Contoh: 'Blok B3 No. 14'
    status_huni ENUM('dihuni_pemilik', 'kontrak', 'kosong') DEFAULT 'dihuni_pemilik',
    kategori_tarif ENUM('standar', 'usaha', 'kosong') DEFAULT 'standar',
    catatan TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (rt_id) REFERENCES rts(id) ON DELETE CASCADE
);

-- 4. Master Data Warga
CREATE TABLE residents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    house_id BIGINT UNSIGNED NOT NULL,
    nik CHAR(16) UNIQUE NOT NULL,
    no_kk CHAR(16) NOT NULL,
    nama_lengkap VARCHAR(150) NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    tempat_lahir VARCHAR(100) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    agama ENUM('Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu') NOT NULL,
    pekerjaan VARCHAR(100) NOT NULL,
    status_keluarga ENUM('kepala_keluarga', 'istri', 'anak', 'famili_lain') NOT NULL,
    status_kependudukan ENUM('ktp_setempat', 'domisili_luar') DEFAULT 'ktp_setempat',
    no_wa VARCHAR(20) NULL,
    status_warga ENUM('aktif', 'pindah', 'meninggal') DEFAULT 'aktif',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE
);
```

---

## 4. RELASI ELOQUENT & SCOPING DATA

```php
// App\Models\Rt.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Rt extends Model {
    protected $guarded = ['id'];

    public function users() { return $this->hasMany(User::class); }
    public function houses() { return $this->hasMany(House::class); }
    public function residents() { return $this->hasManyThrough(Resident::class, House::class); }
}

// App\Models\House.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class House extends Model {
    protected $guarded = ['id'];

    public function rt() { return $this->belongsTo(Rt::class); }
    public function residents() { return $this->hasMany(Resident::class); }

    public function headOfFamily() {
        return $this->hasOne(Resident::class)
                    ->where('status_keluarga', 'kepala_keluarga')
                    ->where('status_warga', 'aktif');
    }

    // Local Scope untuk isolasi data otomatis sesuai login
    public function scopeForCurrentUser($query) {
        $user = auth()->user();
        if ($user && $user->role !== 'admin_rw' && $user->rt_id) {
            return $query->where('rt_id', $user->rt_id);
        }
        return $query;
    }
}

// App\Models\Resident.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model {
    protected $guarded = ['id'];

    public function house() { return $this->belongsTo(House::class); }

    public function scopeForCurrentUser($query) {
        $user = auth()->user();
        if ($user && $user->role !== 'admin_rw' && $user->rt_id) {
            return $query->whereHas('house', function($q) use ($user) {
                $q->where('rt_id', $user->rt_id);
            });
        }
        return $query;
    }
}
```

---

## 5. SPESIFIKASI DETAIL FORMULIR INPUT (CORE ENTRY FIELDS)

### A. Formulir Tambah / Edit Unit Rumah (`HouseForm`)
1. **Pilihan RT**:
   * Dropdown (`rt_id`).
   * *Aturan:* Jika login sebagai `admin_rw`, dropdown RT aktif (wajib pilih RT). Jika login sebagai pengurus RT, input otomatis terkunci ke ID RT akun yang sedang login.
2. **Nomor / Blok Rumah**:
   * Input teks biasa (`nomor_rumah`), wajib diisi. Contoh: `"Blok A4 No. 02"`.
3. **Status Hunian**:
   * Radio button tebal (`status_huni`):
     * `Dihuni Pemilik` (default)
     * `Rumah Kontrak / Kost`
     * `Rumah Kosong`
4. **Kategori Tarif Iuran**:
   * Pilihan tombol radio (`kategori_tarif`):
     * `Standar` (Rumah warga biasa)
     * `Tempat Usaha` (Warung/toko/kantor)
     * `Khusus / Kosong`
5. **Catatan Tambahan**:
   * Area teks singkat (`catatan`), opsional. Contoh: *"Pagar hitam, ada anjing peliharaan"*.

---

### B. Formulir Tambah Anggota Warga (`ResidentForm`)
*Terbuka di dalam halaman detail rumah (`HouseDetail`) saat tombol `+ Tambah Anggota Keluarga` diklik.*

1. **Nomor Kartu Keluarga (No. KK)**:
   * Input teks angka (`no_kk`), tepat 16 digit. (Jika rumah sudah memiliki kepala keluarga, otomatis mengisi nilai KK tersebut sebagai default).
2. **Nomor Induk Kependudukan (NIK)**:
   * Input teks angka (`nik`), tepat 16 digit, wajib unik dalam database.
3. **Nama Lengkap**:
   * Input teks kapital standar (`nama_lengkap`), wajib diisi sesuai KTP.
4. **Jenis Kelamin**:
   * Radio button (`jenis_kelamin`): `Laki-laki (L)` atau `Perempuan (P)`.
5. **Tempat Lahir**:
   * Input teks singkat (`tempat_lahir`), wajib diisi.
6. **Tanggal Lahir**:
   * Input kalender tanggal (`tanggal_lahir`), format YYYY-MM-DD.
7. **Agama**:
   * Dropdown (`agama`): `Islam`, `Kristen`, `Katolik`, `Hindu`, `Buddha`, `Konghucu`.
8. **Pekerjaan**:
   * Input teks (`pekerjaan`), contoh: `Karyawan Swasta`, `PNS`, `Wiraswasta`, `Pelajar/Mahasiswa`.
9. **Status dalam Keluarga**:
   * Dropdown (`status_keluarga`):
     * `Kepala Keluarga` (Otomatis menjadi kontak utama unit rumah)
     * `Istri`
     * `Anak`
     * `Famili Lain`
10. **Status Kependudukan**:
    * Pilihan radio (`status_kependudukan`):
      * `KTP Setempat` (Sesuai alamat domisili)
      * `Domisili Luar` (KTP luar wilayah RT/RW)
11. **Nomor WhatsApp**:
    * Input teks angka (`no_wa`), format `08xxxxxxxxxx`.
    * *Validasi:* Wajib diisi jika status keluarga adalah `Kepala Keluarga`.
12. **Status Keaktifan Warga**:
    * Dropdown (`status_warga`): `Aktif` (default), `Pindah Keluar`, atau `Meninggal`.

---

## 6. SPESIFIKASI KOMPONEN LIVEWIRE

### 1. `Houses\HouseIndex.php`
* **Fitur Pencarian:** `wire:model.live.debounce.300ms="search"` (pencarian langsung nomor rumah atau nama kepala keluarga).
* **Filter RT:** Dropdown pemilih RT khusus untuk akun `admin_rw`.
* **Tampilan Tabel:**
  * Kolom: Nomor Rumah | RT | Kepala Keluarga | Kontak WA | Status Huni | Jumlah Jiwa | Aksi.
  * Tombol Aksi: `Detail / Kelola Penghuni`, `Edit Rumah`, `Hapus`.

### 2. `Houses\HouseDetail.php`
* Menampilkan kartu informasi rumah di bagian atas (Nomor Rumah, Wilayah RT, Status Huni, Kategori Tarif).
* Tabel daftar anggota keluarga yang terikat ke rumah tersebut.
* **Modal Tambah/Edit Warga:** Memuat seluruh field kependudukan Bab 5B.
* **Aksi Cepat:**
  * Tombol WhatsApp: Tautan langsung `https://wa.me/62...` untuk menghubungi kepala keluarga.
  * Tombol Mutasi: Mengubah status warga menjadi `Pindah` tanpa menghapus riwayat dari sistem.

### 3. `Residents\ResidentIndex.php`
* Halaman pencarian master data warga tingkat RW.
* Filter cepat:
  * Filter RT (hanya untuk `admin_rw`).
  * Filter Lansia (usia >= 60 tahun).
  * Filter Warga Domisili Luar (musiman/kontrak).
  * Filter Status Aktif / Riwayat Pindah.
* Tombol ekspor data ke format Excel (`residents.xlsx`).

---

## 7. INSTRUKSI EKSEKUSI UNTUK AI AGENT

1. Buat berkas migrasi database secara berurutan sesuai Bab 3 dan jalankan `php artisan migrate`.
2. Buat Model Eloquent berserta relasi dan `scopeForCurrentUser` sesuai Bab 4.
3. Buat Database Seeder (`DatabaseSeeder.php`):
   * 1 RW dengan 3 RT (RT 001, RT 002, RT 003).
   * 1 akun `admin_rw` (bebas memilih RT).
   * Masing-masing 1 akun `ketua_rt` untuk setiap RT.
   * Data contoh 5 rumah di masing-masing RT beserta anggota keluarganya.
4. Buat komponen Livewire 3 beserta tampilan Blade-nya dengan gaya visual Tailwind yang tegas, kontras tinggi, label teks jelas, dan tombol berukuran besar.