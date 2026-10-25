# SPESIFIKASI TEKNIS MODUL 4: PEMETAAN KESEJAHTERAAN (DTKS) & DISTRIBUSI BANSOS TEPAT SASARAN

**Tech Stack:** Laravel 11/12, Livewire 3, Tailwind CSS 3/4, Alpine.js, Barryvdh/Laravel-DomPDF, Maatwebsite/Laravel-Excel

**Filosofi UI/UX:** **Modern Minimalist Functional** (Sleek & Bersih ala SaaS Modern, Anti-AI Slop, Kontras Tinggi, Tombol Lebar & Ramah Pengurus Senior usia 40–65 tahun).

---

## 1. KONSEP & ALUR OPERASIONAL LAPANGAN (DUA JALUR)

Modul ini dirancang untuk menyelesaikan dua masalah utama penyaluran bantuan di tingkat lingkungan: **tuduhan pilih kasih (subjektif)** dan **ketidaksinkronan data pemerintah pusat dengan kondisi riil di RT**.

### Pembagian Wewenang (Hierarki RW & RT)

1. **Tingkat RT (`ketua_rt`, `sekretaris_rt`) — "Mata & Telinga Lapangan"**:
   * Mengisi profil kesejahteraan/ekonomi setiap rumah (`HouseSocialProfile`) di wilayah RT-nya.
   * Mengajukan nama-nama warga calon penerima sesuai jatah kuota yang diberikan RW (berdasarkan rekomendasi **Skor Prioritas Sistem** & **Indikator Pemerataan**).
   * Melakukan verifikasi lapangan: menandai bantuan yang sudah diterima warga, mengunggah foto dokumentasi serah terima, atau mengajukan **Penggantian Penerima (Sanggahan)** jika penerima dari daftar kelurahan ternyata sudah meninggal, pindah, atau sudah kaya/mampu.

2. **Tingkat RW (`ketua_rw`, `sekretaris_rw`, `admin_rw`) — "Koordinator & Pengesah"**:
   * Membuat program bantuan baru (baik bantuan kuota dari Kelurahan/Pusat maupun santunan mandiri dari Kas RW/CSR).
   * Membagi jatah kuota penerima ke masing-masing RT secara proporsional.
   * Memantau, menyetujui (mengunci) daftar usulan dari seluruh RT, dan mengekspor laporan resmi (PDF Daftar Hadir Tanda Tangan Basah, Berita Acara Penggantian, & Excel Rekapitulasi Usulan DTKS untuk Kelurahan).

---

## 2. SKEMA DATABASE DETAIL (MIGRATIONS)

Buat 4 tabel baru yang berelasi langsung dengan tabel `rts`, `houses`, dan `residents` yang sudah ada:

```sql
-- 1. Profil Sosial Ekonomi Rumah Tangga (1-on-1 dengan tabel houses)
CREATE TABLE house_social_profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    house_id BIGINT UNSIGNED UNIQUE NOT NULL,
    kategori_ekonomi ENUM('mampu', 'menengah', 'rentan_miskin', 'miskin_ekstrem') DEFAULT 'menengah',
    rentang_penghasilan ENUM('di_bawah_1_5_jt', '1_5_sampai_3_jt', 'di_atas_3_jt') DEFAULT '1_5_sampai_3_jt',
    status_dtks ENUM('terdaftar', 'belum_terdaftar', 'proses_usulan') DEFAULT 'belum_terdaftar',
    bantuan_rutin_pemerintah JSON NULL, -- Contoh: ["PKH", "BPNT", "KIP", "PBI_JK"]
    is_kepala_keluarga_janda_duda BOOLEAN DEFAULT FALSE,
    ada_anggota_disabilitas_sakit_kronis BOOLEAN DEFAULT FALSE,
    kondisi_rumah_tidak_layak BOOLEAN DEFAULT FALSE,
    skor_prioritas INT DEFAULT 0, -- Dihitung otomatis oleh sistem saat disimpan
    catatan_kondisi TEXT NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- 2. Master Program Bantuan Sosial (Dibuat oleh Pengurus RW)
CREATE TABLE social_aid_programs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_program VARCHAR(150) NOT NULL, -- Contoh: 'Bantuan Beras CBP Bulog 10 Kg - Oktober 2026'
    sumber_dana ENUM('pemerintah_pusat', 'pemda_kelurahan', 'kas_rw', 'csr_donatur') NOT NULL,
    jenis_bantuan ENUM('sembako_barang', 'uang_tunai') NOT NULL,
    detail_bantuan VARCHAR(150) NOT NULL, -- Contoh: 'Beras 10 Kg + Minyak 2 Liter' atau 'Rp 300.000'
    total_kuota INT NOT NULL DEFAULT 0,
    tanggal_penyaluran DATE NOT NULL,
    lokasi_pengambilan VARCHAR(150) DEFAULT 'Balai RW / Rumah Ketua RT Masing-masing',
    status_program ENUM('pendataan_rt', 'verifikasi_rw', 'penyaluran', 'selesai') DEFAULT 'pendataan_rt',
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. Alokasi Jatah Kuota per RT dalam Satu Program Bantuan
CREATE TABLE social_aid_quotas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    program_id BIGINT UNSIGNED NOT NULL,
    rt_id BIGINT UNSIGNED NOT NULL,
    jumlah_kuota INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_program_rt (program_id, rt_id),
    FOREIGN KEY (program_id) REFERENCES social_aid_programs(id) ON DELETE CASCADE,
    FOREIGN KEY (rt_id) REFERENCES rts(id) ON DELETE CASCADE
);

-- 4. Daftar Penerima Bantuan & Log Penyaluran
CREATE TABLE social_aid_recipients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    program_id BIGINT UNSIGNED NOT NULL,
    rt_id BIGINT UNSIGNED NOT NULL,
    house_id BIGINT UNSIGNED NOT NULL,
    resident_id BIGINT UNSIGNED NOT NULL, -- Kepala Keluarga / Perwakilan Penerima
    nomor_urut_kupon VARCHAR(20) NULL, -- Contoh: 'RT03-012'
    status_usulan ENUM('diusulkan_rt', 'disetujui_rw', 'ditolak_rw', 'diganti') DEFAULT 'diusulkan_rt',

    -- Kolom Khusus Jika Terjadi Penggantian Penerima (Kasus Data Kelurahan Meninggal/Pindah/Mampu)
    alasan_penggantian ENUM('meninggal', 'pindah_domisili', 'sudah_mampu', 'data_ganda') NULL,
    digantikan_oleh_resident_id BIGINT UNSIGNED NULL,
    catatan_penggantian TEXT NULL,

    -- Status Penyerahan Hari-H
    status_penyerahan ENUM('belum_diambil', 'sudah_diterima', 'diwakilkan') DEFAULT 'belum_diambil',
    waktu_diterima TIMESTAMP NULL,
    penerima_aktual VARCHAR(100) NULL, -- Nama yang mengambil jika diwakilkan anggota keluarga lain
    bukti_foto VARCHAR(255) NULL, -- Path foto dokumentasi penyerahan
    diserahkan_oleh BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (program_id) REFERENCES social_aid_programs(id) ON DELETE CASCADE,
    FOREIGN KEY (rt_id) REFERENCES rts(id) ON DELETE CASCADE,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE,
    FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE,
    FOREIGN KEY (digantikan_oleh_resident_id) REFERENCES residents(id) ON DELETE SET NULL,
    FOREIGN KEY (diserahkan_oleh) REFERENCES users(id) ON DELETE SET NULL
);
```

---

## 3. ALGORITMA SKORING PRIORITAS & INDIKATOR PEMERATAAN (ANTI-DOBEL)

