<?php

namespace App\Livewire\Public;

use App\Models\Rt;
use App\Models\SensusSubmission;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Sensus Warga Mandiri - Portal Warga RW')]
#[Layout('layouts.public')]
class PublicSensus extends Component
{
    // Form Rumah & Pelapor
    public ?int $rt_id = null;

    public string $nomor_rumah = '';

    public string $status_huni = 'dihuni_pemilik';

    public string $kategori_tarif = 'standar';

    public string $no_kk = '';

    public string $nama_pengirim = '';

    public string $no_wa_pengirim = '';

    public string $catatan_rumah = '';

    // Array Anggota Keluarga
    /**
     * @var list<array{
     *     nama_lengkap: string,
     *     nik: string,
     *     jenis_kelamin: string,
     *     tempat_lahir: string,
     *     tanggal_lahir: string,
     *     agama: string,
     *     pekerjaan: string,
     *     status_keluarga: string,
     *     status_domisili: string,
     *     no_hp: string,
     *     golongan_darah: string
     * }>
     */
    public array $keluarga = [];

    // State Sukses / Cek Status
    public bool $isSubmitted = false;

    public ?string $submittedTicket = null;

    public ?SensusSubmission $submittedData = null;

    // Tab Mode: 'form' | 'tracking'
    public string $activeTab = 'form';

    public string $trackingTicketInput = '';

    public ?SensusSubmission $trackedSubmission = null;

    public ?string $trackingError = null;

    public function mount(?string $nomor_rt = null): void
    {
        if ($nomor_rt) {
            $formattedRt = str_pad((string) ((int) preg_replace('/[^0-9]/', '', $nomor_rt)), 2, '0', STR_PAD_LEFT);
            $rt = Rt::where('nomor_rt', $formattedRt)->orWhere('nomor_rt', $nomor_rt)->first();
            if ($rt) {
                $this->rt_id = $rt->id;
            }
        }

        if (empty($this->keluarga)) {
            $this->addMember(true);
        }
    }

    public function addMember(string|bool|null $role = null): void
    {
        if ($role === true) {
            $role = 'kepala_keluarga';
        }

        $hasKepala = collect($this->keluarga)->contains(fn ($m) => ($m['status_keluarga'] ?? '') === 'kepala_keluarga');
        $hasIstri = collect($this->keluarga)->contains(fn ($m) => ($m['status_keluarga'] ?? '') === 'istri');

        if ($role) {
            $defaultStatus = $role;
            $defaultGender = match ($role) {
                'kepala_keluarga' => 'L',
                'istri' => 'P',
                default => 'L',
            };
        } elseif (! $hasKepala) {
            $defaultStatus = 'kepala_keluarga';
            $defaultGender = 'L';
        } elseif (! $hasIstri) {
            $defaultStatus = 'istri';
            $defaultGender = 'P';
        } else {
            $defaultStatus = 'anak';
            $defaultGender = 'L';
        }

        $this->keluarga[] = [
            'nama_lengkap' => '',
            'nik' => '',
            'jenis_kelamin' => $defaultGender,
            'tempat_lahir' => '',
            'tanggal_lahir' => '',
            'agama' => 'Islam',
            'pekerjaan' => '',
            'status_keluarga' => $defaultStatus,
            'status_domisili' => 'ktp_setempat',
            'no_hp' => '',
            'golongan_darah' => '-',
        ];
    }

    public function updatedKeluarga(mixed $value = null, ?string $key = null): void
    {
        if ($key && str_ends_with($key, '.status_keluarga')) {
            $parts = explode('.', $key);
            $index = (int) $parts[0];

            if ($value === 'istri' && isset($this->keluarga[$index])) {
                $this->keluarga[$index]['jenis_kelamin'] = 'P';
            } elseif ($value === 'kepala_keluarga' && isset($this->keluarga[$index])) {
                $this->keluarga[$index]['jenis_kelamin'] = 'L';
            }
        }
    }

    public function removeMember(int $index): void
    {
        if (count($this->keluarga) <= 1) {
            return;
        }

        unset($this->keluarga[$index]);
        $this->keluarga = array_values($this->keluarga);
    }

