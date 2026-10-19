<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Letter extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'signed_by_rt_at' => 'datetime',
            'signed_by_rw_at' => 'datetime',
            'nomor_urut' => 'integer',
            'tahun' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Rt, $this>
     */
    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class);
    }

    /**
     * @return BelongsTo<Resident, $this>
     */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Scope Isolasi Data sesuai Role
     *
     * @param  Builder<Letter>  $query
     * @return Builder<Letter>
     */
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
     *
     * @return array{nomor_urut: int, nomor_surat: string, tahun: int}
     */
    public static function generateNextLetterNumber(int $rtId, string $nomorRt, string $nomorRw = '01'): array
    {
        $currentYear = (int) date('Y');
        $currentMonthRoman = self::getRomanMonth((int) date('n'));

        $lastNumber = (int) (self::where('rt_id', $rtId)
            ->where('tahun', $currentYear)
            ->max('nomor_urut') ?? 0);

        $nextNumber = $lastNumber + 1;
        $paddedNumber = str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
        $paddedRt = str_pad((string) ((int) $nomorRt), 2, '0', STR_PAD_LEFT);
        $paddedRw = str_pad((string) ((int) $nomorRw), 2, '0', STR_PAD_LEFT);
        $fullNumber = "{$paddedNumber}/SP/RT.{$paddedRt}/RW.{$paddedRw}/{$currentMonthRoman}/{$currentYear}";

        return [
            'nomor_urut' => $nextNumber,
            'nomor_surat' => $fullNumber,
            'tahun' => $currentYear,
        ];
    }

    public static function getRomanMonth(int $month): string
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return $map[$month] ?? 'I';
    }

    protected static function booted(): void
    {
        static::creating(function (Letter $letter) {
            if (empty($letter->verification_code)) {
                $letter->verification_code = bin2hex(random_bytes(16));
            }
        });
    }

    /**
     * Link WhatsApp instan untuk konfirmasi ke pemohon
     */
    public function getWaShareLinkAttribute(): ?string
    {
        $phone = $this->resident?->no_wa;
        if (! $phone) {
            return null;
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        $msg = "Halo Bpk/Ibu {$this->snapshot_nama}, Surat Pengantar RT Anda dengan No: *{$this->nomor_surat}* untuk keperluan *{$this->jenis_keperluan}* telah selesai diterbitkan oleh Pengurus RT. Silakan hubungi pengurus RT untuk pengambilan dokumen fisik. Terima kasih.";

        return 'https://wa.me/'.$cleanPhone.'?text='.urlencode($msg);
    }

    /**
     * Format tanggal surat dalam Bahasa Indonesia (misal: 18 Oktober 2026).
     */
    public function getFormattedTanggalSuratAttribute(): string
    {
        return Carbon::parse($this->tanggal_surat)->locale('id')->translatedFormat('d F Y');
    }

    /**
     * URL Verifikasi Dokumen Publik / Scan QR
     */
    public function getVerificationUrlAttribute(): string
    {
        $configuredUrl = config('app.url');

        // Jika APP_URL di .env disetel ke domain publik/remote (seperti ngrok/IP LAN),
        // gunakan domain tersebut saat request dibuat dari localhost agar QR Code selalu dapat dipindai oleh HP/luar.
        if ($configuredUrl && ! str_contains($configuredUrl, 'localhost') && ! str_contains($configuredUrl, '127.0.0.1')) {
            if (! request()->hasHeader('host') || request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1') {
                return rtrim($configuredUrl, '/').'/verifikasi-surat/'.$this->verification_code;
            }
        }

        return route('letters.verify', $this->verification_code);
    }

    /**
     * Generate QR Code SVG dalam format Data URI Base64 untuk disisipkan ke Cetak Web dan PDF
     */
    public function getQrCodeSvgBase64Attribute(): string
    {
        $svg = QrCode::size(88)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($this->verification_url);

        return base64_encode((string) $svg);
    }

    /**
     * Label Status yang Manusiawi & Informatif
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu_rt' => 'Pending RT',
            'menunggu_rw' => 'Pending RW',
            'terbit' => 'Terbit (Sah)',
            'ditolak_rt' => 'Ditolak RT',
            'ditolak_rw', 'ditolak' => 'Ditolak RW',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public function isPendingRt(): bool
    {
        return $this->status === 'menunggu_rt';
    }

    public function isPendingRw(): bool
    {
        return $this->status === 'menunggu_rw';
    }

    public function isPublished(): bool
    {
        return $this->status === 'terbit';
    }

    public function isRejected(): bool
    {
        return in_array($this->status, ['ditolak', 'ditolak_rt', 'ditolak_rw'], true);
    }

    public function isCancelled(): bool
    {
        return $this->status === 'dibatalkan';
    }
}
