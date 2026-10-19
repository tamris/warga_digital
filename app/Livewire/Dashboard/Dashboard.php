<?php

namespace App\Livewire\Dashboard;

use App\Models\House;
use App\Models\Resident;
use App\Models\Rt;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard Pengelolaan Warga')]
class Dashboard extends Component
{
    public ?int $selectedRtId = null;

    public function render(): View
    {
        $user = auth()->user();
        $rts = Rt::orderBy('nomor_rt')->get();

        // Query Rumah dengan Scope
        $houseQuery = House::query()->forCurrentUser();
        if ($user && $user->isAdminRw() && $this->selectedRtId) {
            $houseQuery->where('rt_id', $this->selectedRtId);
        }

        $totalHouses = (clone $houseQuery)->count();
        $totalDihuniPemilik = (clone $houseQuery)->where('status_huni', 'dihuni_pemilik')->count();
        $totalKontrak = (clone $houseQuery)->where('status_huni', 'kontrak')->count();
        $totalKosong = (clone $houseQuery)->where('status_huni', 'kosong')->count();

        // Query Warga dengan Scope
        $residentQuery = Resident::query()->forCurrentUser();
        if ($user && $user->isAdminRw() && $this->selectedRtId) {
            $rtId = $this->selectedRtId;
            $residentQuery->whereHas('house', function ($q) use ($rtId) {
                $q->where('rt_id', $rtId);
            });
        }

        $totalResidents = (clone $residentQuery)->where('status_warga', 'aktif')->count();
        $totalLaki = (clone $residentQuery)->where('status_warga', 'aktif')->where('jenis_kelamin', 'L')->count();
        $totalPerempuan = (clone $residentQuery)->where('status_warga', 'aktif')->where('jenis_kelamin', 'P')->count();

        $lansiaThreshold = Carbon::today()->subYears(60);
        $totalLansia = (clone $residentQuery)->where('status_warga', 'aktif')->whereDate('tanggal_lahir', '<=', $lansiaThreshold)->count();

        $totalDomisiliLuar = (clone $residentQuery)->where('status_warga', 'aktif')->where('status_kependudukan', 'domisili_luar')->count();
        $totalPindah = (clone $residentQuery)->where('status_warga', 'pindah')->count();

        // Agregat per RT untuk Admin RW
        /** @var Collection<int, array{id: int, nomor_rt: string, nama_ketua: ?string, no_wa_ketua: ?string, total_rumah: int, total_warga: int}> $rtSummaries */
        $rtSummaries = collect();
        if ($user && $user->isAdminRw()) {
            $rtSummaries = Rt::withCount(['houses', 'residents' => function ($q) {
                $q->where('status_warga', 'aktif');
            }])->get()->map(function ($rt) {
                return [
                    'id' => $rt->id,
                    'nomor_rt' => $rt->nomor_rt,
                    'nama_ketua' => $rt->nama_ketua,
                    'no_wa_ketua' => $rt->no_wa_ketua,
                    'formatted_wa_ketua' => $rt->formatted_wa_ketua,
                    'total_rumah' => $rt->houses_count,
                    'total_warga' => $rt->residents_count,
                ];
            });
        }

        // 5 Warga Terakhir yang Diperbarui / Terdaftar
        $recentResidents = (clone $residentQuery)
            ->with(['house.rt'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Sapaan Berdasarkan Waktu (WIB)
        $hour = Carbon::now('Asia/Jakarta')->hour;
        $greeting = match (true) {
            $hour >= 4 && $hour < 10 => 'Selamat Pagi',
            $hour >= 10 && $hour < 15 => 'Selamat Siang',
            $hour >= 15 && $hour < 18 => 'Selamat Sore',
            default => 'Selamat Malam',
        };

        return view('livewire.dashboard', [
            'user' => $user,
            'greeting' => $greeting,
            'rts' => $rts,
            'totalHouses' => $totalHouses,
            'totalDihuniPemilik' => $totalDihuniPemilik,
            'totalKontrak' => $totalKontrak,
            'totalKosong' => $totalKosong,
            'totalResidents' => $totalResidents,
            'totalLaki' => $totalLaki,
            'totalPerempuan' => $totalPerempuan,
            'totalLansia' => $totalLansia,
            'totalDomisiliLuar' => $totalDomisiliLuar,
            'totalPindah' => $totalPindah,
            'rtSummaries' => $rtSummaries,
            'recentResidents' => $recentResidents,
        ]);
    }
}
