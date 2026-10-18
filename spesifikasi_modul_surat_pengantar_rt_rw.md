# ARSITEKTUR & SPESIFIKASI SISTEM: MODUL SURAT PENGANTAR RT/RW
**Tech Stack:** Laravel 11/12, Livewire 3, Tailwind CSS 3/4, DomPDF / Spatie Browsershot  
**Lingkup Wilayah:** Multi-Tenant Lingkup 1 RW (Hierarki: RW -> RT -> Surat Pengantar)  
**Filosofi Desain:** **Modern Minimalist Functional** (Sleek ala SaaS modern, ramah pengguna usia 40–65 tahun, anti-AI slop, tombol berlabel jelas).

---

## 1. ALUR KERJA OPERASIONAL DI LAPANGAN (REAL-WORLD WORKFLOW)

Surat pengantar berfungsi sebagai bukti rekomendasi resmi dari lingkungan warga sebelum diajukan ke tingkat Kelurahan, Kecamatan, Disdukcapil, atau Kepolisian.

### Alur Kerja Operasional:
1. **Permintaan Warga**: Warga menghubungi Sekretaris atau Ketua RT (via WhatsApp atau datang langsung) menyampaikan keperluannya.
2. **Pembuatan Cepat oleh Pengurus RT**:
   * Pengurus RT (Sekretaris / Ketua RT) membuka modul *Buat Surat Pengantar*.
   * Pengurus cukup mengetik nama pemohon di kolom pencarian warga.
   * Data kependudukan (**Nama Lengkap, NIK, No. KK, Tempat/Tgl Lahir, Jenis Kelamin, Agama, Pekerjaan, Alamat**) otomatis terisi (*auto-populated*).
   * Pengurus memilih jenis keperluan dari daftar siap pakai atau mengetik keperluan spesifik.
3. **Penerbitan Nomor Surat Otomatis**:
   * Sistem otomatis membangkitkan nomor surat urut tahunan berdasarkan kode RT & RW.
4. **Cetak Fisik / Ekspor PDF**:
   * Pengurus mencetak surat dalam format kertas A4/F4 resmi.
   * Dokumen berisi dua blok legalisasi tanda tangan:
     * **Sisi Kanan**: Ketua RT pembuat surat (TTD + stempel basah RT).
     * **Sisi Kiri**: Mengetahui Ketua RW (TTD + stempel basah RW).
   * Opsi tambahan: Mengirimkan berkas PDF langsung ke WhatsApp warga via tautan instan.

---

## 2. HAK AKSES PERAN (PERMISSION MATRIX)

| Tindakan / Aksi | admin_rw | ketua_rt | sekretaris_rt | bendahara_rt |
| :--- | :---: | :---: | :---: | :---: |
| **Buat Draf Surat Baru** | Ya (Pilih RT) | Ya (RT Sendiri) | Ya (RT Sendiri) | Tidak |
| **Cetak / Ekspor PDF Surat** | Ya (Semua RT) | Ya (RT Sendiri) | Ya (RT Sendiri) | Tidak |
| **Cetak Ulang Arsip Surat** | Ya (Semua RT) | Ya (RT Sendiri) | Ya (RT Sendiri) | Tidak |
| **Batalkan / Arsipkan Surat** | Ya | Ya (RT Sendiri) | Tidak | Tidak |
| **Lihat Riwayat & Register Surat** | Pantau Seluruh RW | Hanya RT Sendiri | Hanya RT Sendiri | Hanya RT Sendiri (Read-Only) |

---

## 3. SKEMA DATABASE DETAIL (MIGRATIONS)

Tabel ini berelasi dengan tabel `rts`, `residents`, dan `users` yang sudah ada di modul data warga.

