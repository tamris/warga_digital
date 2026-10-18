# ARSITEKTUR & SPESIFIKASI SISTEM: PORTAL PENGELOLAAN WARGA RW (MULTI-RT)

**Tech Stack:** Laravel 11/12, Livewire 3, Tailwind CSS 3/4, Alpine.js  
**Arsitektur Wilayah:** Multi-Tenant Lingkup 1 RW (membawahi banyak RT)  
**Filosofi UI/UX:** **Modern Minimalist Functional** (Sleek, bersih, dan elegan setara SaaS modern seperti Linear/Tailwind UI, Anti-AI Slop, namun secara ergonomi & UX sangat mudah dipahami oleh pengurus usia 40–65 tahun).

---

## 1. DESIGN SYSTEM & UI/UX GUIDELINES (MODERN CLEAN TAPI SENIOR-FRIENDLY)

> **INSTRUKSI MUTLAK UNTUK AI AGENT:**  
> DILARANG menggunakan gradasi neon/ungu glow, efek kaca berlebihan (glassmorphism), animasi mengambang (floating animation), teks abu-abu tipis yang sulit dibaca ("AI slop"), atau tombol misterius yang hanya berupa icon tanpa teks.  
> DILARANG pula membuat tampilan kuno/jadul seperti web era Windows 98.  
> Buat antarmuka setara aplikasi web modern kelas enterprise/SaaS yang mengutamakan fungsi, kerapian border 1px, estetika minimalis, dan keterbacaan tinggi bagi mata pengurus senior.

### A. Palet Warna (Color Palette & Tokens)
* **Background Utama:** `bg-slate-50` (soft off-white netral, sejuk di mata).
* **Surface / Kartu:** `bg-white` dengan border halus `border border-slate-200` dan bayangan tipis `shadow-sm rounded-xl`.
* **Warna Teks (Kontras Tinggi & Tegas):**
  * Judul & Teks Utama: `text-slate-900` (hitam pekat terbaca jelas, kontras tinggi).
  * Label & Keterangan: `text-slate-600 font-medium` (BUKAN abu-abu pudar).
* **Aksen & Brand Color:**
  * **Primary (Utama):** `emerald-600` (Hijau institusi modern yang ramah dan segar) dengan hover `bg-emerald-700` dan focus ring `focus:ring-emerald-500`.
  * **Secondary / Netral:** `slate-100` dengan teks `text-slate-800` dan border `border-slate-300 hover:bg-slate-200`.
  * **Danger (Bahaya/Hapus):** `rose-600` dengan hover `bg-rose-700`.

### B. Tipografi & Skala Teks
* **Font Family:** `font-sans` menggunakan font sans-serif modern berdaya baca tinggi (**Plus Jakarta Sans** atau **Inter**).
* **Ukuran Huruf Terbaca:**
  * Isi tabel & input: minimal `text-sm` (14px) hingga `text-base` (16px).
  * Label form: `text-sm font-semibold text-slate-800 tracking-wide uppercase` (jelas fungsi inputnya).
  * Judul Halaman: `text-2xl font-bold tracking-tight text-slate-900`.