Sistem menghitung `skor_prioritas` (rentang 0 – 100) secara otomatis setiap kali profil sosial rumah (`HouseSocialProfile`) diperbarui atau anggota keluarga (`Resident`) berubah.

### A. Rumus Perhitungan `calculatePriorityScore(House $house)`

**1. Kategori Ekonomi**

| Kategori | Poin |
|---|---|
| `miskin_ekstrem` | +35 |
| `rentan_miskin` | +25 |
| `menengah` | +5 |
| `mampu` | 0 (otomatis gugur dari rekomendasi bansos) |

**2. Rentang Penghasilan Kepala Keluarga**

| Rentang | Poin |
|---|---|
| `di_bawah_1_5_jt` | +20 |
| `1_5_sampai_3_jt` | +10 |
| `di_atas_3_jt` | 0 |

**3. Indikator Kerentanan Otomatis** (ditarik dari tabel `residents` & checklist)

| Indikator | Poin |
|---|---|
| Terdapat Lansia (> 60 tahun berdasarkan `tanggal_lahir`) di rumah tersebut | +15 |
| Kepala Keluarga berstatus Janda/Duda tidak bekerja atau buruh harian lepas | +10 |
| Ada anggota keluarga disabilitas / sakit kronis menahun | +10 |
| Jumlah anak/tanggungan di rumah (`status_keluarga = 'anak'`) ≥ 3 orang | +5 |
| Kondisi fisik rumah tidak layak huni | +5 |

### B. Badge Indikator Pemerataan (Filter Anti-Tumpang Tindih)

* **Badge Hijau** (`bg-emerald-50 text-emerald-800 border-emerald-300`): *Prioritas Utama: Belum Pernah Dapat Bantuan Tahun Ini.*
* **Badge Kuning** (`bg-amber-50 text-amber-800 border-amber-300`): *Pernah Menerima 1x Tahun Ini ([Nama Program Terakhir]).*
* **Badge Biru** (`bg-sky-50 text-sky-800 border-sky-300`): *Penerima Rutin Pemerintah (PKH / BPNT)* — memberi sinyal ke RT agar memprioritaskan warga miskin non-DTKS terlebih dahulu jika bantuan berasal dari Kas RW/CSR.

---

## 4. RELASI ELOQUENT & SCOPING HAK AKSES

Gunakan pola `scopeForCurrentUser($query)` / `scopeAccessibleBy($query)` agar pengurus RT hanya dapat mengelola data kesejahteraan dan usulan warga di RT-nya sendiri, sedangkan pengurus RW memiliki visibilitas penuh lintas RT:

```php
// App\Models\SocialAidRecipient.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAidRecipient extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'waktu_diterima' => 'datetime',
    ];

    public function program() { return $this->belongsTo(SocialAidProgram::class, 'program_id'); }
    public function rt() { return $this->belongsTo(Rt::class); }
    public function house() { return $this->belongsTo(House::class); }
    public function resident() { return $this->belongsTo(Resident::class); }
    public function replacementResident() { return $this->belongsTo(Resident::class, 'digantikan_oleh_resident_id'); }

    public function scopeForCurrentUser($query, $user = null)
    {
        $user = $user ?? auth()->user();
        if ($user && in_array($user->role, ['ketua_rt', 'sekretaris_rt', 'bendahara_rt']) && $user->rt_id) {
            return $query->where('rt_id', $user->rt_id);
        }
        return $query;
    }
}
```

---

## 5. SPESIFIKASI KOMPONEN LIVEWIRE & UI/UX

Patuhi Design System **Modern Minimalist Functional**: latar `bg-slate-50`, kartu `bg-white border-slate-200`, teks kontras tinggi `text-slate-900`, tinggi tombol minimal `h-11` (44px), dan label tombol berupa teks eksplisit.

### 5.1 `SocialAid/WelfareMappingIndex.php` (Pemetaan Ekonomi & DTKS Warga)

