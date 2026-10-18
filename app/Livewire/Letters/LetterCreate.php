<?php

namespace App\Livewire\Letters;

use App\Models\Letter;
use App\Models\LetterType;
use App\Models\Resident;
use App\Models\Rt;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Buat Surat Pengantar RT/RW')]
class LetterCreate extends Component
{
    public ?int $selectedRtId = null;

    // Pencarian Warga
    public string $searchResident = '';

    public ?int $selectedResidentId = null;

    // Form Data Surat
    public string $tanggalSurat = '';

    public string $selectedJenisKeperluan = '';

    public string $customKeperluan = '';

    public string $keteranganKeperluan = '';

    public string $namaKetuaRt = '';

    public string $namaKetuaRw = 'AMAT MAJA';

    // Pratinjau Nomor Surat
    public string $previewNomorSurat = '';

    public int $previewNomorUrut = 1;

    public function mount(): void
    {
        $user = auth()->user();

        // Hak Akses (Hanya admin_rw, ketua_rt, dan sekretaris_rt)
        if (! $user || (! $user->isAdminRw() && ! in_array($user->role, ['ketua_rt', 'sekretaris_rt']))) {
            abort(403, 'Anda tidak memiliki hak akses untuk menerbitkan surat pengantar.');
        }

        $this->tanggalSurat = date('Y-m-d');

        if ($user->rt_id) {
            $this->selectedRtId = $user->rt_id;
            $this->namaKetuaRt = $user->rt->nama_ketua ?? $user->nama;
        } else {
            // Default RT pertama untuk Admin RW
            $firstRt = Rt::orderBy('nomor_rt')->first();
            if ($firstRt) {
                $this->selectedRtId = $firstRt->id;
                $this->namaKetuaRt = $firstRt->nama_ketua ?? '';
            }
        }

        $this->updateNomorSuratPreview();
    }

    public function updatedSelectedRtId(): void
    {
        $rt = Rt::find($this->selectedRtId);
        if ($rt) {
            $this->namaKetuaRt = $rt->nama_ketua ?? '';
        }

        // Reset warga terpilih jika RT berubah
        $this->selectedResidentId = null;
        $this->searchResident = '';
        $this->updateNomorSuratPreview();
    }

    public function updateNomorSuratPreview(): void
    {
        if (! $this->selectedRtId) {
            $this->previewNomorSurat = '-';

            return;
        }

        $rt = Rt::find($this->selectedRtId);
        if (! $rt) {
            $this->previewNomorSurat = '-';

            return;
        }

        $preview = Letter::generateNextLetterNumber($this->selectedRtId, $rt->nomor_rt, '01');
        $this->previewNomorUrut = $preview['nomor_urut'];
        $this->previewNomorSurat = $preview['nomor_surat'];
    }

    public function selectResident(int $residentId): void
    {
        $user = auth()->user();
        $resident = Resident::query()
            ->with(['house.rt'])
            ->accessibleBy($user, $this->selectedRtId)
            ->where('status_warga', 'aktif')
            ->findOrFail($residentId);

        $this->selectedResidentId = $resident->id;
        $this->searchResident = $resident->nama_lengkap;
    }

    public function resetSelectedResident(): void
    {
        $this->selectedResidentId = null;
        $this->searchResident = '';
    }

