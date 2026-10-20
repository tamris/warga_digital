<?php

namespace App\Livewire\Finance;

use App\Models\CashLedger;
use App\Models\FeeSetting;
use App\Models\House;
use App\Models\HouseDue;
use App\Models\Rt;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Buku Iuran Warga Bulanan')]
class DuesMatrix extends Component
{
    use WithPagination;

    #[Url(as: 'tahun')]
    public int $year;

    #[Url(as: 'rt')]
    public ?int $selectedRtId = null;

    #[Url(as: 'cari')]
    public string $search = '';

    #[Url(as: 'kategori')]
    public string $kategoriTarif = '';

    // Properti Modal Pembayaran
    public bool $showPayModal = false;

    public ?int $payHouseId = null;

    public string $payHouseNumber = '';

    public string $payHeadName = '';

    public string $payPhone = '';

    public string $payRtNomor = '';

    public float $payNominalPerMonth = 0;

    /** @var list<int> */
    public array $payMonths = [];

    /** @var list<int> */
    public array $payAlreadyPaidMonths = [];

    public string $payMethod = 'tunai';

    public string $payPayerName = '';

    public string $payDate = '';

    public string $payNotes = '';

    // Properti Kuitansi Sukses Terakhir (untuk share WA)
    public ?array $lastPaymentReceipt = null;

    // Properti Modal Detail Pelunasan
    public bool $showDetailModal = false;

    public ?int $detailDueId = null;

    public ?HouseDue $detailDue = null;

    // Properti Modal Pengaturan Tarif RT
    public bool $showSettingsModal = false;

    public ?int $settingsRtId = null;

    public float $feeStandar = 50000;

    public float $feeUsaha = 100000;

    public float $feeKosong = 20000;