**Fungsi:** Halaman bagi Sekretaris/Ketua RT untuk memetakan status ekonomi rumah tangga dan mengecek siapa warga kurang mampu yang belum masuk DTKS Kelurahan.

**Summary Cards (4 Kartu Atas):**

1. **Total KK Rentan & Miskin** — angka tegas + persentase dari total rumah.
2. **Sudah Terdaftar DTKS** — warga yang sudah tercover bantuan pusat.
3. **Belum Masuk DTKS (Wajib Diusulkan)** — highlight warna amber/warning; daftar warga miskin yang tercecer belum dapat bantuan pemerintah.
4. **KK Lansia / Disabilitas.**

**Fitur Utama:**

* Filter cepat: *Semua Ekonomi*, *Miskin & Rentan*, *Belum Masuk DTKS*, *Penerima PKH/BPNT*.
* Tombol **[ Edit Profil Ekonomi ]** di tiap baris rumah yang membuka Modal Card bersih berisi *segmented cards* (bukan radio button kecil) untuk mencentang kondisi keluarga.
* Tombol Ekspor: **[ Unduh Pengantar Usulan DTKS (.xlsx) ]** — mengunduh daftar warga kategori `miskin_ekstrem` dan `rentan_miskin` yang status DTKS-nya masih `belum_terdaftar` untuk diserahkan ke Operator Kelurahan.

### 5.2 `SocialAid/ProgramIndex.php` & `ProgramCreateModal.php` (Manajemen Program & Kuota)

**Akses Pembuatan:** Khusus `ketua_rw`, `sekretaris_rw`, atau `admin_rw`. Pengurus RT melihat daftar program beserta jatah kuota untuk RT mereka.

**Form Pembuatan Program (`ProgramCreateModal`):**

* Input Nama Program, Sumber Dana, Jenis Bantuan, Detail Bantuan, Tanggal & Lokasi Penyaluran.
* **Tabel Alokasi Kuota per RT:** menampilkan daftar seluruh RT (RT 001, RT 002, dst.) disertai informasi jumlah "KK Rentan/Miskin" di tiap RT sebagai acuan RW dalam membagi angka kuota secara adil.

### 5.3 `SocialAid/ProgramDetail.php` (Usulan Kandidat, Sanggahan, & Penyaluran)

Halaman ini memiliki 3 Tab Navigasi:

#### Tab A: Pemilihan & Usulan Penerima (Kerja Pengurus RT)

* Menampilkan indikator progres kuota RT. Contoh: *Kuota RT 03: Terisi 12 dari 15 KK.*
* Tombol **[ + Pilih Warga dari Rekomendasi Prioritas ]** membuka daftar KK di RT tersebut yang sudah diurutkan otomatis dari Skor Prioritas tertinggi ke terendah.
* Setiap baris menampilkan: Nomor Rumah, Nama Kepala Keluarga, Usia, Pekerjaan, Skor Prioritas (contoh: 85 Poin), dan Badge Riwayat Penerimaan (Hijau/Kuning/Biru).
* Pengurus RT cukup mencentang kotak di samping nama warga hingga kuota terpenuhi, lalu klik **[ Simpan Daftar Usulan RT ]**.

#### Tab B: Penggantian / Sanggahan Penerima (Jika Data Kelurahan Tidak Akurat)

* Jika ada warga penerima yang sudah meninggal, pindah, atau sudah mampu, pengurus RT mengklik tombol **[ Ajukan Pengganti ]** pada baris warga tersebut.
* Modal akan meminta:
  1. **Alasan Penggantian** (Meninggal Dunia, Pindah Domisili, Sudah Mampu / Graduasi).
  2. **Pilih Warga Pengganti** (dropdown pencarian warga aktif di RT yang sama dengan skor prioritas tinggi).
  3. **Catatan Tambahan.**
* Data ini otomatis terekam untuk dicetak menjadi **Berita Acara Penggantian Penerima Bantuan** yang sah untuk Kelurahan.