### C. UX & Ergonomi Pengurus Senior
1. **Target Sentuh & Klik Besar (Fitts's Law):**
   * Tinggi input formulir dan tombol aksi minimal `h-11` (44px) atau `h-12` (48px) agar tidak meleset saat diklik mouse atau disentuh jari di layar ponsel/tablet.
2. **Ikon + Label Teks Nyata:**
   * Dilarang membuat tombol hanya berupa ikon tanpa teks. Tombol wajib memiliki label eksplisit:
     * Contoh: `[ + Tambah Rumah Baru ]` bukan hanya ikon `+`.
     * Contoh: `[ Kelola Penghuni ]` bukan hanya ikon mata/pensil.
3. **Formulir Terstruktur & Fokus:**
   * Status aktif input ditandai jelas dengan outline ring: `focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500`.
   * Radio button / pilihan status menggunakan kartu pilihan (segmented cards/pills) yang bisa diklik seluruh areanya, bukan bulatan radio kecil yang sulit dibidik.
4. **Indikator Feedback:**
   * Flash alert yang rapi di pojok atas atau banner sticky dengan icon centang hijau solid dan pesan bahasa Indonesia lugas: *"Data Rumah Blok B No. 04 Berhasil Disimpan"*.

---

## 2. ATURAN ROLE, PERMISSION & DATA ISOLATION (SCOPING EKSPLISIT)

> **PERINGATAN UNTUK AI AGENT:** Pahami hierarki wilayah ini dengan mutlak. Pengurus RT HANYA boleh mengakses data di RT miliknya sendiri (*isolated tenant*). Admin RW memegang kontrol agregat lintas RT.

```text
[ Admin RW (Super Admin / Akses Lintas RT) ]
       │
       ├── [ RT 001 ] ──> Data Terisolasi (Hanya diakses Akun RT 001 & Admin RW)
       ├── [ RT 002 ] ──> Data Terisolasi (Hanya diakses Akun RT 002 & Admin RW)
       └── [ RT 003 ] ──> Data Terisolasi (Hanya diakses Akun RT 003 & Admin RW)
```

### A. Definisi Kolom di Tabel `users`
* `role`: ENUM(`'admin_rw'`, `'ketua_rt'`, `'sekretaris_rt'`, `'bendahara_rt'`)
* `rt_id`: Foreign Key ke `rts.id` (Nullable)

**Aturan Integritas Data Akun:**
* Jika `role === 'admin_rw'`: Kolom `rt_id` **HARUS NULL**. Tidak terikat pada satu RT tertentu karena mengawasi 1 RW penuh.
* Jika `role !== 'admin_rw'`: Kolom `rt_id` **WAJIB DIISI (NOT NULL)**. Menunjukkan RT yang dinaungi.

### B. Matriks Hak Akses (Permission Matrix)

| Aksi / Kemampuan | admin_rw | ketua_rt | sekretaris_rt | bendahara_rt |
| :--- | :---: | :---: | :---: | :---: |
| **Filter Global Pilihan RT** | Ya (Bebas ganti RT) | Terkunci di RT-nya | Terkunci di RT-nya | Terkunci di RT-nya |
| **Kelola Master RT & Akun RT** | Ya | Tidak | Tidak | Tidak |
| **Lihat Data Rumah & Warga** | Semua RT | Hanya RT Sendiri | Hanya RT Sendiri | Hanya RT Sendiri |
| **Tambah / Edit Rumah & Warga** | Ya (Semua RT) | Ya (RT Sendiri) | Ya (RT Sendiri) | Tidak (Hanya Lihat) |
| **Hapus Data Rumah / Warga** | Ya | Ya (RT Sendiri) | Tidak | Tidak |
| **Pencatatan Keuangan / Iuran** | Pantau Saja | Ya (RT Sendiri) | Pantau Saja | Ya (RT Sendiri) |

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
    rt_id BIGINT UNSIGNED NULL, -- NULL khusus untuk role admin_rw
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

-- 4. Master Data Individu Warga
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

## 4. RELASI ELOQUENT & SCOPING OTOMATIS (WAJIB DITERAPKAN)

```php
// App/Models/User.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $guarded = ['id'];

    public function rt() { return $this->belongsTo(Rt::class); }

    public function isAdminRw(): bool { return $this->role === 'admin_rw'; }
    public function isRtOperator(): bool { return in_array($this->role, ['ketua_rt', 'sekretaris_rt']); }
    public function isBendahara(): bool { return $this->role === 'bendahara_rt'; }
}
```

```php
// App/Models/House.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class House extends Model
{
    protected $guarded = ['id'];

    public function rt() { return $this->belongsTo(Rt::class); }
    public function residents() { return $this->hasMany(Resident::class); }

    public function headOfFamily()
    {
        return $this->hasOne(Resident::class)
                    ->where('status_keluarga', 'kepala_keluarga')
                    ->where('status_warga', 'aktif');
    }

    // SCOPE WAJIB: Membatasi query sesuai identitas pengguna yang login
    public function scopeAccessibleBy(Builder $query, User $user, ?int $selectedRtId = null): Builder
    {
        if ($user->isAdminRw()) {
            return $selectedRtId ? $query->where('rt_id', $selectedRtId) : $query;
        }

        // Pengurus RT dikunci mati ke rt_id miliknya
        return $query->where('rt_id', $user->rt_id);
    }
}
```

```php
// App/Models/Resident.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Resident extends Model
{
    protected $guarded = ['id'];

    public function house() { return $this->belongsTo(House::class); }

    // SCOPE WAJIB: Membatasi query resident lewat relasi house
    public function scopeAccessibleBy(Builder $query, User $user, ?int $selectedRtId = null): Builder
    {
        if ($user->isAdminRw()) {
            return $selectedRtId 
                ? $query->whereHas('house', fn($q) => $q->where('rt_id', $selectedRtId)) 
                : $query;
        }

        return $query->whereHas('house', fn($q) => $q->where('rt_id', $user->rt_id));
    }
}
```

---

## 5. SPESIFIKASI FORMULIR INPUT & KOMPONEN UI DETAIL

### A. Formulir Unit Rumah (`HouseFormModal`)
Gunakan Modal Card elegan (`bg-white rounded-xl shadow-lg border border-slate-200 max-w-xl`).

1. **Pilihan RT (`rt_id`)**:
   * Dropdown (`rt_id`) dengan styling modern (`rounded-lg border-slate-300 py-2.5 px-3 h-11 text-base text-slate-900`).
   * Terkunci otomatis (readonly/disabled) untuk akun pengurus RT, terbuka khusus untuk `admin_rw`.
2. **Nomor / Identitas Rumah (`nomor_rumah`)**:
   * Input teks jelas (`nomor_rumah`), placeholder: *"Contoh: Blok B3 No. 14"*, wajib diisi.
3. **Status Hunian (`status_huni`)**:
   * Pilihan tombol segmen (Segmented Cards yang lebar, bukan radio button kecil):
     * `Dihuni Pemilik` (Badge hijau muda: `bg-emerald-50 text-emerald-800 border-emerald-300`)
     * `Kontrak / Kost` (Badge biru muda: `bg-sky-50 text-sky-800 border-sky-300`)
     * `Kosong` (Badge abu netral: `bg-slate-100 text-slate-700 border-slate-300`)
4. **Kategori Tarif Iuran (`kategori_tarif`)**:
   * Pilihan kartu horizontal dengan border rapi: `Standar (Warga Biasa)`, `Tempat Usaha / Ruko`, `Khusus / Kosong`.
5. **Catatan Tambahan (`catatan`)**:
   * Textarea 2 baris untuk catatan fisik rumah (contoh: *"Pagar hitam, ada anjing peliharaan"*).

---

### B. Formulir Anggota Warga (`ResidentFormModal`)
*Terbuka dari halaman detail rumah saat klik tombol `[ + Tambah Anggota ]`.*

1. **Nomor Kartu Keluarga (`no_kk`)**:
   * Input teks 16 digit. Jika rumah sudah memiliki kepala keluarga, otomatis terisi nomor KK kepala keluarga tersebut.
2. **Nomor Induk Kependudukan (`nik`)**:
   * Input teks 16 digit, validasi unik. Font monospace tipis/terbaca (`font-mono text-base`).
3. **Nama Lengkap (`nama_lengkap`)**:
   * Input teks kapital standar sesuai KTP.
4. **Jenis Kelamin (`jenis_kelamin`)**:
   * 2 Tombol segmented besar yang mudah diklik: `[ Laki-laki ]` dan `[ Perempuan ]`.
5. **Tempat & Tanggal Lahir (`tempat_lahir`, `tanggal_lahir`)**:
   * 2 kolom bersisian: Tempat lahir (teks) & Tanggal lahir (date picker HTML5 standar bersih format YYYY-MM-DD).
6. **Agama & Pekerjaan (`agama`, `pekerjaan`)**:
   * Dropdown agama (`Islam`, `Kristen`, `Katolik`, `Hindu`, `Buddha`, `Konghucu`) dan input teks pekerjaan dengan saran autocomplete umum.
7. **Peran / Status dalam Keluarga (`status_keluarga`)**:
   * Dropdown: `Kepala Keluarga`, `Istri`, `Anak`, `Famili Lain`.
   * *Logika UX:* Jika dipilih `Kepala Keluarga`, field No. WhatsApp otomatis diberi tanda `Wajib Diisi`.
8. **Status Kependudukan (`status_kependudukan`)**:
   * Pilihan tombol: `KTP Setempat (Tetap)` atau `Domisili Luar (Pendatang/Kontrak)`.
9. **Nomor WhatsApp (`no_wa`)**:
   * Input teks format Indonesia (`08xxxxxxxxxx`). Ditambahkan keterangan kecil di bawah: *"Digunakan untuk mengirim bukti iuran dan surat pengantar via WA."*
10. **Status Keaktifan Warga (`status_warga`)**:
    * Pilihan: `Aktif`, `Pindah Keluar`, `Meninggal`.

---

## 6. SPESIFIKASI KOMPONEN LIVEWIRE

### 1. `Houses/HouseIndex.php`
* **Logika Query & Mount:**
  ```php
  public string $search = '';
  public ?int $selectedRtId = null;

  public function mount() {
      if (!auth()->user()->isAdminRw()) {
          $this->selectedRtId = auth()->user()->rt_id;
      }
  }

  public function render() {
      $user = auth()->user();
      $houses = House::with(['rt', 'headOfFamily'])
          ->accessibleBy($user, $this->selectedRtId)
          ->when($this->search, function ($query) {
              $query->where('nomor_rumah', 'like', '%' . $this->search . '%')
                    ->orWhereHas('residents', function ($q) {
                        $q->where('nama_lengkap', 'like', '%' . $this->search . '%');
                    });
          })
          ->latest()
          ->paginate(15);

      return view('livewire.houses.house-index', [
          'houses' => $houses,
          'rts' => $user->isAdminRw() ? Rt::orderBy('nomor_rt')->get() : collect(),
      ]);
  }
  ```
* **Tampilan Blade:**
  * **Header Area:** Judul halaman "Daftar Rumah & Hunian" + Tombol hijau solid `[ + Tambah Rumah Baru ]` (`bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2.5 rounded-lg shadow-sm h-11`).
  * **Toolbar / Filter Bar:**
    * Search bar ber-ikon: `wire:model.live.debounce.300ms="search"` dengan placeholder *"Cari nomor rumah atau nama warga..."*.
    * Filter RT (hanya muncul untuk `admin_rw`): Dropdown `Semua RT | RT 001 | RT 002 | ...`.
    * Filter Status: Tab pill bersih (`Semua`, `Dihuni`, `Kontrak`, `Kosong`).
  * **Tabel Bersih:**
    * Header `bg-slate-50 text-slate-700 font-semibold uppercase text-xs tracking-wider`.
    * Row `hover:bg-slate-50/80 transition-colors border-b border-slate-200`.
    * Kolom: Nomor Rumah | RT | Kepala Keluarga | Kontak WhatsApp | Status Huni | Jumlah Jiwa | Aksi.
    * Tombol aksi eksplisit: `[ Kelola Penghuni ]` dan `[ Edit ]`.

### 2. `Houses/HouseDetail.php`
* **Card Profil Rumah:**
  * Menampilkan Nomor Rumah dengan teks besar (`text-xl font-bold text-slate-900`), badge RT, dan status hunian.
  * Tombol WhatsApp langsung ke Kepala Keluarga (`https://wa.me/62...`) dengan label teks hijau rapi: `[ Kirim Pesan WhatsApp ]`.
* **Tabel Anggota Keluarga:**
  * Menampilkan seluruh penghuni di rumah tersebut.
  * Baris Kepala Keluarga ditandai dengan badge khusus: `Kepala Keluarga`.
  * Tombol aksi tiap baris: `[ Edit Data ]` dan `[ Tandai Pindah / Keluar ]`.

### 3. `Residents/ResidentIndex.php`
* Master view data seluruh warga 1 RW untuk pencarian cepat saat pendataan mendesak.
* Filter cepat satu klik: `Semua`, `Lansia (>=60 thn)`, `Balita (<5 thn)`, `Warga KTP Luar (Domisili)`, `Status Aktif`.
* Tombol ekspor data Excel bersih: `[ Unduh Data Excel (.xlsx) ]`.

---

## 7. INSTRUKSI RUNNING UNTUK AI AGENT

1. Jalankan migrasi database sesuai urutan skema di Bab 3 dengan perintah `php artisan migrate`.
2. Implementasikan Model Eloquent beserta relasi dan method `scopeAccessibleBy` sesuai Bab 4.
3. Buat Database Seeder lengkap (`DatabaseSeeder.php`):
   * 1 RW dengan 3 RT (`RT 001`, `RT 002`, `RT 003`).
   * 1 User Admin RW (`admin_rw`, `rt_id = NULL`).
   * 3 User Ketua RT (`ketua_rt`, masing-masing terikat ke `rt_id` RT 001, 002, dan 003).
   * Data contoh minimal 5 rumah di setiap RT lengkap dengan kepala keluarga dan anggota keluarganya.
4. Bangun seluruh komponen Livewire 3 dan Blade view dengan mematuhi **Bab 1 (Design System)**: tampilan modern, bersih, minimalis, kontras tinggi, tombol berlabel kata utuh, dan tanpa animasi berlebih.