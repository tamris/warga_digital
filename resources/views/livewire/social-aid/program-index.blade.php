<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="bg-white dark:bg-[#111b2f] rounded-2xl p-5 sm:p-6 border border-slate-200 dark:border-[#1d2c4d] shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-800/60 mb-2">
                <span>Bantuan Sosial Tepat Sasaran</span>
                <span>•</span>
                <span>{{ $user->isRwLevel() ? 'Tingkat RW 01' : 'Wilayah RT ' . str_pad((string)((int)$user->rt?->nomor_rt), 2, '0', STR_PAD_LEFT) }}</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                Program Bantuan Sosial
            </h1>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 font-medium">
                Penyaluran bantuan pangan, santunan tunai, dan logistik terpusat dengan kuota proporsional per wilayah RT
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            @if($user->canCreateSocialAidProgram())
            <button type="button" 
                    wire:click="openCreateModal" 
                    class="h-11 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs inline-flex items-center space-x-2 transition shadow-xs cursor-pointer">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Buat Program Baru</span>
            </button>
            @endif
        </div>
    </div>

    <!-- Toolbar Filter & Tabel Program -->
    <div class="bg-white dark:bg-[#111b2f] rounded-2xl border border-slate-200 dark:border-[#1d2c4d] shadow-xs overflow-hidden">
        <!-- Filter Bar -->
        <div class="p-4 border-b border-slate-200 dark:border-[#1d2c4d] bg-slate-50/50 dark:bg-[#0c1427]/60 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="flex-1 relative">
                <input type="text" 
                       wire:model.live.debounce.250ms="search"
                       placeholder="Cari nama program bantuan atau detail sembako..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-white dark:bg-[#0c1427] border border-slate-200 dark:border-[#1d2c4d] rounded-xl focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 shadow-2xs transition">
                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div class="flex items-center gap-2">
                <select wire:model.live="statusFilter" 
                        class="text-xs bg-white dark:bg-[#0c1427] border border-slate-200 dark:border-[#1d2c4d] rounded-xl px-2.5 py-2 font-medium text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-emerald-500 shadow-2xs transition">
                    <option value="">Semua Status Program</option>
                    <option value="pendataan_rt">Pendataan RT</option>
                    <option value="verifikasi_rw">Verifikasi RW</option>
                    <option value="penyaluran">Tahap Penyaluran</option>
                    <option value="selesai">Selesai & Arsip</option>
                </select>
            </div>
        </div>

        <!-- Tabel Daftar Program -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-[#0c1427] text-slate-600 dark:text-slate-400 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200 dark:border-[#1d2c4d]">
                        <th class="py-3 px-5">Nama Program & Sumber Dana</th>
                        <th class="py-3 px-4">Bentuk Bantuan</th>
                        <th class="py-3 px-4 text-center">Jatah Kuota</th>
                        <th class="py-3 px-4">Jadwal Penyaluran</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#1d2c4d] font-medium text-slate-700 dark:text-slate-300">
                    @forelse($programs as $prog)
                        @php
                            $userRtId = $user->rt_id;
                            $rtQuota = $userRtId ? $prog->getQuotaForRt($userRtId) : $prog->total_kuota;
                            $rtFilled = $userRtId ? $prog->getRecipientsCountForRt($userRtId) : $prog->recipients->count();
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-[#162441]/60 transition-colors">
                            <!-- 1. Nama Program -->
                            <td class="py-4 px-5">
                                <a href="{{ route('social-aid.programs.show', $prog->id) }}" wire:navigate class="font-bold text-slate-900 dark:text-white text-sm hover:text-emerald-700 dark:hover:text-emerald-400 transition">
                                    {{ $prog->nama_program }}
                                </a>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center space-x-1.5">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-[#172440] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-[#273d6b]/60">
                                        {{ $prog->sumber_dana_label }}
                                    </span>
                                </div>
                            </td>

                            <!-- 2. Bentuk Bantuan -->
                            <td class="py-4 px-4">
                                <div class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $prog->detail_bantuan }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $prog->jenis_bantuan_label }}</div>
                            </td>

                            <!-- 3. Kuota & Progres Usulan -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                @if($user->isRwLevel())
                                    <div class="text-xs font-bold text-slate-900 dark:text-white">
                                        <span class="text-emerald-700 dark:text-emerald-400">{{ $prog->recipients->count() }}</span>
                                        <span class="text-slate-400 dark:text-slate-500 font-normal">/ {{ $prog->total_kuota }} KK</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">Total Seluruh RT</div>
                                @else
                                    <div class="text-xs font-bold text-slate-900 dark:text-white">
                                        <span class="{{ $rtFilled >= $rtQuota ? 'text-emerald-700 dark:text-emerald-400' : 'text-amber-700 dark:text-amber-400' }}">{{ $rtFilled }}</span>
                                        <span class="text-slate-400 dark:text-slate-500 font-normal">/ {{ $rtQuota }} KK</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">Jatah Kuota RT Anda</div>
                                @endif
                            </td>

                            <!-- 4. Jadwal Penyaluran -->
                            <td class="py-4 px-4 text-xs whitespace-nowrap">
                                <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $prog->tanggal_penyaluran->locale('id')->translatedFormat('d F Y') }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-xs" title="{{ $prog->lokasi_pengambilan }}">{{ $prog->lokasi_pengambilan }}</div>
                            </td>

                            <!-- 5. Status Program -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                @if($prog->status_program === 'pendataan_rt')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                        Pendataan RT
                                    </span>
                                @elseif($prog->status_program === 'verifikasi_rw')
                                    @if($prog->isReadyForDistribution())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 dark:bg-sky-950/40 text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60" title="Seluruh kuota terverifikasi, menunggu jadwal penyaluran">
                                            Siap Disalurkan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-800 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/60">
                                            Verifikasi RW
                                        </span>
                                    @endif
                                @elseif($prog->status_program === 'penyaluran')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                        Tahap Penyaluran
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-[#172440] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-[#273d6b]/60">
                                        Selesai
                                    </span>
                                @endif
                            </td>

                            <!-- 6. Aksi -->
                            <td class="py-4 px-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center space-x-1.5 justify-end">
                                    <a href="{{ route('social-aid.programs.show', $prog->id) }}" 
                                       wire:navigate 
                                       class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-600 dark:hover:bg-emerald-600 text-emerald-700 dark:text-emerald-400 hover:text-white dark:hover:text-white border border-emerald-200 dark:border-emerald-800/60 transition inline-flex items-center justify-center shadow-2xs group"
                                       title="Buka Detail & Usulan Program">
                                        <svg class="w-4 h-4 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>

                                    @if($user->isRwLevel() && in_array($prog->status_program, ['pendataan_rt', 'verifikasi_rw']))
                                        <button type="button" 
                                                wire:click="confirmDeleteProgram({{ $prog->id }})" 
                                                class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-[#172440] hover:bg-rose-50 dark:hover:bg-rose-950/40 text-slate-400 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 border border-slate-200 dark:border-[#273d6b]/60 hover:border-rose-200 dark:hover:border-rose-800/60 transition inline-flex items-center justify-center shadow-2xs cursor-pointer" 
                                                title="Hapus Program Bantuan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500 dark:text-slate-400">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-[#172440] text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-[#273d6b]/60 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                </div>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Belum ada program bantuan sosial</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Pengurus RW dapat menambahkan program bantuan baru melalui tombol di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($programs->total() > 0)
            <div class="p-4 bg-slate-50/50 dark:bg-[#0c1427]/60 border-t border-slate-100 dark:border-[#1d2c4d]">
                {{ $programs->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL BUAT PROGRAM BARU (KHUSUS PENGURUS RW) -->
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#111b2f] rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 dark:border-[#1d2c4d] animate-in fade-in zoom-in-95 duration-150 my-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-[#1d2c4d] mb-5">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Buat Program Bantuan Sosial Baru</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Tentukan nama bantuan dan bagikan jatah kuota penerima secara proporsional ke tiap RT</p>
                </div>
                <button type="button" wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit="saveProgram" class="space-y-4 text-xs">
                <!-- 1. Nama Program -->
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Program Bantuan</label>
                    <input type="text" 
                           wire:model="nama_program" 
                           placeholder="Contoh: Bantuan Beras CBP Bulog 10 Kg - Oktober 2026" 
                           class="w-full h-10 px-3 rounded-xl border border-slate-300 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-1 focus:ring-emerald-500 font-medium transition">
                    @error('nama_program') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 2. Sumber Dana & Bentuk Bantuan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Sumber Alokasi Dana</label>
                        <select wire:model="sumber_dana" class="w-full h-10 px-2.5 rounded-xl border border-slate-300 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] text-slate-800 dark:text-slate-100 font-medium focus:ring-1 focus:ring-emerald-500 transition">
                            <option value="pemerintah_pusat">Pemerintah Pusat (Kemensos / Bapanas)</option>
                            <option value="pemda_kelurahan">Pemerintah Daerah / Kelurahan</option>
                            <option value="kas_rw">Kas Induk RW 01 (Mandiri)</option>
                            <option value="csr_donatur">CSR Perusahaan / Donatur Warga</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Bantuan</label>
                        <select wire:model="jenis_bantuan" class="w-full h-10 px-2.5 rounded-xl border border-slate-300 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] text-slate-800 dark:text-slate-100 font-medium focus:ring-1 focus:ring-emerald-500 transition">
                            <option value="sembako_barang">Paket Sembako / Barang Fisik</option>
                            <option value="uang_tunai">Bantuan Uang Tunai (BLT)</option>
                        </select>
                    </div>
                </div>

                <!-- 3. Rincian Barang / Nominal -->
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Rincian Paket Bantuan / Besaran Uang</label>
                    <input type="text" 
                           wire:model="detail_bantuan" 
                           placeholder="Contoh: Beras 10 Kg + Minyak 2 Liter ATAU Rp 300.000" 
                           class="w-full h-10 px-3 rounded-xl border border-slate-300 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-1 focus:ring-emerald-500 font-medium transition">
                    @error('detail_bantuan') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 4. Tanggal & Lokasi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Rencana Penyaluran</label>
                        <input type="date" 
                               wire:model="tanggal_penyaluran" 
                               class="w-full h-10 px-3 rounded-xl border border-slate-300 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] text-slate-800 dark:text-slate-100 font-medium focus:ring-1 focus:ring-emerald-500 transition">
                        @error('tanggal_penyaluran') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Lokasi Pengambilan Bantuan</label>
                        <input type="text" 
                               wire:model="lokasi_pengambilan" 
                               placeholder="Contoh: Balai RW 01 / Rumah Ketua RT Masing-masing" 
                               class="w-full h-10 px-3 rounded-xl border border-slate-300 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] text-slate-800 dark:text-slate-100 font-medium focus:ring-1 focus:ring-emerald-500 transition">
                        @error('lokasi_pengambilan') <p class="text-rose-600 dark:text-rose-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- 5. Pembagian Jatah Kuota per RT (Sesuai Bab 5.2) -->
                <div class="pt-3 border-t border-slate-100 dark:border-[#1d2c4d]">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <label class="block font-bold text-slate-800 dark:text-slate-200">Alokasi Jatah Kuota per RT</label>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Isi jumlah kuota paket yang dialokasikan ke masing-masing RT</p>
                        </div>
                        <div class="px-3 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-xs font-bold text-emerald-800 dark:text-emerald-300">
                            Total Kuota: {{ $this->totalAllocatedQuota }} Paket
                        </div>
                    </div>

                    @error('rtQuotas') <p class="text-rose-600 dark:text-rose-400 text-[11px] mb-2">{{ $message }}</p> @enderror

                    <div class="overflow-hidden border border-slate-200 dark:border-[#1d2c4d] rounded-xl">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-[#0c1427] text-slate-600 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-[#1d2c4d]">
                                <tr>
                                    <th class="py-2.5 px-3">Wilayah RT</th>
                                    <th class="py-2.5 px-3 text-center">Ketua RT</th>
                                    <th class="py-2.5 px-3 text-center">Jumlah KK Rentan</th>
                                    <th class="py-2.5 px-3 text-right">Alokasi Kuota</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-[#1d2c4d] font-medium">
                                @foreach($rtsData as $data)
                                    @php $rt = $data['rt']; @endphp
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-[#162441]/50 transition-colors">
                                        <td class="py-2.5 px-3 font-bold text-slate-800 dark:text-slate-200">
                                            Rukun Tetangga {{ str_pad((string)((int)$rt->nomor_rt), 2, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center text-slate-600 dark:text-slate-400">
                                            {{ $rt->nama_ketua ?: 'Belum diisi' }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                                {{ $data['poor_count'] }} KK Miskin
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 text-right">
                                            <input type="number" 
                                                   min="0"
                                                   wire:model.live="rtQuotas.{{ $rt->id }}" 
                                                   class="w-20 h-8 px-2 text-right rounded-lg border border-slate-300 dark:border-[#1d2c4d] bg-white dark:bg-[#0c1427] font-bold text-slate-900 dark:text-slate-100 focus:ring-1 focus:ring-emerald-500 transition">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="pt-4 border-t border-slate-100 dark:border-[#1d2c4d] flex items-center justify-end space-x-2">
                    <button type="button" 
                            wire:click="$set('showCreateModal', false)" 
                            class="h-10 px-4 rounded-xl bg-white dark:bg-[#172440] border border-slate-300 dark:border-[#273d6b]/60 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-50 dark:hover:bg-[#1d2c4d] transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="h-10 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition shadow-xs cursor-pointer">
                        Buat Program Bantuan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL KONFIRMASI HAPUS PROGRAM BANSOS -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#111b2f] rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-[#1d2c4d] space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center space-x-3.5">
                <div class="w-11 h-11 rounded-2xl bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/50 flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">Hapus Program Bantuan Sosial?</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-[#0c1427] border border-slate-200/80 dark:border-[#1d2c4d] space-y-1">
                <div class="text-xs font-bold text-slate-800 dark:text-white">{{ $deletingProgramName }}</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Seluruh data alokasi kuota RT dan usulan penerima pada program ini akan ikut dihapus permanen.</div>
            </div>

            <div class="flex items-center justify-end space-x-2.5 pt-2 border-t border-slate-100 dark:border-[#1d2c4d]">
                <button type="button" 
                        wire:click="cancelDeleteProgram" 
                        class="h-9 px-4 rounded-xl border border-slate-200 dark:border-[#273d6b]/60 bg-white dark:bg-[#172440] hover:bg-slate-50 dark:hover:bg-[#1d2c4d] text-slate-700 dark:text-slate-300 text-xs font-bold transition cursor-pointer">
                    Batal
                </button>
                <button type="button" 
                        wire:click="executeDeleteProgram" 
                        class="h-9 px-5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                    Ya, Hapus Program
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
