<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resident extends Model
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
            'tanggal_lahir' => 'date',
        ];
    }

    /**
     * @return BelongsTo<House, $this>
     */
    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    /**
     * @return HasMany<SocialAidRecipient, $this>
     */
    public function socialAidRecipients(): HasMany
    {
        return $this->hasMany(SocialAidRecipient::class);
    }

    /**
     * SCOPE WAJIB: Membatasi query resident lewat relasi house (Sesuai Bab 4 baru.md).
     *
     * @param  Builder<Resident>  $query
     * @return Builder<Resident>
     */
    public function scopeAccessibleBy(Builder $query, User $user, ?int $selectedRtId = null): Builder
    {
        if ($user->isRwLevel()) {
            return $selectedRtId
                ? $query->whereHas('house', fn ($q) => $q->where('rt_id', $selectedRtId))
                : $query;
        }

        return $query->whereHas('house', fn ($q) => $q->where('rt_id', $user->rt_id));
    }

    /**
     * Local Scope untuk isolasi data otomatis sesuai login pengurus (backward-compatibility).
     *
     * @param  Builder<Resident>  $query
     * @return Builder<Resident>
     */
    public function scopeForCurrentUser(Builder $query): Builder
    {
        $user = auth()->user();
        if ($user && ! $user->isRwLevel() && $user->rt_id) {
            return $query->whereHas('house', function (Builder $q) use ($user) {
                $q->where('rt_id', $user->rt_id);
            });
        }

        return $query;
    }

    /**
     * Aksesor kalkulasi usia warga berdasarkan tanggal lahir.
     */
    public function getUmurAttribute(): int
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : 0;
    }

    /**
     * URL tautan WhatsApp langsung (wa.me) dengan normalisasi kode negara 62.
     */
    public function getWaLinkAttribute(): ?string
    {
        if (! $this->no_wa) {
            return null;
        }

        $clean = preg_replace('/[^0-9]/', '', $this->no_wa);
        if (str_starts_with($clean, '0')) {
            $clean = '62'.substr($clean, 1);
        }

        return 'https://wa.me/'.$clean;
    }

    /**
     * Format nomor telepon / WhatsApp ke format yang mudah dibaca (contoh: 0813-9149-7365).
     */
    public static function formatPhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($digits, '62')) {
            $digits = '0'.substr($digits, 2);
        }

        if (strlen($digits) >= 10 && strlen($digits) <= 13) {
            return substr($digits, 0, 4).'-'.substr($digits, 4, 4).'-'.substr($digits, 8);
        }

        return $phone;
    }

    /**
     * Nomor WhatsApp terformat rapi agar mudah dibaca (contoh: 0813-9149-7365).
     */
    public function getFormattedWaAttribute(): ?string
    {
        return static::formatPhone($this->no_wa);
    }
}
