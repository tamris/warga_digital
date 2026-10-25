<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialAidProgram extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_penyaluran' => 'date',
        'total_kuota' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quotas(): HasMany
    {
        return $this->hasMany(SocialAidQuota::class, 'program_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(SocialAidRecipient::class, 'program_id');
    }

    public function getSumberDanaLabelAttribute(): string
    {
        return match ($this->sumber_dana) {
            'pemerintah_pusat' => 'Pemerintah Pusat',
            'pemda_kelurahan' => 'Pemda / Kelurahan',
            'kas_rw' => 'Kas Induk RW',
            'csr_donatur' => 'CSR / Donatur Warga',
            default => ucfirst(str_replace('_', ' ', $this->sumber_dana)),
        };
    }

    public function getJenisBantuanLabelAttribute(): string
    {
        return match ($this->jenis_bantuan) {
            'sembako_barang' => 'Sembako / Barang',
            'uang_tunai' => 'Bantuan Tunai',
            default => ucfirst(str_replace('_', ' ', $this->jenis_bantuan)),
        };
    }

    public function isDistributionDue(): bool
    {
        if (! $this->tanggal_penyaluran) {
            return false;
        }

        return now()->startOfDay()->gte($this->tanggal_penyaluran->startOfDay());
    }

    public function isReadyForDistribution(): bool
    {
        return $this->areAllRtQuotasFilled() && $this->areAllProposalsApproved();
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->status_program === 'verifikasi_rw' && $this->isReadyForDistribution()) {
            return 'Siap Disalurkan (Menunggu Jadwal)';
        }

        return match ($this->status_program) {
            'pendataan_rt' => 'Pendataan RT',
            'verifikasi_rw' => 'Verifikasi RW',
            'penyaluran' => 'Tahap Penyaluran',
            'selesai' => 'Selesai & Arsip',
            default => ucfirst($this->status_program),
        };
    }

    public function getQuotaForRt(int $rtId): int
    {
        return (int) ($this->quotas->firstWhere('rt_id', $rtId)->jumlah_kuota ?? 0);
    }

    public function getRecipientsCountForRt(int $rtId): int
    {
        return $this->recipients->where('rt_id', $rtId)->count();
    }

    public function areAllRtQuotasFilled(): bool
    {
        $quotas = $this->quotas;
        if ($quotas->isEmpty()) {
            return false;
        }

        $activeRecipients = $this->recipients->where('status_usulan', '!=', 'diganti');
        $targetQuota = min($this->total_kuota, $quotas->sum('jumlah_kuota'));

        if ($activeRecipients->count() < $targetQuota) {
            return false;
        }

        foreach ($quotas as $q) {
            $rtCount = $activeRecipients->where('rt_id', $q->rt_id)->count();
            if ($rtCount < $q->jumlah_kuota) {
                return false;
            }
        }

        return true;
    }

    public function areAllProposalsApproved(): bool
    {
        $activeRecipients = $this->recipients->where('status_usulan', '!=', 'diganti');
        if ($activeRecipients->isEmpty()) {
            return false;
        }

        if (! $this->areAllRtQuotasFilled()) {
            return false;
        }

        $pending = $activeRecipients->where('status_usulan', 'diusulkan_rt')->count();
        $rejected = $activeRecipients->where('status_usulan', 'ditolak_rw')->count();
        $approved = $activeRecipients->where('status_usulan', 'disetujui_rw')->count();

        $targetQuota = $this->quotas->isNotEmpty() ? min($this->total_kuota, $this->quotas->sum('jumlah_kuota')) : $this->total_kuota;

        return $pending === 0 && $rejected === 0 && $approved >= $targetQuota;
    }

    public function areAllDeliveriesCompleted(): bool
    {
        $approved = $this->recipients->where('status_usulan', 'disetujui_rw');
        if ($approved->isEmpty()) {
            return false;
        }

        return $approved->where('status_penyerahan', 'belum_diambil')->count() === 0;
    }
}