    public function mount(): void
    {
        abort_unless(auth()->user()?->canManageFinance(), 403, 'Akses ditolak. Halaman khusus pengurus keuangan.');

        $this->year = (int) date('Y');
        $this->payDate = Carbon::today()->toDateString();

        $user = auth()->user();
        if ($user && ! $user->isRwLevel() && $user->rt_id) {
            $this->selectedRtId = $user->rt_id;
            $this->settingsRtId = $user->rt_id;
        } else {
            $firstRt = Rt::orderBy('nomor_rt')->first();
            $this->settingsRtId = $firstRt?->id;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedRtId(&$value): void
    {
        $user = auth()->user();
        if ($user && ! $user->isRwLevel() && $user->rt_id) {
            $value = $user->rt_id;
        }
        $this->resetPage();
    }

    public function updatedSelectedRtId(): void
    {
        $user = auth()->user();
        if ($user && ! $user->isRwLevel() && $user->rt_id) {
            $this->selectedRtId = $user->rt_id;
        }
        $this->resetPage();
    }

    public function updatingKategoriTarif(): void
    {
        $this->resetPage();
    }

    public function updatingYear(): void
    {
        $this->resetPage();
    }

    public function prevYear(): void
    {
        $this->year--;
        $this->resetPage();
    }

    public function nextYear(): void
    {
        $this->year++;
        $this->resetPage();
    }

    /**
     * Buka Modal Pembayaran Cepat untuk Rumah Tertentu
     */
    public function openPayModal(int $houseId, int $month): void
    {
        $house = House::with(['rt', 'headOfFamily'])->findOrFail($houseId);

        $user = auth()->user();
        if (! $user || ! $user->canManageFinance()) {
            session()->flash('error', 'Anda tidak memiliki hak akses untuk menerima pembayaran iuran.');

            return;
        }

        if ($user->isBendaharaRw()) {
            session()->flash('error', 'Akses ditolak. Bendahara RW berstatus pemantau (read-only) untuk iuran warga bulanan.');

            return;
        }

        if (! $user->isRwLevel() && $user->rt_id !== $house->rt_id) {
            session()->flash('error', 'Anda hanya dapat mengelola iuran di wilayah RT Anda sendiri.');

            return;
        }

        $this->payHouseId = $house->id;
        $this->payHouseNumber = $house->nomor_rumah;
        $this->payHeadName = $house->headOfFamily?->nama_lengkap ?? 'Penghuni Rumah';
        $this->payPhone = $house->headOfFamily?->no_wa ?? '';
        $this->payRtNomor = $house->rt ? str_pad((string) ((int) $house->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : '01';
        $this->payPayerName = $this->payHeadName;
        $this->payDate = Carbon::today()->toDateString();
        $this->payMethod = 'tunai';
        $this->payNotes = '';

        // Ambil tarif iuran bulanan rumah ini
        $setting = FeeSetting::where('rt_id', $house->rt_id)
            ->where('kategori_tarif', $house->kategori_tarif)
            ->first();

        $this->payNominalPerMonth = (float) ($setting?->nominal ?? match ($house->kategori_tarif) {
            'usaha' => 100000,
            'kosong' => 20000,
            default => 50000,
        });

        // Ambil daftar bulan yang sudah lunas untuk rumah ini di tahun ini
        $this->payAlreadyPaidMonths = HouseDue::where('house_id', $house->id)
            ->where('tahun', $this->year)
            ->where('status', 'lunas')
            ->pluck('bulan')
            ->map(fn ($b) => (int) $b)
            ->toArray();

        // Bulan yang dipilih awal (hanya jika belum lunas)
        $this->payMonths = in_array($month, $this->payAlreadyPaidMonths, true) ? [] : [$month];
        $this->showPayModal = true;
    }

    /**
     * Toggle centang bulan di modal pembayaran
     */
    public function togglePayMonth(int $month): void
    {
        if (in_array($month, $this->payAlreadyPaidMonths, true)) {
            return;
        }

        if (in_array($month, $this->payMonths, true)) {
            $this->payMonths = array_values(array_diff($this->payMonths, [$month]));
        } else {
            $this->payMonths[] = $month;
            sort($this->payMonths);
        }
    }

    /**
     * Pilih semua bulan yang belum lunas
     */
    public function selectAllUnpaidMonths(): void
    {
        $all = range(1, 12);
        $this->payMonths = array_values(array_diff($all, $this->payAlreadyPaidMonths));
    }

    /**
     * Kosongkan pilihan bulan
     */
    public function clearPayMonths(): void
    {
        $this->payMonths = [];
    }

    /**
     * Simpan Pembayaran Iuran & Catat Otomatis ke Buku Kas
     */
    public function savePayment(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->canManageFinance()) {
            session()->flash('error', 'Akses ditolak.');

            return;
        }

        if ($user->isBendaharaRw()) {
            session()->flash('error', 'Akses ditolak. Bendahara RW berstatus pemantau (read-only) untuk iuran warga bulanan.');

            return;
        }

        if (! $this->payHouseId || empty($this->payMonths)) {
            session()->flash('error', 'Pilih minimal satu bulan yang akan dilunasi.');

            return;
        }

        $house = House::with(['rt', 'headOfFamily'])->findOrFail($this->payHouseId);

        $monthNames = [];
        $totalNominal = 0;
        $savedDues = [];

        foreach ($this->payMonths as $m) {
            $due = HouseDue::updateOrCreate(
                [
                    'house_id' => $house->id,
                    'bulan' => $m,
                    'tahun' => $this->year,
                ],
                [
                    'rt_id' => $house->rt_id,
                    'nominal' => $this->payNominalPerMonth,
                    'status' => 'lunas',
                    'tanggal_bayar' => $this->payDate ?: Carbon::today()->toDateString(),
                    'metode_bayar' => $this->payMethod,
                    'nama_pembayar' => $this->payPayerName ?: $this->payHeadName,
                    'received_by_user_id' => $user->id,
                    'catatan' => $this->payNotes,
                ]
            );

            $monthNames[] = HouseDue::getMonthName($m);
            $totalNominal += $this->payNominalPerMonth;
            $savedDues[] = $due;
        }

        // Catat otomatis ke tabel buku kas operasional (CashLedger)
        $countMonths = count($this->payMonths);
        if ($countMonths === 12) {
            $monthStr = '1 Tahun (Jan - Des)';
        } elseif ($countMonths > 2) {
            $firstM = substr(HouseDue::getMonthName(min($this->payMonths)), 0, 3);
            $lastM = substr(HouseDue::getMonthName(max($this->payMonths)), 0, 3);
            $monthStr = "{$countMonths} Bulan ({$firstM} - {$lastM})";
        } else {
            $monthStr = implode(', ', $monthNames);
        }

        $keteranganKas = "Iuran {$monthStr} {$this->year} - {$house->nomor_rumah} (".($this->payPayerName ?: $this->payHeadName).')';

        // Hubungkan ke due pertama
        $firstDue = $savedDues[0] ?? null;

        CashLedger::create([
            'rt_id' => $house->rt_id,
            'user_id' => $user->id,
            'house_due_id' => $firstDue?->id,
            'jenis_transaksi' => 'masuk',
            'kategori' => 'iuran_warga',
            'nominal' => $totalNominal,
            'tanggal' => $this->payDate ?: Carbon::today()->toDateString(),
            'keterangan' => $keteranganKas,
        ]);

        // Simpan data kuitansi untuk tombol WhatsApp instan
        $this->lastPaymentReceipt = [
            'house_number' => $house->nomor_rumah,
            'head_name' => $this->payPayerName ?: $this->payHeadName,
            'rt' => $this->payRtNomor,
            'periode' => "{$monthStr} {$this->year}",
            'nominal' => 'Rp '.number_format($totalNominal, 0, ',', '.'),
            'tanggal' => Carbon::parse($this->payDate)->translatedFormat('d/m/Y'),
            'metode' => ucfirst($this->payMethod),
            'phone' => $this->payPhone,
            'wa_link' => $this->generateWaReceiptLink($house->nomor_rumah, $this->payPayerName ?: $this->payHeadName, $this->payRtNomor, "{$monthStr} {$this->year}", $totalNominal, $this->payDate, $this->payPhone),
        ];

        $this->showPayModal = false;
        session()->flash('success', "Pembayaran iuran {$house->nomor_rumah} ({$monthStr} {$this->year}) sebesar Rp ".number_format($totalNominal, 0, ',', '.').' berhasil disimpan dan tercatat di Kas RT.');
    }

    /**
     * Buka Detail Pelunasan Bulan yang Sudah Lunas
     */
    public function openDetailModal(int $dueId): void
    {
        $due = HouseDue::with(['house.rt', 'receivedBy'])->findOrFail($dueId);
        $this->detailDue = $due;
        $this->detailDueId = $due->id;
        $this->showDetailModal = true;
    }

    /**
     * Batalkan Pelunasan / Void Iuran
     */
    public function voidPayment(int $dueId): void
    {
        $user = auth()->user();
        if (! $user || ! $user->canManageFinance()) {
            session()->flash('error', 'Akses ditolak.');

            return;
        }

        if ($user->isBendaharaRw()) {
            session()->flash('error', 'Akses ditolak. Bendahara RW tidak memiliki wewenang membatalkan pelunasan iuran tingkat RT.');

            return;
        }

        $due = HouseDue::with('house')->findOrFail($dueId);

        if (! $user->isRwLevel() && $user->rt_id !== $due->rt_id) {
            session()->flash('error', 'Anda hanya dapat membatalkan iuran di wilayah RT Anda.');

            return;
        }

        // Hapus juga transaksi kas terkait agar pembukuan tidak selisih
        CashLedger::where('house_due_id', $due->id)->delete();

        $info = "Iuran {$due->nama_bulan} {$due->tahun} untuk {$due->house?->nomor_rumah}";
        $due->delete();

        $this->showDetailModal = false;
        $this->detailDue = null;
        $this->detailDueId = null;

        session()->flash('success', "Status {$info} berhasil dibatalkan dan transaksi kas terkait telah dihapus.");
    }

    /**
     * Buka Modal Pengaturan Tarif RT
     */
    public function openSettingsModal(?int $rtId = null): void
    {
        $user = auth()->user();
        if ($user && $user->isBendaharaRw()) {
            session()->flash('error', 'Akses ditolak. Pengaturan tarif iuran ditentukan oleh masing-masing RT.');

            return;
        }

        $targetRtId = $rtId ?? $this->selectedRtId ?? Rt::first()?->id;
        $this->settingsRtId = $targetRtId;

        $settings = FeeSetting::where('rt_id', $targetRtId)->get()->keyBy('kategori_tarif');

        $this->feeStandar = (float) ($settings['standar']->nominal ?? 50000);
        $this->feeUsaha = (float) ($settings['usaha']->nominal ?? 100000);
        $this->feeKosong = (float) ($settings['kosong']->nominal ?? 20000);

        $this->showSettingsModal = true;
    }

    /**
     * Simpan Pengaturan Tarif RT
     */
    public function saveSettings(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->canManageFinance()) {
            session()->flash('error', 'Akses ditolak.');

            return;
        }

        if ($user->isBendaharaRw()) {
            session()->flash('error', 'Akses ditolak. Pengaturan tarif iuran ditentukan oleh masing-masing RT.');

            return;
        }

        if (! $this->settingsRtId) {
            return;
        }

        FeeSetting::updateOrCreate(
            ['rt_id' => $this->settingsRtId, 'kategori_tarif' => 'standar'],
            ['nominal' => $this->feeStandar, 'keterangan' => 'Tarif standar hunian']
        );
        FeeSetting::updateOrCreate(
            ['rt_id' => $this->settingsRtId, 'kategori_tarif' => 'usaha'],
            ['nominal' => $this->feeUsaha, 'keterangan' => 'Tarif tempat usaha / toko']
        );
        FeeSetting::updateOrCreate(
            ['rt_id' => $this->settingsRtId, 'kategori_tarif' => 'kosong'],
            ['nominal' => $this->feeKosong, 'keterangan' => 'Tarif rumah kosong']
        );

        $this->showSettingsModal = false;
        session()->flash('success', 'Besaran tarif iuran bulanan RT berhasil disimpan.');
    }

    /**
     * Generate Link Kirim Kuitansi WhatsApp
     */
    protected function generateWaReceiptLink(string $houseNumber, string $payerName, string $rtNumber, string $periode, float $nominal, string $date, ?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        $formattedNominal = 'Rp '.number_format($nominal, 0, ',', '.');
        $formattedDate = Carbon::parse($date)->translatedFormat('d/m/Y');

        $message = "Yth. Bapak/Ibu *{$payerName}*,\n\n"
            ."Terima kasih. Pembayaran Iuran RT *{$rtNumber}* untuk unit *{$houseNumber}* "
            ."periode *{$periode}* sebesar *{$formattedNominal}* telah kami terima pada *{$formattedDate}*.\n\n"
            ."Status: *LUNAS (SAH)*\n"
            .'Pencatat: *'.(auth()->user()?->nama ?? 'Bendahara RT')."*\n\n"
            .'_Sistem Informasi Manajemen Warga Digital RT/RW_';

        return 'https://wa.me/'.$cleanPhone.'?text='.rawurlencode($message);
    }

    public function render(): View
    {
        $user = auth()->user();

        // 1. Query Daftar Rumah
        $housesQuery = House::query()
            ->with(['rt', 'headOfFamily'])
            ->accessibleBy($user, $this->selectedRtId);

        if ($this->search) {
            $term = '%'.trim($this->search).'%';
            $housesQuery->where(function ($q) use ($term) {
                $q->where('nomor_rumah', 'like', $term)
                    ->orWhereHas('residents', function ($rq) use ($term) {
                        $rq->where('nama_lengkap', 'like', $term);
                    });
            });
        }

        if ($this->kategoriTarif) {
            $housesQuery->where('kategori_tarif', $this->kategoriTarif);
        }

        $houses = $housesQuery->orderBy('rt_id')->orderBy('nomor_rumah')->paginate(15);

        // 2. Ambil seluruh iuran yang sudah tercatat di database untuk tahun ini & rumah-rumah tersebut
        $houseIds = $houses->pluck('id')->toArray();
        $duesRecords = HouseDue::whereIn('house_id', $houseIds)
            ->where('tahun', $this->year)
            ->get()
            ->groupBy('house_id')
            ->map(function ($items) {
                return $items->keyBy('bulan');
            });

        // 3. Ambil Master Tarif per RT untuk menghitung nominal bagi bulan yang belum tercatat
        $allRtIds = House::accessibleBy($user, $this->selectedRtId)->pluck('rt_id')->unique()->toArray();
        $feeSettings = FeeSetting::whereIn('rt_id', $allRtIds)->get()->groupBy('rt_id')->map(function ($items) {
            return $items->keyBy('kategori_tarif');
        });

        // 4. Hitung Statistik Ringkasan Cepat
        $allHousesScope = House::accessibleBy($user, $this->selectedRtId)->get();
        $allScopeIds = $allHousesScope->pluck('id')->toArray();

        $totalTerkumpulTahunIni = (float) HouseDue::whereIn('house_id', $allScopeIds)
            ->where('tahun', $this->year)
            ->where('status', 'lunas')
            ->sum('nominal');

        $totalLunasBulanIni = HouseDue::whereIn('house_id', $allScopeIds)
            ->where('tahun', $this->year)
            ->where('bulan', (int) date('n'))
            ->where('status', 'lunas')
            ->count();

        $totalRumah = $allHousesScope->count();
        $persenBulanIni = $totalRumah > 0 ? round(($totalLunasBulanIni / $totalRumah) * 100) : 0;

        // Ambil Daftar RT untuk Filter jika pengguna level RW
        $rts = $user && $user->isRwLevel() ? Rt::orderBy('nomor_rt')->get() : collect();

        return view('livewire.finance.dues-matrix', [
            'houses' => $houses,
            'duesRecords' => $duesRecords,
            'feeSettings' => $feeSettings,
            'rts' => $rts,
            'totalTerkumpulTahunIni' => $totalTerkumpulTahunIni,
            'totalLunasBulanIni' => $totalLunasBulanIni,
            'totalRumah' => $totalRumah,
            'persenBulanIni' => $persenBulanIni,
            'user' => $user,
        ]);
    }
}
