<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashLedger extends Model
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
            'nominal' => 'decimal:2',
            'tanggal' => 'date',
            'accepted_at' => 'datetime',
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<HouseDue, $this>
     */
    public function houseDue(): BelongsTo
    {
        return $this->belongsTo(HouseDue::class);
    }

    /**
     * @return BelongsTo<CashLedger, $this>
     */
    public function relatedLedger(): BelongsTo
    {
        return $this->belongsTo(CashLedger::class, 'related_ledger_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by_user_id');
    }

    public function getFormattedNominalAttribute(): string
    {
        return 'Rp '.number_format((float) $this->nominal, 0, ',', '.');
    }

    public function getCleanKeteranganAttribute(): string
    {
        $text = (string) $this->keterangan;
        if (str_starts_with($text, 'Iuran ') && str_contains($text, ' - ')) {
            [$periode, $rest] = explode(' - ', $text, 2);
            $monthsPart = substr($periode, 6);
            if (substr_count($monthsPart, ',') >= 3) {
                preg_match('/\b(20\d\d)\b/', $monthsPart, $yearMatches);
                $year = $yearMatches[1] ?? '';
                if (str_contains($monthsPart, 'Januari') && str_contains($monthsPart, 'Desember')) {
                    return "Iuran 1 Tahun (Jan - Des {$year}) - {$rest}";
                }
                $parts = array_map('trim', explode(',', $monthsPart));
                $first = substr(trim($parts[0]), 0, 3);
                $lastRaw = end($parts);
                $last = substr(trim(preg_replace('/\b20\d\d\b/', '', $lastRaw)), 0, 3);
                $count = count($parts);

                return "Iuran {$count} Bulan ({$first} - {$last} {$year}) - {$rest}";
            }
        }

        return $text;
    }

    public function getKategoriLabelAttribute(): string
    {
        $categories = [
            'iuran_warga' => 'Iuran Warga Bulanan',
            'donasi_warga' => 'Donasi',
            'bantuan_rw' => 'Dana Bantuan RW',
            'setoran_rt' => 'Setoran Wajib dari RT',
            'donasi_luar' => 'Donasi Pihak Luar',
            'bantuan_pemerintah' => 'Bantuan Kelurahan',
            'gaji_keamanan' => 'Gaji Petugas Ronda',
            'angkut_sampah' => 'Retribusi',
            'lampu_jalan' => 'Pemeliharaan Lampu Jalan',
            'kas_sosial' => 'Santunan Kematian',
            'setor_ke_rw' => 'Setor Kas Wajib ke RW',
            'acara_lingkungan' => 'Kegiatan Warga',
            'operasional_rt' => 'Operasional RT',
            'operasional_rw' => 'Operasional RW',
            'lainnya' => 'Lain-lain',
        ];

        return $categories[$this->kategori] ?? ucwords(str_replace('_', ' ', (string) $this->kategori));
    }

    /**
     * Scope Isolasi Buku Kas:
     * - Pengurus RT hanya melihat transaksi kas dengan rt_id miliknya
     * - Pengurus RW dapat melihat Kas Induk RW (rt_id IS NULL) atau memfilter kas per-RT
     *
     * @param  Builder<CashLedger>  $query
     * @return Builder<CashLedger>
     */
    public function scopeAccessibleBy(Builder $query, User $user, ?string $scopeType = 'rw', ?int $targetRtId = null): Builder
    {
        if ($user->isRwLevel()) {
            if ($scopeType === 'rw') {
                return $query->whereNull('rt_id');
            }
            if ($scopeType === 'rt') {
                return $targetRtId ? $query->where('rt_id', $targetRtId) : $query->whereNotNull('rt_id');
            }

            return $query;
        }

        return $query->where('rt_id', $user->rt_id);
    }
}
