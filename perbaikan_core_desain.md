# ARSITEKTUR & SPESIFIKASI SISTEM: PORTAL PENGELOLAAN WARGA RW (MULTI-RT)

**Tech Stack:** Laravel 11/12, Livewire 3, Tailwind CSS 3/4, Alpine.js  
**Filosofi UI/UX:** **Modern Minimalist Functional** (Sleek & Bersih ala SaaS Modern, Anti-AI Slop, namun secara UX sangat mudah dipahami oleh pengurus usia 40–65 tahun).

---

## 1. DESIGN SYSTEM & UI/UX GUIDELINES (MODERN CLEAN TAPI SENIOR-FRIENDLY)

> **Catatan untuk AI Agent:** JANGAN membuat desain bertema gradasi neon/ungu glow, rounded ekstrem, floating animations, atau teks abu-abu tipis ("AI slop"). Jangan pula membuat tampilan kuno/jadul seperti era Windows 98. Buat antarmuka setara aplikasi web modern kelas enterprise/SaaS (seperti Linear, Tailwind UI, atau Stripe Dashboard) yang mengutamakan fungsi, estetika minimalis, dan keterbacaan tinggi.

### A. Palet Warna (Color Palette)
* **Background Utama:** `bg-slate-50` (soft off-white, sejuk di mata).
* **Surface / Kartu:** `bg-white` dengan border halus `border border-slate-200` dan bayangan tipis `shadow-sm`.
* **Warna Teks (Kontras Tinggi):**
  * Judul & Teks Utama: `text-slate-900` (hitam pekat terbaca jelas, tidak pudar).
  * Label & Sub-keterangan: `text-slate-600` (BUKAN teks abu-abu terang yang susah dibaca orang tua).
* **Aksen & Brand Color:**
  * **Primary (Utama):** `emerald-600` (Hijau institusi modern yang ramah dan segar) dengan hover `bg-emerald-700`.
  * **Secondary / Netral:** `slate-100` dengan teks `text-slate-800` dan border `border-slate-300`.
  * **Danger (Bahaya/Hapus):** `rose-600` dengan latar `hover:bg-rose-700`.

### B. Tipografi & Skala Teks
* **Font Family:** `font-sans` menggunakan font sans-serif modern berdaya baca tinggi seperti **Plus Jakarta Sans** atau **Inter**.
* **Ukuran Huruf Terbaca:**
  * Isi tabel & input: minimal `text-sm` (14px) hingga `text-base` (16px).
  * Label form: `text-sm font-semibold text-slate-800 tracking-wide uppercase` (jelas fungsi inputnya).
  * Judul Halaman: `text-2xl font-bold tracking-tight text-slate-900`.

