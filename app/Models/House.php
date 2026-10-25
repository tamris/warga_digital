<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class House extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::created(function (self $house): void {
            $house->getOrCreateSocialProfile();
        });
    }

    /**
     * @return BelongsTo<Rt, $this>
     */
    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class);
    }

    /**
     * @return HasMany<Resident, $this>
     */
    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class);
    }

    /**
     * @return HasOne<Resident, $this>
     */
    public function headOfFamily(): HasOne
    {
        return $this->hasOne(Resident::class)
            ->where('status_keluarga', 'kepala_keluarga')
            ->where('status_warga', 'aktif');
    }

    /**
     * @return HasMany<HouseDue, $this>
     */
    public function dues(): HasMany
    {
        return $this->hasMany(HouseDue::class);
    }

    /**
     * @return HasOne<HouseSocialProfile, $this>
     */
    public function socialProfile(): HasOne
    {
        return $this->hasOne(HouseSocialProfile::class);
    }

    /**
     * @return HasMany<SocialAidRecipient, $this>
     */
    public function socialAidRecipients(): HasMany
    {
        return $this->hasMany(SocialAidRecipient::class);
    }

    public function getOrCreateSocialProfile(): HouseSocialProfile
    {
        if ($this->relationLoaded('socialProfile') && $this->socialProfile) {
            return $this->socialProfile;
        }

        $profile = $this->socialProfile()->first();
        if ($profile) {
            $this->setRelation('socialProfile', $profile);

            return $profile;
        }

        $head = $this->headOfFamily;
        $hasIstri = $this->residents()->where('status_warga', 'aktif')->where('status_keluarga', 'istri')->exists();
        $isJandaDuda = $head ? (($head->jenis_kelamin === 'P') || ! $hasIstri) : false;

        $created = $this->socialProfile()->firstOrCreate(
            ['house_id' => $this->id],
            [
                'kategori_ekonomi' => 'menengah',
                'rentang_penghasilan' => '1_5_sampai_3_jt',
                'status_dtks' => 'belum_terdaftar',
                'is_kepala_keluarga_janda_duda' => $isJandaDuda,
            ]
        );

        $this->setRelation('socialProfile', $created);

        return $created;
    }

    /**
     * SCOPE WAJIB: Membatasi query sesuai identitas pengguna yang login (Sesuai Bab 4 baru.md).
     *
     * @param  Builder<House>  $query
     * @return Builder<House>
     */
    public function scopeAccessibleBy(Builder $query, User $user, ?int $selectedRtId = null): Builder
    {
        if ($user->isRwLevel()) {
            return $selectedRtId ? $query->where('rt_id', $selectedRtId) : $query;
        }

        // Pengurus RT dikunci mati ke rt_id miliknya
        return $query->where('rt_id', $user->rt_id);
    }

    /**
     * Local Scope untuk isolasi data otomatis sesuai login (backward-compatibility).
     *
     * @param  Builder<House>  $query
     * @return Builder<House>
     */
    public function scopeForCurrentUser(Builder $query): Builder
    {
        $user = auth()->user();
        if ($user && ! $user->isRwLevel() && $user->rt_id) {
            return $query->where('rt_id', $user->rt_id);
        }

        return $query;
    }

    /**
     * Menghitung metrik rotasi bantuan dan skor pemerataan (fairness score).
     * Mencegah monopoli penerima bansos pada kuota terbatas (rotasi berkeadilan).
     *
     * @return array{
     *     total_received: int,
     *     last_received_at: Carbon|null,
     *     last_program_name: string|null,
     *     days_since_last: int|null,
     *     status: 'never'|'cooldown'|'recent'|'ready',
     *     status_label: string,
     *     adjustment: int,
     *     base_score: int,
     *     fairness_score: int
     * }
     */
    public function getAidRotationMetrics(?int $excludeProgramId = null): array
    {
        $recipients = $this->relationLoaded('socialAidRecipients')
            ? $this->socialAidRecipients
            : $this->socialAidRecipients()->with('program')->get();

        $validRecords = $recipients->filter(function (SocialAidRecipient $r) use ($excludeProgramId) {
            if ($excludeProgramId && $r->program_id === $excludeProgramId) {
                return false;
            }

            // Abaikan usulan yang telah dibatalkan / diganti atau ditolak RW
            if (in_array($r->status_usulan, ['diganti', 'ditolak_rw'], true)) {
                return false;
            }

            return true;
        });

        $totalReceived = $validRecords->count();
        $baseScore = min(100, max(0, (int) ($this->socialProfile?->skor_prioritas ?? 0)));

        if ($totalReceived === 0) {
            $adjustment = 25; // Bonus prioritas giliran utama bagi KK yang belum pernah mendapat bansos
            $fairnessScore = min(100, $baseScore + $adjustment);

            return [
                'total_received' => 0,
                'last_received_at' => null,
                'last_program_name' => null,
                'days_since_last' => null,
                'status' => 'never',
                'status_label' => 'Belum Pernah',
                'adjustment' => $adjustment,
                'base_score' => $baseScore,
                'fairness_score' => $fairnessScore,
            ];
        }

        // Cari riwayat bantuan terakhir yang disetujui / disalurkan
        $lastRecord = $validRecords->sortByDesc(function (SocialAidRecipient $r) {
            return $r->waktu_diterima ?? ($r->program?->tanggal_penyaluran && $r->program->tanggal_penyaluran->isPast() ? $r->program->tanggal_penyaluran : null) ?? $r->created_at;
        })->first();

        $lastProgramName = $lastRecord?->program?->nama_program ?? 'Bansos Sebelumnya';
        $waktuDiterima = $lastRecord?->waktu_diterima;
        $tanggalPenyaluran = $lastRecord?->program?->tanggal_penyaluran;
        $isPending = false;
        $lastDate = null;
        $daysSince = 0;

        if ($waktuDiterima) {
            $lastDate = $waktuDiterima;
            $daysSince = (int) $waktuDiterima->diffInDays(now());
        } elseif ($tanggalPenyaluran && $tanggalPenyaluran->startOfDay()->lte(now()->startOfDay())) {
            $lastDate = $tanggalPenyaluran;
            $daysSince = (int) $tanggalPenyaluran->startOfDay()->diffInDays(now()->startOfDay());
        } else {
            // Terdaftar di program aktif lain yang belum selesai disalurkan (sedang berjalan / dijadwalkan)
            $isPending = true;
            $lastDate = $lastRecord?->created_at ?? now();
            $daysSince = 0;
        }

        if ($isPending) {
            $status = 'cooldown';
            $statusLabel = 'Terdaftar di Program Lain';
            $adjustment = -35;
            $timeLabel = $tanggalPenyaluran ? 'Jadwal '.$tanggalPenyaluran->translatedFormat('d M Y') : 'Menunggu Penyaluran';
        } elseif ($daysSince <= 30) {
            $status = 'cooldown';
            $statusLabel = 'Masa Jeda';
            $adjustment = -35;
            $timeLabel = $daysSince === 0 ? 'Hari ini' : "{$daysSince} hari lalu";
        } elseif ($daysSince <= 60) {
            $status = 'recent';
            $statusLabel = 'Jeda Ringan';
            $adjustment = -15;
            $timeLabel = "{$daysSince} hari lalu";
        } else {
            $status = 'ready';
            $statusLabel = 'Siap Giliran';
            $adjustment = 0;
            $timeLabel = "{$daysSince} hari lalu";
        }

        $fairnessScore = $baseScore + $adjustment;

        // Safety floor: jika warga miskin ekstrem (base_score >= 80), jangan sampai skor anjlok di bawah 50
        if ($baseScore >= 80 && $fairnessScore < 50) {
            $fairnessScore = 50;
        }

        $fairnessScore = max(0, min(100, $fairnessScore));

        return [
            'total_received' => $totalReceived,
            'last_received_at' => $lastDate,
            'last_program_name' => $lastProgramName,
            'days_since_last' => $daysSince,
            'is_pending' => $isPending,
            'time_label' => $timeLabel,
            'status' => $status,
            'status_label' => $statusLabel,
            'adjustment' => $adjustment,
            'base_score' => $baseScore,
            'fairness_score' => $fairnessScore,
        ];
    }
}