```sql
-- Tabel Master Template / Jenis Keperluan Surat Pengantar
CREATE TABLE letter_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_keperluan VARCHAR(150) NOT NULL, -- Contoh: "Pembuatan KTP Baru", "Pengantar SKCK"
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Tabel Arsip & Penerbitan Surat Pengantar
CREATE TABLE letters (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rt_id BIGINT UNSIGNED NOT NULL,
    resident_id BIGINT UNSIGNED NOT NULL,
    created_by_user_id BIGINT UNSIGNED NOT NULL,
    
    -- Identitas Penomoran Surat
    nomor_urut INT UNSIGNED NOT NULL, -- Nomor urut dalam tahun berjalan (contoh: 1, 2, 3...)
    nomor_surat VARCHAR(100) NOT NULL, -- Format lengkap: 005/SP/RT.002/RW.005/X/2026
    tahun YEAR NOT NULL,
    
    -- Keperluan & Keterangan
    jenis_keperluan VARCHAR(150) NOT NULL,
    keterangan_keperluan TEXT NULL, -- Detail keperluan tambahan jika ada
    
    -- Snapshot Data Warga Saat Surat Dibuat (Data historis tetap aman jika master warga diubah)
    snapshot_nama VARCHAR(150) NOT NULL,
    snapshot_nik CHAR(16) NOT NULL,
    snapshot_no_kk CHAR(16) NOT NULL,
    snapshot_ttl VARCHAR(150) NOT NULL,
    snapshot_jenis_kelamin ENUM('L', 'P') NOT NULL,
    snapshot_agama VARCHAR(30) NOT NULL,
    snapshot_pekerjaan VARCHAR(100) NOT NULL,
    snapshot_alamat TEXT NOT NULL,
    
    -- Pejabat Penandatangan
    nama_ketua_rt VARCHAR(100) NOT NULL,
    nama_ketua_rw VARCHAR(100) NULL,
    
    tanggal_surat DATE NOT NULL,
    status ENUM('terbit', 'dibatalkan') DEFAULT 'terbit',
    alasan_batal TEXT NULL,
    
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (rt_id) REFERENCES rts(id) ON DELETE CASCADE,
    FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE RESTRICT,
    UNIQUE KEY unique_no_surat_per_rt (rt_id, nomor_urut, tahun)
);
```

---

## 4. RELASI ELOQUENT & GENERATOR NOMOR SURAT OTOMATIS

```php
// App\Models\Letter.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Letter extends Model
{
    protected $guarded = ['id'];

    public function rt() { return $this->belongsTo(Rt::class); }
    public function resident() { return $this->belongsTo(Resident::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by_user_id'); }

    // Scope Isolasi Data sesuai Role
    public function scopeAccessibleBy(Builder $query, User $user, ?int $selectedRtId = null): Builder
    {
        if ($user->isAdminRw()) {
            return $selectedRtId ? $query->where('rt_id', $selectedRtId) : $query;
        }

        return $query->where('rt_id', $user->rt_id);
    }

    /**
     * Helper Generator Nomor Surat Otomatis
     * Contoh hasil: 014/SP/RT.002/RW.005/X/2026
     */
    public static function generateNextLetterNumber(int $rtId, string $nomorRt, string $nomorRw = '005'): array
    {
        $currentYear = (int) date('Y');
        $currentMonthRoman = self::getRomanMonth((int) date('n'));

        $lastNumber = self::where('rt_id', $rtId)
            ->where('tahun', $currentYear)
            ->max('nomor_urut') ?? 0;

        $nextNumber = $lastNumber + 1;
        $paddedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $fullNumber = "{$paddedNumber}/SP/RT.{$nomorRt}/RW.{$nomorRw}/{$currentMonthRoman}/{$currentYear}";

        return [
            'nomor_urut' => $nextNumber,
            'nomor_surat' => $fullNumber,
            'tahun' => $currentYear,
        ];
    }

    private static function getRomanMonth(int $month): string
    {
        $map = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 
                7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        return $map[$month] ?? 'I';
    }
}
```

---

## 5. SPESIFIKASI FORMULIR PENGINPUTAN SURAT (`LetterCreateModal / Page`)

Antarmuka pembuatan surat dirancang seringkas mungkin:

### Langkah 1: Pilih Pemohon (Warga)
* Input pencarian pintar (`wire:model.live.debounce.300ms="searchResident"`).
* Pengurus mengetik nama atau NIK. Sistem memunculkan daftar nama warga RT bersangkutan.
* Saat warga dipilih, tampil kartu ringkas konfirmasi:
  * Nama Lengkap & NIK
  * Nomor Rumah / Blok
  * No. WhatsApp aktif (dengan ikon WhatsApp hijau)

### Langkah 2: Detail Surat & Keperluan
1. **Tanggal Surat**:
   * Input pemilih tanggal (default: tanggal hari ini).
2. **Pilihan Jenis Keperluan**:
   * Dropdown cepat pilihan standar:
     * *Pengantar Pembuatan / Perpanjangan KTP*
     * *Pengantar Perubahan / Pembuatan Kartu Keluarga (KK)*
     * *Pengantar Surat Keterangan Catatan Kepolisian (SKCK)*
     * *Surat Keterangan Domisili Warga*
     * *Surat Keterangan Kematian*
     * *Surat Keterangan Usaha (SKU)*
     * *Surat Pengantar Nikah (N1-N4)*
     * *Lainnya (Tulis Manual)*
3. **Keterangan Tambahan / Spesifikasi Keperluan**:
   * Area teks singkat untuk memperjelas tujuan surat (contoh: *"Untuk persyaratan melamar pekerjaan di PT ABC"*).
4. **Pratinjau Nomor Surat**:
   * Teks tebal read-only: `014/SP/RT.002/RW.005/X/2026`.

