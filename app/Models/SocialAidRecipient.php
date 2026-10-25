<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAidRecipient extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'waktu_diterima' => 'datetime',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(SocialAidProgram::class, 'program_id');
    }

    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class);
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    public function replacementResident(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'digantikan_oleh_resident_id');
    }

    public function diserahkanOlehUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diserahkan_oleh');
    }

    public function scopeForCurrentUser(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();
        if ($user && in_array($user->role, ['ketua_rt', 'sekretaris_rt', 'bendahara_rt'], true) && $user->rt_id) {
            return $query->where('rt_id', $user->rt_id);
        }

        return $query;
    }

    public function getStatusUsulanLabelAttribute(): string
    {
        return match ($this->status_usulan) {
            'diusulkan_rt' => 'Diusulkan RT',
            'disetujui_rw' => 'Disetujui RW',
            'ditolak_rw' => 'Ditolak RW',
            'diganti' => 'Penggantian Disetujui',
            default => ucfirst(str_replace('_', ' ', $this->status_usulan)),
        };
    }

    public function getAlasanPenggantianLabelAttribute(): ?string
    {
        return match ($this->alasan_penggantian) {
            'meninggal' => 'Meninggal Dunia',
            'pindah_domisili' => 'Pindah Domisili',
            'sudah_mampu' => 'Sudah Mampu (Graduasi Mandiri)',
            'data_ganda' => 'Data Dobel / Tidak Ditemukan',
            default => $this->alasan_penggantian,
        };
    }

    public function getStatusPenyerahanLabelAttribute(): string
    {
        return match ($this->status_penyerahan) {
            'belum_diambil' => 'Belum Diambil',
            'sudah_diterima' => 'Sudah Diterima',
            'diwakilkan' => 'Diwakilkan Anggota KK',
            default => ucfirst(str_replace('_', ' ', $this->status_penyerahan)),
        };
    }
}
