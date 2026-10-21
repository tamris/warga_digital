<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensusSubmission extends Model
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
            'data_keluarga' => 'array',
            'verified_at' => 'datetime',
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
     * @return BelongsTo<User, $this>
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    /**
     * Scope akses per wilayah pengguna
     *
     * @param  Builder<SensusSubmission>  $query
     * @return Builder<SensusSubmission>
     */
    public function scopeAccessibleBy(Builder $query, User $user, ?int $selectedRtId = null): Builder
    {
        if ($user->isRwLevel()) {
            return $selectedRtId
                ? $query->where('rt_id', $selectedRtId)
                : $query;
        }

        return $query->where('rt_id', $user->rt_id);
    }

    /**
     * Hitung total anggota keluarga yang didaftarkan dalam submission ini
     */
    public function getTotalAnggotaAttribute(): int
    {
        return is_array($this->data_keluarga) ? count($this->data_keluarga) : 0;
    }

    /**
     * Ambil data Kepala Keluarga dari JSON data_keluarga
     *
     * @return array<string, mixed>|null
     */
    public function getKepalaKeluargaAttribute(): ?array
    {
        if (! is_array($this->data_keluarga)) {
            return null;
        }

        foreach ($this->data_keluarga as $member) {
            if (($member['status_keluarga'] ?? '') === 'kepala_keluarga') {
                return $member;
            }
        }

        return $this->data_keluarga[0] ?? null;
    }
}