### C. UX & Ergonomi Pengurus Senior
1. **Target Sentuh & Klik Besar (Fitts's Law):**
   * Tinggi input formulir dan tombol minimal `h-11` (44px) atau `h-12` (48px) agar tidak meleset saat diklik mouse atau disentuh jari di layar HP.
2. **Ikon + Label Teks Nyata:**
   * Jangan gunakan tombol yang hanya berupa ikon misterius tanpa teks. Tombol wajib memiliki label eksplisit:
     * Contoh: `[ + Tambah Rumah ]` bukan hanya ikon `+`.
     * Contoh: `[ Kelola Penghuni ]` bukan hanya ikon mata.
3. **Formulir Terstruktur & Fokus:**
   * Status aktif input ditandai jelas dengan outline ring: `focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500`.
   * Radio button / pilihan status menggunakan kartu pilihan (segmented controls/cards) yang bisa diklik seluruh areanya, bukan bulatan radio kecil yang sulit dibidik.
4. **Indikator Feedback:**
   * Flash alert yang rapi di pojok atas atau banner sticky dengan icon centang hijau solid dan pesan bahasa Indonesia lugas: *"Data Rumah Blok B No. 04 Berhasil Disimpan"*.

---

## 2. HIERARKI WILAYAH & PEMBATASAN AKSES (SCOPING)

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
  * Memiliki pemilih global (dropdown pill bar di atas) untuk filter RT atau melihat seluruh RW.
  * Berhak menambah dan mengelola data master RT serta akun pengurus RT.
* **Pengurus RT (`ketua_rt`, `sekretaris_rt`, `bendahara_rt`)**:
  * Akses terisolasi mutlak hanya untuk data RT masing-masing.
  * Seluruh kueri data rumah dan warga dibatasi otomatis dengan `where('rt_id', auth()->user()->rt_id)`.
  * Tidak dapat melihat, mencari, atau mengedit data milik RT tetangga.

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

## 4. RELASI ELOQUENT & SCOPING OTOMATIS

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

## 5. SPESIFIKASI FORMULIR INPUT & KOMPONEN UI

### A. Formulir Unit Rumah (`HouseFormModal`)
Gunakan Modal Card elegan (`bg-white rounded-xl shadow-lg border border-slate-200 max-w-xl`).

1. **Pilihan RT**:
   * Dropdown (`rt_id`) dengan styling modern (`rounded-lg border-slate-300 py-2.5 px-3`).
   * Terkunci otomatis untuk akun pengurus RT, terbuka khusus untuk `admin_rw`.
2. **Nomor / Identitas Rumah**:
   * Input teks (`nomor_rumah`), placeholder: *"Contoh: Blok B3 No. 14"*.
3. **Status Hunian (Pilihan Card / Badge Clickable)**:
   * Bukan radio button kecil, melainkan kartu pill:
     * `Dihuni Pemilik` (Badge hijau muda `bg-emerald-50 text-emerald-800 border-emerald-300`)
     * `Kontrak / Kost` (Badge biru muda `bg-sky-50 text-sky-800 border-sky-300`)
     * `Kosong` (Badge abu netral `bg-slate-100 text-slate-700 border-slate-300`)
4. **Kategori Tarif Iuran**:
   * Radio group horizontal dengan border rapi: `Standar`, `Tempat Usaha`, `Khusus/Kosong`.
5. **Catatan**:
   * Textarea 2 baris untuk penanda rumah (`catatan`).

---

### B. Formulir Anggota Warga (`ResidentFormModal`)
*Terbuka dari halaman detail rumah saat klik tombol `[ + Tambah Anggota ]`.*

1. **Nomor Kartu Keluarga (No. KK)**:
   * Input teks 16 digit. Otomatis terisi no KK kepala keluarga jika sudah ada.
2. **NIK (Nomor Induk Kependudukan)**:
   * Input teks 16 digit, validasi unik. Font monospace tipis/terbaca (`font-mono text-base`).
3. **Nama Lengkap**:
   * Input teks kapital standar sesuai KTP.
4. **Jenis Kelamin**:
   * 2 Tombol segmented besar: `[ Laki-laki ]` dan `[ Perempuan ]`.
5. **Tempat & Tanggal Lahir**:
   * 2 kolom bersisian: Tempat lahir (teks) & Tanggal lahir (date picker HTML5 standar bersih).
6. **Agama & Pekerjaan**:
   * Dropdown agama dan input teks pekerjaan dengan saran autocomplete umum.
7. **Peran / Status Keluarga**:
   * Dropdown: `Kepala Keluarga`, `Istri`, `Anak`, `Famili Lain`.
   * *Logika UX:* Jika dipilih `Kepala Keluarga`, field No. WhatsApp otomatis diberi tanda `Wajib Diisi`.
8. **Status Kependudukan**:
   * Pilihan tombol: `KTP Setempat (Tetap)` atau `Domisili Luar (Pendatang/Kontrak)`.
9. **Nomor WhatsApp**:
   * Input teks format Indonesia (`08xxxxxxxxxx`). Ditambahkan keterangan kecil di bawah: *"Digunakan untuk mengirim bukti iuran dan surat pengantar."*
10. **Status Warga**:
    * Pilihan: `Aktif`, `Pindah Keluar`, `Meninggal`.

---

## 6. SPESIFIKASI KOMPONEN LIVEWIRE

### 1. `Houses/HouseIndex.php`
* **Header Area:** 
  * Judul: "Daftar Rumah & Hunian"
  * Kanan: Tombol hijau solid `[ + Tambah Rumah Baru ]` (`bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2.5 rounded-lg shadow-sm`).
* **Toolbar / Filter Bar:**
  * Search bar ber-icon kaca pembesar: `wire:model.live.debounce.300ms="search"` dengan placeholder *"Cari nomor rumah atau nama warga..."*.
  * Filter RT (hanya tampil jika login `admin_rw`): Dropdown `Semua RT | RT 001 | RT 002 | ...`.
  * Filter Status: Tab pill bersih (`Semua`, `Dihuni`, `Kontrak`, `Kosong`).
* **Table Design:**
  * Clean modern table: Header `bg-slate-50 text-slate-700 font-semibold uppercase text-xs tracking-wider`.
  * Row `hover:bg-slate-50/80 transition-colors border-b border-slate-200`.
  * Kolom: Rumah | RT | Kepala Keluarga | Kontak WhatsApp | Status | Jiwa | Aksi.
  * Tombol aksi langsung terlihat (bukan dropdown tersembunyi): tombol teks `[ Kelola Penghuni ]` dan `[ Edit ]`.

### 2. `Houses/HouseDetail.php`
* **Card Profil Rumah:**
  * Menampilkan Nomor Rumah dengan teks besar (`text-xl font-bold`), badge RT, dan status hunian.
  * Tombol cepat WhatsApp ke Kepala Keluarga dengan logo WA hijau rapi yang langsung membuka obrolan chat.
* **Tabel Anggota Keluarga:**
  * Menampilkan seluruh penghuni di rumah tersebut.
  * Baris Kepala Keluarga ditandai dengan badge khusus: `Kepala Keluarga`.
  * Tombol aksi tiap baris: `[ Edit Data ]` dan `[ Tandai Pindah / Keluar ]`.

### 3. `Residents/ResidentIndex.php`
* Master view data warga 1 RW untuk pencarian cepat saat pendataan mendesak.
* Filter cepat: Lansia (>60 thn), Balita (<5 thn), Warga KTP Luar (Domisili), Status Aktif.
* Tombol ekspor Excel bersih `[ Unduh Data Excel (.xlsx) ]`.

---

## 7. INSTRUKSI RUNNING UNTUK AI AGENT

1. Jalankan migrasi database sesuai urutan skema di Bab 3.
2. Implementasikan Model Eloquent beserta relasi dan local scope `scopeForCurrentUser` di Bab 4.
3. Buat Database Seeder lengkap:
   * 1 RW dengan 3 RT (`RT 001`, `RT 002`, `RT 003`).
   * 1 User Admin RW (`admin_rw`).
   * 3 User Ketua RT (`ketua_rt`) untuk masing-masing RT.
   * Data contoh 5 rumah di tiap RT lengkap dengan kepala keluarga dan anggota keluarganya.
4. Buat seluruh Blade view dengan mematuhi **Bab 1 (Design System)**: tampilan modern, bersih, minimalis, kontras tegas, tombol berlabel kata utuh, dan tanpa animasi berlebih.