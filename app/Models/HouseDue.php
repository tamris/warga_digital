<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HouseDue extends Model
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
            'bulan' => 'integer',
            'tahun' => 'integer',
            'nominal' => 'decimal:2',
            'tanggal_bayar' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (HouseDue $due) {
            if ($due->status === 'lunas' && empty($due->received_by_name) && auth()->check()) {
                $due->received_by_name = auth()->user()->nama;
            }
        });
    }

    public function getReceiverNameAttribute(): ?string
    {
        return $this->received_by_name ?: ($this->receivedBy?->nama);
    }

    /**
     * @return BelongsTo<House, $this>
     */
    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
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
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    /**
     * @return HasOne<CashLedger, $this>
     */
    public function cashLedger(): HasOne
    {
        return $this->hasOne(CashLedger::class, 'house_due_id');
    }

    public static function getMonthName(int $month): string
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $months[$month] ?? '';
    }

    public function getNamaBulanAttribute(): string
    {
        return self::getMonthName($this->bulan);
    }

    public function getFormattedPeriodeAttribute(): string
    {
        return "{$this->nama_bulan} {$this->tahun}";
    }

    public function getFormattedNominalAttribute(): string
    {
        return 'Rp '.number_format((float) $this->nominal, 0, ',', '.');
    }

    public function getMetodeLabelAttribute(): string
    {
        return match ($this->metode_bayar) {
            'tunai' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'qris' => 'QRIS',
            default => '-',
        };
    }

    /**
     * Scope Isolasi Data Iuran sesuai Tingkat Wilayah Login
     *
     * @param  Builder<HouseDue>  $query
     * @return Builder<HouseDue>
     */
    public function scopeAccessibleBy(Builder $query, User $user, ?int $selectedRtId = null): Builder
    {
        if ($user->isRwLevel()) {
            return $selectedRtId ? $query->where('rt_id', $selectedRtId) : $query;
        }

        return $query->where('rt_id', $user->rt_id);
    }
}