    public function saveLetter()
    {
        $user = auth()->user();

        // Hak Akses
        if (! $user || (! $user->isAdminRw() && ! in_array($user->role, ['ketua_rt', 'sekretaris_rt']))) {
            abort(403);
        }

        $this->validate([
            'selectedRtId' => 'required|exists:rts,id',
            'selectedResidentId' => 'required|exists:residents,id',
            'tanggalSurat' => 'required|date',
            'selectedJenisKeperluan' => 'required|string',
            'customKeperluan' => 'required_if:selectedJenisKeperluan,Lainnya|nullable|string|max:150',
            'keteranganKeperluan' => 'nullable|string|max:500',
            'namaKetuaRt' => 'required|string|max:100',
            'namaKetuaRw' => 'nullable|string|max:100',
        ], [
            'selectedResidentId.required' => 'Wajib memilih warga pemohon surat.',
            'selectedJenisKeperluan.required' => 'Pilih jenis keperluan surat.',
            'customKeperluan.required_if' => 'Tuliskan jenis keperluan surat jika memilih opsi Lainnya.',
            'namaKetuaRt.required' => 'Nama Ketua RT penandatangan wajib diisi.',
        ]);

        $rt = Rt::findOrFail($this->selectedRtId);
        $resident = Resident::with(['house.rt'])->findOrFail($this->selectedResidentId);

        // Pastikan warga berstatus aktif
        if ($resident->status_warga !== 'aktif') {
            $this->addError('selectedResidentId', 'Hanya warga dengan status aktif yang dapat mengajukan surat.');

            return null;
        }

        $jenisKeperluan = $this->selectedJenisKeperluan === 'Lainnya'
            ? trim($this->customKeperluan)
            : trim($this->selectedJenisKeperluan);

        // Buat nomor surat resmi secara atomik (RT dan RW 2 digit)
        $letterNumberData = Letter::generateNextLetterNumber($rt->id, $rt->nomor_rt, '01');

        // Format snapshot data warga (huruf kapital standar persuratan resmi)
        $rtFormatted = str_pad((string) ((int) $rt->nomor_rt), 2, '0', STR_PAD_LEFT);
        $ttl = strtoupper("{$resident->tempat_lahir}, ".Carbon::parse($resident->tanggal_lahir)->locale('id')->translatedFormat('d F Y'));
        $alamat = strtoupper(($resident->house?->nomor_rumah ?? 'Wilayah RT '.$rtFormatted).", RT.{$rtFormatted} RW.01 Kelurahan Slawi Kulon, Kecamatan Slawi, Kab. Tegal");

        $letter = Letter::create([
            'rt_id' => $rt->id,
            'resident_id' => $resident->id,
            'created_by_user_id' => $user->id,
            'nomor_urut' => $letterNumberData['nomor_urut'],
            'nomor_surat' => $letterNumberData['nomor_surat'],
            'tahun' => $letterNumberData['tahun'],
            'jenis_keperluan' => $jenisKeperluan,
            'keterangan_keperluan' => $this->keteranganKeperluan ? trim($this->keteranganKeperluan) : null,
            'snapshot_nama' => strtoupper($resident->nama_lengkap),
            'snapshot_nik' => $resident->nik,
            'snapshot_no_kk' => $resident->no_kk,
            'snapshot_ttl' => $ttl,
            'snapshot_jenis_kelamin' => $resident->jenis_kelamin,
            'snapshot_agama' => strtoupper($resident->agama),
            'snapshot_pekerjaan' => strtoupper($resident->pekerjaan),
            'snapshot_alamat' => $alamat,
            'nama_ketua_rt' => trim($this->namaKetuaRt),
            'nama_ketua_rw' => $this->namaKetuaRw ? trim($this->namaKetuaRw) : 'AMAT MAJA',
            'tanggal_surat' => $this->tanggalSurat,
            'signed_by_rt_at' => $user->role === 'sekretaris_rt' ? null : now(),
            'signed_by_rw_at' => $user->isAdminRw() ? now() : null,
            'status' => $user->isAdminRw() ? 'terbit' : ($user->role === 'sekretaris_rt' ? 'menunggu_rt' : 'menunggu_rw'),
        ]);

        if ($user->isAdminRw()) {
            session()->flash('success', "Surat Pengantar No. {$letter->nomor_surat} berhasil diterbitkan dan disahkan langsung oleh Ketua RW!");

            return redirect()->route('letters.print', $letter->id);
        }

        if ($user->role === 'sekretaris_rt') {
            session()->flash('success', "Draf Surat Pengantar No. {$letter->nomor_surat} berhasil diajukan! Menunggu Persetujuan & TTE Ketua RT.");

            return redirect()->route('letters.index');
        }

        session()->flash('success', "Surat Pengantar No. {$letter->nomor_surat} berhasil dibuat! Status saat ini: Menunggu TTE & Persetujuan Ketua RW.");

        return redirect()->route('letters.index');
    }

    public function render(): View
    {
        $user = auth()->user();
        $rts = Rt::orderBy('nomor_rt')->get();
        $letterTypes = LetterType::where('is_active', true)->orderBy('nama_keperluan')->get();

        // Autocomplete pencarian warga
        /** @var Collection<int, Resident> $residentSuggestions */
        $residentSuggestions = collect();
        if ($this->searchResident && ! $this->selectedResidentId) {
            $s = '%'.trim($this->searchResident).'%';
            $residentSuggestions = Resident::query()
                ->with(['house.rt'])
                ->accessibleBy($user, $this->selectedRtId)
                ->where('status_warga', 'aktif')
                ->where(function ($q) use ($s) {
                    $q->where('nama_lengkap', 'like', $s)
                        ->orWhere('nik', 'like', $s)
                        ->orWhereHas('house', fn ($hq) => $hq->where('nomor_rumah', 'like', $s));
                })
                ->take(8)
                ->get();
        }

        // Detail Warga Terpilih
        $selectedResident = null;
        if ($this->selectedResidentId) {
            $selectedResident = Resident::with(['house.rt'])->find($this->selectedResidentId);
        }

        return view('livewire.letters.letter-create', [
            'user' => $user,
            'rts' => $rts,
            'letterTypes' => $letterTypes,
            'residentSuggestions' => $residentSuggestions,
            'selectedResident' => $selectedResident,
        ]);
    }
}
