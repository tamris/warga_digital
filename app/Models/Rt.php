<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Rt extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<House, $this>
     */
    public function houses(): HasMany
    {
        return $this->hasMany(House::class);
    }

    /**
     * @return HasManyThrough<Resident, House, $this>
     */
    public function residents(): HasManyThrough
    {
        return $this->hasManyThrough(Resident::class, House::class);
    }

    /**
     * Nomor WhatsApp Ketua RT terformat rapi (contoh: 0813-9149-7365).
     */
    public function getFormattedWaKetuaAttribute(): ?string
    {
        return Resident::formatPhone($this->no_wa_ketua);
    }
}
