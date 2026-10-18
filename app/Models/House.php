<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class House extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $guarded = ['id'];

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
     * SCOPE WAJIB: Membatasi query sesuai identitas pengguna yang login (Sesuai Bab 4 baru.md).
     *
     * @param  Builder<House>  $query
     * @return Builder<House>
     */
    public function scopeAccessibleBy(Builder $query, User $user, ?int $selectedRtId = null): Builder
    {
        if ($user->isAdminRw()) {
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
        if ($user && ! $user->isAdminRw() && $user->rt_id) {
            return $query->where('rt_id', $user->rt_id);
        }

        return $query;
    }
}
