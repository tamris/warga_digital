<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * @return BelongsTo<Rt, $this>
     */
    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class);
    }

    public function isAdminRw(): bool
    {
        return $this->role === 'admin_rw';
    }

    public function isKetuaRt(): bool
    {
        return $this->role === 'ketua_rt';
    }

    public function isSekretarisRt(): bool
    {
        return $this->role === 'sekretaris_rt';
    }

    public function isRtOperator(): bool
    {
        return in_array($this->role, ['ketua_rt', 'sekretaris_rt'], true);
    }

    public function isBendahara(): bool
    {
        return $this->role === 'bendahara_rt';
    }

    public function canManageLetters(): bool
    {
        return $this->isAdminRw() || $this->isRtOperator();
    }

    public function canManageHouse(): bool
    {
        return $this->isAdminRw() || in_array($this->role, ['ketua_rt', 'sekretaris_rt'], true);
    }

    public function canDeleteHouse(): bool
    {
        return $this->isAdminRw() || $this->role === 'ketua_rt';
    }

    public function isPengurusRt(): bool
    {
        return in_array($this->role, ['ketua_rt', 'sekretaris_rt', 'bendahara_rt'], true);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin_rw' => 'Ketua RW',
            'ketua_rt' => 'Ketua RT',
            'sekretaris_rt' => 'Sekretaris RT',
            'bendahara_rt' => 'Bendahara RT',
            default => ucfirst(str_replace('_', ' ', $this->role)),
        };
    }

    public function getFormattedWaAttribute(): ?string
    {
        return Resident::formatPhone($this->no_wa);
    }

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
}