### Langkah 3: Tombol Aksi
* `[ Batal ]` (Warna netral `border-slate-300 text-slate-700`).
* `[ Terbitkan & Siapkan Cetak ]` (Warna hijau solid `bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm`).

---

## 6. LAYOUT & FORMAT CETAK DOKUMEN RESMI (PDF TEMPLATE)

Dokumen PDF wajib berukuran **A4** tegak (*Portrait*), margin standar 2 cm di semua sisi, tipografi resmi (*Times New Roman* atau *Arial*).

### Struktur Dokumen:
1. **Kop Surat (Header)**:
   * Baris 1: **RUKUN WARGA 005**
   * Baris 2: **RUKUN TETANGGA 002**
   * Baris 3: Kelurahan Mekarsari, Kecamatan Cidadap, Kota Tasikmalaya
   * Garis pemisah kop surat ganda tipis-tebal (`border-b-2 border-black`).
2. **Judul Dokumen**:
   * **<u>SURAT PENGANTAR</u>** (Font 14pt, Bold, Center)
   * Nomor: `014/SP/RT.002/RW.005/X/2026`
3. **Paragraf Pembuka**:
   * *"Yang bertanda tangan di bawah ini Ketua Rukun Tetangga 002 Rukun Warga 005, dengan ini menerangkan bahwa:"*
4. **Tabel Biodata Pemohon**:
   * Nama Lengkap : `[snapshot_nama]`
   * Nomor NIK : `[snapshot_nik]`
   * Nomor KK : `[snapshot_no_kk]`
   * Tempat / Tgl Lahir : `[snapshot_ttl]`
   * Jenis Kelamin : `[snapshot_jenis_kelamin]`
   * Agama : `[snapshot_agama]`
   * Pekerjaan : `[snapshot_pekerjaan]`
   * Alamat Lengkap : `[snapshot_alamat]`
5. **Paragraf Keperluan**:
   * *"Orang tersebut di atas adalah benar-benar warga kami yang berdomisili di lingkungan RT.002 RW.005 dan bermaksud untuk mengurus:"*
   * **[jenis_keperluan]** (keterangan: *[keterangan_keperluan]*)
6. **Paragraf Penutup**:
   * *"Demikian surat pengantar ini kami buat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya."*
7. **Blok Tanda Tangan (2 Kolom Bawah)**:
   * Kolom Kiri:
     ```text
     Mengetahui,
     Ketua RW 005

     ( ..................................... )
     ```
   * Kolom Kanan:
     ```text
     Tasikmalaya, 18 Oktober 2026
     Ketua RT 002

     ( [nama_ketua_rt] )
     ```

---

## 7. SPESIFIKASI KOMPONEN LIVEWIRE

### 1. `Letters/LetterList.php`
* Menampilkan buku agenda / register nomor surat yang pernah terbit.
* **Filter Bar**:
  * Filter RT (khusus `admin_rw`).
  * Filter Tahun & Bulan berjalan.
  * Search bar (Cari nomor surat atau nama pemohon).
* **Kolom Tabel**:
  * Nomor Surat | Tanggal | Pemohon (NIK & Nama) | RT | Keperluan | Aksi.
* **Tombol Aksi**:
  * `[ Cetak PDF ]`: Membuka tab baru tampilan PDF siap cetak.
  * `[ Kirim WA ]`: Tautan instan WhatsApp ke pemohon berisi konfirmasi nomor surat dan link dokumen.
  * `[ Batalkan ]`: Hanya untuk Ketua RT / Admin RW jika terjadi salah terbit.

### 2. `Letters/LetterCreate.php`
* Halaman formulir input cepat pembuatan surat pengantar sesuai spesifikasi Bab 5.
* Validasi pemohon aktif dan pencegahan nomor ganda.

---

## 8. INSTRUKSI EKSEKUSI UNTUK AI AGENT

1. Buat berkas migrasi database tabel `letter_types` dan `letters` sesuai Bab 3, lalu jalankan `php artisan migrate`.
2. Pasang package PDF generator resmi di Laravel (misal: `barryvdh/laravel-dompdf`).
3. Buat template Blade PDF khusus cetak di `resources/views/letters/pdf-template.blade.php` sesuai aturan layout Bab 6.
4. Buat komponen Livewire `LetterList` dan `LetterCreate` dengan gaya UI minimalis, kontras tinggi, dan tombol berlabel jelas sesuai spesifikasi.
5. Siapkan Seeder awal untuk daftar `letter_types` umum kependudukan.
```

Dokumen spesifikasi modul surat di atas sudah lengkap dan siap Anda berikan langsung ke AI agent Antigravity sebagai berkas panduan lanjutan.