#### Tab C: Hari-H Penyaluran & Bukti Terima (Mode Lapangan Cepat)

Dirancang khusus agar mudah dioperasikan lewat layar HP saat pembagian sembako/santunan di Balai RW atau rumah RT.

Fitur setiap baris penerima:

* **Tombol WhatsApp Undangan: [ Kirim Kupon WA ]** — mengirim pesan otomatis ke WA warga:

  > Yth. Bapak/Ibu [Nama Warga] (RT [03] / [Blok Rumah]), Anda terdaftar sebagai penerima [Nama Program] ([Detail Bantuan]). Silakan mengambil bantuan pada [Tanggal] di [Lokasi] dengan menunjukkan pesan ini. Nomor Kupon: [Nomor Kupon].

* **Tombol Konfirmasi Serah Terima: [ Tandai Sudah Diterima ]** (tombol hijau tinggi `h-11`) yang membuka modal ringkas untuk opsional unggah foto warga saat menerima paket bantuan.

---

## 6. GENERATOR DOKUMEN CETAK RESMI (PDF A4)

Gunakan `Barryvdh\Laravel-DomPDF` dengan kop surat resmi RW/RT:

### 6.1 Daftar Hadir & Tanda Terima Penyaluran (`Daftar_Hadir_Bansos.pdf`)

* Berisi tabel formal: No, No. Kupon, RT, Nama Kepala Keluarga, NIK (16 Digit), Alamat/Blok, Bantuan Diterima, dan kolom kosong lebar untuk Tanda Tangan Basah Warga.
* Bagian bawah dilengkapi blok pengesahan Ketua RT dan Ketua RW.

### 6.2 Berita Acara Penggantian Penerima (`Berita_Acara_Penggantian.pdf`)

* Dokumen surat resmi yang menerangkan daftar warga awal (yang meninggal/pindah/mampu) beserta daftar warga penggantinya lengkap dengan NIK dan alasan penggantian, siap diserahkan ke pihak Kelurahan/Dinsos.

---

## 7. INSTRUKSI EKSEKUSI UNTUK AI AGENT

1. **Migrasi:** Buat dan jalankan 4 file migrasi database sesuai skema di Bab 2.
2. **Model:** Buat `HouseSocialProfile`, `SocialAidProgram`, `SocialAidQuota`, dan `SocialAidRecipient` lengkap dengan relasi ke `House`, `Resident`, dan `Rt`, serta terapkan method kalkulasi otomatis `calculatePriorityScore()` dan local scope `scopeForCurrentUser()` sesuai Bab 3 & Bab 4.
3. **Livewire:** Buat komponen beserta tampilan Blade untuk:
   * `SocialAid/WelfareMappingIndex` (Pemetaan Ekonomi & DTKS)
   * `SocialAid/ProgramIndex` (Daftar Program Bantuan)
   * `SocialAid/ProgramDetail` (Manajemen Usulan RT, Penggantian, WA Kupon, & Serah Terima)
4. **PDF:** Buat template PDF formal untuk Daftar Hadir Penyaluran dan Berita Acara Penggantian sesuai Bab 6.
5. **Seeder:** Tambahkan data seeder pada `DatabaseSeeder`:
   * Profil sosial ekonomi untuk seluruh rumah contoh (variasikan antara `miskin_ekstrem`, `rentan_miskin`, dan `mampu`).
   * 1 Program Bantuan contoh ("Bantuan Pangan Beras Cadangan Pemerintah 10 Kg - Oktober 2026") dengan kuota terbagi ke tiap RT dan beberapa kandidat penerima.
6. **Konsistensi UI:** Pastikan seluruh elemen antarmuka konsisten dengan Design System Modern Minimalist Functional: bersih, tanpa gradasi neon/AI slop, kontras teks tajam, dan setiap tombol aksi memiliki label teks bahasa Indonesia yang jelas.
