<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseSocialProfile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'bantuan_rutin_pemerintah' => 'array',
        'is_kepala_keluarga_janda_duda' => 'boolean',
        'ada_anggota_disabilitas_sakit_kronis' => 'boolean',
        'kondisi_rumah_tidak_layak' => 'boolean',
        'skor_prioritas' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $profile): void {
            $profile->skor_prioritas = $profile->calculatePriorityScore();
            $profile->kategori_ekonomi = $profile->determineKategoriEkonomi($profile->skor_prioritas);
        });
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Hitung Skor Prioritas Bantuan Sosial (0 - 100)
     * Berdasarkan indikator objektif: Pendapatan, Tempat Tinggal, dan Kerentanan Sosial
     */
    public function calculatePriorityScore(): int
    {
        $score = 0;

        // 1. Rentang Penghasilan Total Kepala Keluarga / Bulan (Bobot Max 40 Poin)
        $score += match ($this->rentang_penghasilan) {
            'di_bawah_1_5_jt' => 40,
            '1_5_sampai_3_jt' => 20,
            default => 0, // di_atas_3_jt: 0 Poin
        };

        // 2. Kondisi Fisik Rumah & Tempat Tinggal (Bobot Max 30 Poin)
        // A. Fisik Rumah Tidak Layak Huni (lantai tanah, bilik lapuk, atap bocor)
        if ($this->kondisi_rumah_tidak_layak) {
            $score += 20;
        }

        // B. Status Kepemilikan Bukan Milik Sendiri (Kontrak / Sewa)
        if ($this->house && $this->house->status_huni === 'kontrak') {
            $score += 10;
        }

        // 3. Indikator Kerentanan Sosial & Beban Keluarga (Bobot Max 40 Poin)
        // A. Kepala Keluarga Tunggal (Janda / Duda bertanggungan)
        if ($this->is_kepala_keluarga_janda_duda) {
            $score += 10;
        }

        // B. Ada Anggota Disabilitas Berat / Sakit Kronis
        if ($this->ada_anggota_disabilitas_sakit_kronis) {
            $score += 10;
        }

        // C. Cek Lansia (>= 60 thn) di rumah
        $hasLansia = false;
        if ($this->house) {
            $hasLansia = $this->house->relationLoaded('residents')
                ? $this->house->residents->where('status_warga', 'aktif')->contains(fn ($r) => $r->tanggal_lahir && $r->tanggal_lahir->age >= 60)
                : $this->house->residents()->where('status_warga', 'aktif')->whereDate('tanggal_lahir', '<=', now()->subYears(60))->exists();
        }

        if ($hasLansia) {
            $score += 10;
        }

        // D. Jumlah Anak / Tanggungan Sekolah / Balita >= 3 orang
        $childCount = 0;
        if ($this->house) {
            $childCount = $this->house->relationLoaded('residents')
                ? $this->house->residents->where('status_warga', 'aktif')->where('status_keluarga', 'anak')->count()
                : $this->house->residents()->where('status_warga', 'aktif')->where('status_keluarga', 'anak')->count();
        }

        if ($childCount >= 3) {
            $score += 10;
        }

        return min(100, $score);
    }

    /**
     * Deteksi otomatis status kepala keluarga tunggal (Janda/Duda)
     * jika KK perempuan atau KK laki-laki tanpa istri di rumah.
     */
    public function detectIsJandaDuda(): bool
    {
        if (! $this->house) {
            return false;
        }

        $head = $this->house->headOfFamily
            ?? $this->house->residents()->where('status_warga', 'aktif')->where('status_keluarga', 'kepala_keluarga')->first();

        if (! $head) {
            return false;
        }

        if ($head->jenis_kelamin === 'P') {
            return true;
        }

        $hasIstri = $this->house->residents()
            ->where('status_warga', 'aktif')
            ->where('status_keluarga', 'istri')
            ->exists();

        return ! $hasIstri;
    }

    /**
     * Tentukan Kategori Ekonomi Otomatis Berdasarkan Skor Prioritas (0 - 100)
     */
    public function determineKategoriEkonomi(?int $score = null): string
    {
        $score = $score ?? $this->skor_prioritas ?? $this->calculatePriorityScore();

        return match (true) {
            $score >= 80 => 'miskin_ekstrem',
            $score >= 55 => 'rentan_miskin',
            $score >= 30 => 'menengah',
            default => 'mampu',
        };
    }

    public function getKategoriEkonomiLabelAttribute(): string
    {
        return match ($this->kategori_ekonomi) {
            'miskin_ekstrem' => 'Miskin Ekstrem',
            'rentan_miskin' => 'Rentan Miskin',
            'menengah' => 'Menengah',
            'mampu' => 'Mampu',
            default => ucfirst($this->kategori_ekonomi),
        };
    }

    public function getRentangPenghasilanLabelAttribute(): string
    {
        return match ($this->rentang_penghasilan) {
            'di_bawah_1_5_jt' => '< Rp 1.500.000',
            '1_5_sampai_3_jt' => 'Rp 1.5jt - Rp 3jt',
            'di_atas_3_jt' => '> Rp 3.000.000',
            default => $this->rentang_penghasilan,
        };
    }

    public function getStatusDtksLabelAttribute(): string
    {
        return match ($this->status_dtks) {
            'terdaftar' => 'Terdaftar DTKS',
            'belum_terdaftar' => 'Belum Terdaftar',
            'proses_usulan' => 'Proses Usulan',
            default => ucfirst($this->status_dtks),
        };
    }
}