    public function submit(): void
    {
        // Bersihkan NIK, No KK, dan No WA/HP ke format angka murni serta teks ke UPPERCASE
        $this->no_kk = preg_replace('/[^0-9]/', '', (string) $this->no_kk);
        $this->no_wa_pengirim = preg_replace('/[^0-9]/', '', (string) $this->no_wa_pengirim);
        $this->nama_pengirim = strtoupper(trim($this->nama_pengirim));
        $this->nomor_rumah = strtoupper(trim($this->nomor_rumah));

        foreach ($this->keluarga as $idx => $m) {
            $this->keluarga[$idx]['nik'] = preg_replace('/[^0-9]/', '', (string) ($m['nik'] ?? ''));
            $this->keluarga[$idx]['no_hp'] = ! empty($m['no_hp']) ? preg_replace('/[^0-9]/', '', (string) $m['no_hp']) : null;
            $this->keluarga[$idx]['nama_lengkap'] = strtoupper(trim($m['nama_lengkap'] ?? ''));
            $this->keluarga[$idx]['tempat_lahir'] = strtoupper(trim($m['tempat_lahir'] ?? ''));
            $this->keluarga[$idx]['pekerjaan'] = strtoupper(trim($m['pekerjaan'] ?? ''));
        }

        $this->validate([
            'rt_id' => 'required|exists:rts,id',
            'nomor_rumah' => 'required|string|max:50',
            'status_huni' => 'required|in:dihuni_pemilik,kontrak,kosong',
            'kategori_tarif' => 'required|in:standar,usaha,kosong',
            'no_kk' => 'required|digits:16',
            'nama_pengirim' => 'required|string|max:100',
            'no_wa_pengirim' => 'required|string|min:9|max:20',
            'catatan_rumah' => 'nullable|string|max:500',

            'keluarga' => 'required|array|min:1',
            'keluarga.*.nama_lengkap' => 'required|string|max:100',
            'keluarga.*.nik' => 'required|digits:16',
            'keluarga.*.jenis_kelamin' => 'required|in:L,P',
            'keluarga.*.tempat_lahir' => 'nullable|string|max:50',
            'keluarga.*.tanggal_lahir' => 'required|date',
            'keluarga.*.agama' => 'required|string|max:20',
            'keluarga.*.pekerjaan' => 'nullable|string|max:50',
            'keluarga.*.status_keluarga' => 'required|in:kepala_keluarga,istri,anak,famili_lain,orang_tua',
            'keluarga.*.status_domisili' => 'required|in:ktp_setempat,domisili_luar',
            'keluarga.*.no_hp' => 'nullable|string|max:20',
            'keluarga.*.golongan_darah' => 'nullable|string|max:5',
        ], [
            'rt_id.required' => 'Pilih wilayah RT domisili Anda.',
            'nomor_rumah.required' => 'Nomor / Blok Rumah wajib diisi.',
            'no_kk.required' => 'Nomor Kartu Keluarga wajib diisi.',
            'no_kk.digits' => 'Nomor Kartu Keluarga (KK) wajib berupa angka dan tepat 16 digit.',
            'nama_pengirim.required' => 'Nama pelapor / kepala keluarga wajib diisi.',
            'no_wa_pengirim.required' => 'Nomor WhatsApp aktif wajib diisi untuk verifikasi pengurus.',
            'keluarga.*.nama_lengkap.required' => 'Nama lengkap anggota keluarga wajib diisi.',
            'keluarga.*.nik.required' => 'NIK wajib diisi.',
            'keluarga.*.nik.digits' => 'NIK wajib berupa angka dan tepat 16 digit.',
            'keluarga.*.tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
        ]);

        // Validasi duplikasi NIK antar baris dalam form
        $nikList = [];
        foreach ($this->keluarga as $idx => $m) {
            $nik = $m['nik'];
            if (in_array($nik, $nikList, true)) {
                $this->addError("keluarga.{$idx}.nik", "NIK {$nik} terdaftar ganda di formulir ini.");

                return;
            }
            $nikList[] = $nik;
        }

        // Generate Kode Tiket unik: SNS-YYYYMMDD-XXXX
        $datePrefix = date('Ymd');
        $randomCode = strtoupper(Str::random(4));
        $ticketCode = "SNS-{$datePrefix}-{$randomCode}";

        while (SensusSubmission::where('kode_tiket', $ticketCode)->exists()) {
            $randomCode = strtoupper(Str::random(4));
            $ticketCode = "SNS-{$datePrefix}-{$randomCode}";
        }

        $submission = SensusSubmission::create([
            'rt_id' => $this->rt_id,
            'kode_tiket' => $ticketCode,
            'nomor_rumah' => $this->nomor_rumah,
            'status_huni' => $this->status_huni,
            'kategori_tarif' => $this->kategori_tarif,
            'catatan_rumah' => $this->catatan_rumah ?: null,
            'no_kk' => $this->no_kk,
            'nama_pengirim' => $this->nama_pengirim,
            'no_wa_pengirim' => $this->no_wa_pengirim,
            'data_keluarga' => $this->keluarga,
            'status' => 'menunggu_verifikasi',
        ]);

        $this->isSubmitted = true;
        $this->submittedTicket = $ticketCode;
        $this->submittedData = $submission;
    }

    public function trackTicket(): void
    {
        $this->trackingError = null;
        $this->trackedSubmission = null;

        $ticket = trim(strtoupper($this->trackingTicketInput));
        if ($ticket === '') {
            $this->trackingError = 'Masukkan nomor tiket sensus Anda.';

            return;
        }

        $submission = SensusSubmission::with(['rt', 'verifiedBy'])
            ->where('kode_tiket', $ticket)
            ->first();

        if (! $submission) {
            $this->trackingError = "Tiket \"{$ticket}\" tidak ditemukan dalam database. Mohon periksa kembali.";

            return;
        }

        $this->trackedSubmission = $submission;
    }

    public function resetForm(): void
    {
        $this->isSubmitted = false;
        $this->submittedTicket = null;
        $this->submittedData = null;
        $this->nomor_rumah = '';
        $this->no_kk = '';
        $this->nama_pengirim = '';
        $this->no_wa_pengirim = '';
        $this->catatan_rumah = '';
        $this->keluarga = [];
        $this->addMember(true);
    }

    public function render(): View
    {
        return view('livewire.public.public-sensus', [
            'rts' => Rt::orderBy('nomor_rt')->get(),
        ]);
    }
}
