<div>
    <!-- Page Header & Segmented Switcher -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-6 border-b border-slate-200">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                    Pendataan Sensus Warga Mandiri
                </h1>
                <p class="text-sm text-slate-600 mt-1.5 max-w-2xl">
                    Formulir resmi pendaftaran tempat tinggal dan anggota keluarga untuk pembaruan basis data kependudukan RT/RW.
                </p>
            </div>

            <!-- Segmented Control Tabs -->
            <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200 shrink-0 self-start sm:self-auto">
                <button type="button" 
                    wire:click="$set('activeTab', 'form')" 
                    class="px-4 py-2 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 {{ $activeTab === 'form' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    <svg class="w-3.5 h-3.5 {{ $activeTab === 'form' ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Formulir Sensus</span>
                </button>
                <button type="button" 
                    wire:click="$set('activeTab', 'tracking')" 
                    class="px-4 py-2 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 {{ $activeTab === 'tracking' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    <svg class="w-3.5 h-3.5 {{ $activeTab === 'tracking' ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Lacak Status Tiket</span>
                </button>
            </div>
        </div>
    </div>

    @if ($activeTab === 'tracking')
        <!-- ============================================== -->
        <!-- TAB 1: LACAK STATUS TIKET SENSUS -->
        <!-- ============================================== -->
        <div class="max-w-2xl mx-auto space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
                <div class="mb-5">
                    <h2 class="text-base font-bold text-slate-900">Periksa Status Pengajuan</h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Masukkan kode tiket yang Anda terima saat mengirimkan formulir sensus mandiri (contoh: SNS-{{ date('Ymd') }}-XXXX).
                    </p>
                </div>

                <form wire:submit.prevent="trackTicket" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kode Tiket Pengajuan</label>
                        <div class="flex flex-col sm:flex-row gap-2.5">
                            <input type="text" 
                                wire:model.defer="trackingTicketInput" 
                                placeholder="SNS-20261020-XXXX" 
                                class="flex-1 font-mono tracking-wider text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3.5 py-2.5 outline-none">
                            <button type="submit" 
                                wire:loading.attr="disabled"
                                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                                <span wire:loading.remove wire:target="trackTicket">Periksa Tiket</span>
                                <span wire:loading wire:target="trackTicket">Mencari...</span>
                            </button>
                        </div>
                        @if ($trackingError)
                            <p class="mt-2 text-xs font-medium text-rose-600">{{ $trackingError }}</p>
                        @endif
                    </div>
                </form>

                @if ($trackedSubmission)
                    <div class="mt-8 pt-6 border-t border-slate-100 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div>
                                <span class="text-xs text-slate-500 block">Nomor Tiket</span>
                                <span class="font-mono font-bold text-slate-900 text-sm">{{ $trackedSubmission->kode_tiket }}</span>
                            </div>
                            <div>
                                @if ($trackedSubmission->status === 'menunggu_verifikasi')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                        Menunggu Verifikasi RT
                                    </span>
                                @elseif ($trackedSubmission->status === 'disetujui')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Disetujui & Masuk Sistem
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Perlu Perbaikan / Ditolak
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block font-medium">Alamat Rumah</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $trackedSubmission->nomor_rumah }}</span>
                                <span class="text-[11px] text-slate-500">RT {{ str_pad((string)((int)$trackedSubmission->rt?->nomor_rt), 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block font-medium">Kepala Keluarga / Pelapor</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $trackedSubmission->nama_pengirim }}</span>
                                <span class="text-[11px] text-slate-500">{{ $trackedSubmission->no_wa_pengirim }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block font-medium">Nomor Kartu Keluarga (KK)</span>
                                <span class="font-mono font-bold text-slate-900 text-sm mt-0.5 block">{{ $trackedSubmission->no_kk }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block font-medium">Total Anggota Keluarga</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $trackedSubmission->total_anggota }} Jiwa</span>
                            </div>
                        </div>

                        @if ($trackedSubmission->catatan_verifikasi)
                            <div class="p-4 rounded-xl bg-amber-50/80 border border-amber-200 text-xs space-y-1">
                                <span class="font-bold text-amber-950 block">Catatan dari Pengurus RT:</span>
                                <p class="text-amber-900 leading-relaxed">{{ $trackedSubmission->catatan_verifikasi }}</p>
                            </div>
                        @endif

                        @if ($trackedSubmission->status === 'disetujui')
                            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 leading-relaxed">
                                Data sensus Anda telah disetujui secara resmi oleh pengurus dan telah tercatat dalam basis data kependudukan RT/RW.
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

    @elseif ($isSubmitted)
        <!-- ============================================== -->
        <!-- LAYAR KONFIRMASI SUKSES KIRIM SENSUS -->
        <!-- ============================================== -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-10 text-center">
                <div class="w-14 h-14 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-center mx-auto mb-4 text-emerald-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Formulir Sensus Berhasil Dikirim</h2>
                <p class="text-sm text-slate-600 mt-2 max-w-md mx-auto leading-relaxed">
                    Data tempat tinggal dan susunan anggota keluarga Anda telah masuk ke dalam antrean verifikasi pengurus RT setempat.
                </p>

                <!-- Box Kode Tiket -->
                <div class="my-6 p-5 rounded-2xl bg-slate-50 border border-slate-200 max-w-md mx-auto" x-data="{ copied: false }">
                    <span class="text-xs font-semibold text-slate-500 block mb-1">Kode Tiket Anda</span>
                    <div class="flex items-center justify-center gap-3">
                        <span class="font-mono text-2xl font-bold text-slate-900 tracking-wider">
                            {{ $submittedTicket }}
                        </span>
                        <button type="button" 
                            @click="navigator.clipboard.writeText('{{ $submittedTicket }}'); copied = true; setTimeout(() => copied = false, 2500)"
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 transition shadow-2xs">
                            <span x-show="!copied">Salin Tiket</span>
                            <span x-show="copied" class="text-emerald-700 font-bold">Tersalin</span>
                        </button>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">
                        Simpan nomor ini untuk memeriksa proses verifikasi melalui tab <strong>Lacak Status Tiket</strong>.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                    <button type="button" 
                        wire:click="resetForm" 
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition">
                        Isi Formulir Rumah Lain
                    </button>
                    <a href="{{ route('login') }}" 
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs">
                        Kembali ke Halaman Depan
                    </a>
                </div>
            </div>
        </div>

    @else
        <!-- ============================================== -->
        <!-- TAB 2: FORMULIR SENSUS WARGA MANDIRI -->
        <!-- ============================================== -->
        <form wire:submit.prevent="submit" class="space-y-6">

            <!-- KARTU 1: DATA TEMPAT TINGGAL -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="text-base font-bold text-slate-900">1. Data Tempat Tinggal</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Tentukan wilayah RT dan spesifikasi rumah yang Anda huni.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Wilayah Rukun Tetangga (RT) <span class="text-rose-500">*</span>
                        </label>
                        <select wire:model="rt_id" class="w-full h-11 text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3.5 bg-white outline-none">
                            <option value="">-- Pilih Wilayah RT --</option>
                            @foreach ($rts as $rt)
                                <option value="{{ $rt->id }}">RT {{ str_pad((string)((int)$rt->nomor_rt), 2, '0', STR_PAD_LEFT) }}</option>
                            @endforeach
                        </select>
                        @error('rt_id') <span class="text-rose-600 text-xs font-medium block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                         Alamat Rumah <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" wire:model.defer="nomor_rumah" oninput="this.value = this.value.toUpperCase()" placeholder="Contoh: BLOK B3 NO. 12" class="w-full h-11 text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3.5 outline-none uppercase font-semibold">
                        @error('nomor_rumah') <span class="text-rose-600 text-xs font-medium block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Status Hunian <span class="text-rose-500">*</span>
                        </label>
                        <select wire:model="status_huni" class="w-full h-11 text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3.5 bg-white outline-none">
                            <option value="dihuni_pemilik">Dihuni Pemilik Sendiri</option>
                            <option value="kontrak">Kontrak / Sewa / Kost</option>
                            <option value="kosong">Kosong (Belum Berpenghuni)</option>
                        </select>
                        @error('status_huni') <span class="text-rose-600 text-xs font-medium block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kategori Tempat Tinggal <span class="text-rose-500">*</span>
                        </label>
                        <select wire:model="kategori_tarif" class="w-full h-11 text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3.5 bg-white outline-none">
                            <option value="standar">Rumah Tinggal Reguler</option>
                            <option value="usaha">Tempat Usaha / Toko / Kantor</option>
                            <option value="kosong">Bangunan Kosong</option>
                        </select>
                        @error('kategori_tarif') <span class="text-rose-600 text-xs font-medium block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Catatan Khusus Tempat Tinggal <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="text" wire:model.defer="catatan_rumah" placeholder="Contoh: Pagar abu-abu, dekat pos satpam" class="w-full h-11 text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3.5 outline-none">
                    </div>
                </div>
            </div>

            <!-- KARTU 2: IDENTITAS KEPALA KELUARGA / PELAPOR -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="text-base font-bold text-slate-900">2. Identitas Penanggung Jawab Keluarga</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kontak kepala keluarga atau pelapor yang dapat dihubungi pengurus RT.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nama Kepala Keluarga / Pelapor <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" wire:model.defer="nama_pengirim" oninput="this.value = this.value.toUpperCase()" placeholder="NAMA LENGKAP SESUAI KTP" class="w-full h-11 text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3.5 outline-none uppercase font-semibold">
                        @error('nama_pengirim') <span class="text-rose-600 text-xs font-medium block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nomor Kartu Keluarga (KK) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="16" wire:model.defer="no_kk" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="16 digit angka No. KK" class="w-full h-11 font-mono text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3.5 outline-none">
                        @error('no_kk') <span class="text-rose-600 text-xs font-medium block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nomor WhatsApp Aktif <span class="text-rose-500">*</span>
                        </label>
                        <input type="tel" inputmode="numeric" maxlength="17" wire:model.defer="no_wa_pengirim" 
                               oninput="let v = this.value.replace(/[^0-9]/g, '').slice(0, 14); let p = v.match(/.{1,4}/g); this.value = p ? p.join('-') : '';" 
                               placeholder="Contoh: 0808-9898-9898" 
                               class="w-full h-11 font-mono text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3.5 outline-none">
                        <span class="text-xs text-slate-400 block mt-1">Nomor ini digunakan oleh pengurus RT jika ada konfirmasi atau perbaikan data. Format: 0808-9898-9898.</span>
                        @error('no_wa_pengirim') <span class="text-rose-600 text-xs font-medium block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- KARTU 3: SUSUNAN ANGGOTA KELUARGA -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">3. Susunan Anggota Keluarga</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Daftarkan Kepala Keluarga, Istri, Anak, serta anggota lain yang tinggal satu rumah.</p>
                    </div>

                    <!-- Split Button Tambah Anggota dengan Dropdown Pilihan Peran -->
                    <div x-data="{ open: false }" class="relative self-start sm:self-auto">
                        <div class="inline-flex items-stretch rounded-xl shadow-2xs border border-slate-200 bg-white">
                            <button type="button" 
                                wire:click="addMember()" 
                                class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 hover:text-emerald-700 hover:bg-slate-50 rounded-l-xl transition cursor-pointer">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>Tambah Anggota</span>
                            </button>
                            <button @click="open = !open" type="button" class="px-2.5 py-2 border-l border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-r-xl transition cursor-pointer" title="Pilih status peran anggota">
                                <svg class="w-3.5 h-3.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </div>

                        <div x-show="open" 
                             @click.outside="open = false" 
                             x-cloak 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-30">
                            <div class="px-3 py-1.5 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                Pilih Peran Anggota
                            </div>
                            <button type="button" wire:click="addMember('kepala_keluarga')" @click="open = false" class="w-full text-left px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 flex items-center justify-between cursor-pointer">
                                <span>Kepala Keluarga</span>
                                <span class="text-[10px] text-slate-400 font-semibold">L</span>
                            </button>
                            <button type="button" wire:click="addMember('istri')" @click="open = false" class="w-full text-left px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 flex items-center justify-between cursor-pointer">
                                <span>Istri</span>
                                <span class="text-[10px] text-slate-400 font-semibold">P</span>
                            </button>
                            <button type="button" wire:click="addMember('anak')" @click="open = false" class="w-full text-left px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 flex items-center justify-between cursor-pointer">
                                <span>Anak</span>
                                <span class="text-[10px] text-slate-400 font-semibold">L / P</span>
                            </button>
                            <button type="button" wire:click="addMember('orang_tua')" @click="open = false" class="w-full text-left px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 flex items-center justify-between cursor-pointer">
                                <span>Orang Tua</span>
                                <span class="text-[10px] text-slate-400 font-semibold">Kerabat</span>
                            </button>
                            <button type="button" wire:click="addMember('famili_lain')" @click="open = false" class="w-full text-left px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 flex items-center justify-between cursor-pointer">
                                <span>Famili Lain / Kerabat</span>
                                <span class="text-[10px] text-slate-400 font-semibold">Lainnya</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    @foreach ($keluarga as $index => $member)
                        <div class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50/60 space-y-4" wire:key="member-row-{{ $index }}">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-200/60">
                                <div class="flex items-center gap-2.5">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-white text-slate-800 border border-slate-200 shadow-2xs">
                                        Anggota {{ $index + 1 }}
                                    </span>
                                    <span class="text-xs font-semibold text-slate-600">
                                        @if(($member['status_keluarga'] ?? '') === 'kepala_keluarga')
                                            Kepala Keluarga
                                        @elseif(($member['status_keluarga'] ?? '') === 'istri')
                                            Istri
                                        @elseif(($member['status_keluarga'] ?? '') === 'anak')
                                            Anak
                                        @elseif(($member['status_keluarga'] ?? '') === 'orang_tua')
                                            Orang Tua
                                        @else
                                            Famili Lain
                                        @endif
                                    </span>
                                </div>

                                @if (count($keluarga) > 1)
                                    <button type="button" 
                                        wire:click="removeMember({{ $index }})" 
                                        class="text-xs text-rose-600 hover:text-rose-800 font-semibold px-2 py-1 rounded-md hover:bg-rose-50 transition cursor-pointer">
                                        Hapus
                                    </button>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                                <div class="sm:col-span-8">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Nama Lengkap (Sesuai KTP / Akta) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" wire:model.defer="keluarga.{{ $index }}.nama_lengkap" oninput="this.value = this.value.toUpperCase()" placeholder="NAMA LENGKAP SESUAI KTP" class="w-full h-10 text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3 bg-white outline-none uppercase font-semibold">
                                    @error("keluarga.{$index}.nama_lengkap") <span class="text-rose-600 text-xs font-medium block mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="sm:col-span-4">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        NIK (16 Digit) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="16" wire:model.defer="keluarga.{{ $index }}.nik" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="16 digit angka NIK" class="w-full h-10 font-mono text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3 bg-white outline-none">
                                    @error("keluarga.{$index}.nik") <span class="text-rose-600 text-xs font-medium block mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="sm:col-span-4">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Hubungan Keluarga <span class="text-rose-500">*</span>
                                    </label>
                                    <select wire:model.live="keluarga.{{ $index }}.status_keluarga" class="w-full h-10 text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3 bg-white outline-none">
                                        <option value="kepala_keluarga">Kepala Keluarga</option>
                                        <option value="istri">Istri</option>
                                        <option value="anak">Anak</option>
                                        <option value="famili_lain">Famili Lain / Kerabat</option>
                                        <option value="orang_tua">Orang Tua</option>
                                    </select>
                                </div>

                                <div class="sm:col-span-4">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Jenis Kelamin <span class="text-rose-500">*</span>
                                    </label>
                                    <select wire:model.live="keluarga.{{ $index }}.jenis_kelamin" class="w-full h-10 text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3 bg-white outline-none">
                                        <option value="L">Laki-Laki (L)</option>
                                        <option value="P">Perempuan (P)</option>
                                    </select>
                                </div>

                                <div class="sm:col-span-4">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Status Kependudukan <span class="text-rose-500">*</span>
                                    </label>
                                    <select wire:model.defer="keluarga.{{ $index }}.status_domisili" class="w-full h-10 text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3 bg-white outline-none">
                                        <option value="ktp_setempat">KTP Sesuai Alamat (Tetap)</option>
                                        <option value="domisili_luar">KTP Luar (Domisili)</option>
                                    </select>
                                </div>

                                <div class="sm:col-span-4">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Tempat Lahir
                                    </label>
                                    <input type="text" wire:model.defer="keluarga.{{ $index }}.tempat_lahir" oninput="this.value = this.value.toUpperCase()" placeholder="KOTA LAHIR" class="w-full h-10 text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3 bg-white outline-none uppercase">
                                </div>

                                <div class="sm:col-span-4">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Tanggal Lahir <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="date" wire:model.defer="keluarga.{{ $index }}.tanggal_lahir" class="w-full h-10 text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3 bg-white outline-none">
                                    @error("keluarga.{$index}.tanggal_lahir") <span class="text-rose-600 text-xs font-medium block mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="sm:col-span-4">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Agama
                                    </label>
                                    <select wire:model.defer="keluarga.{{ $index }}.agama" class="w-full h-10 text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3 bg-white outline-none">
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Khonghucu">Khonghucu</option>
                                    </select>
                                </div>

                                <div class="sm:col-span-5">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Pekerjaan / Profesi
                                    </label>
                                    <input type="text" wire:model.defer="keluarga.{{ $index }}.pekerjaan" oninput="this.value = this.value.toUpperCase()" placeholder="CONTOH: KARYAWAN SWASTA, WIRASWASTA, PELAJAR" class="w-full h-10 text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3 bg-white outline-none uppercase">
                                </div>

                                <div class="sm:col-span-4">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        No. WhatsApp / HP Anggota
                                    </label>
                                    <input type="tel" inputmode="numeric" maxlength="17" wire:model.defer="keluarga.{{ $index }}.no_hp" 
                                           oninput="let v = this.value.replace(/[^0-9]/g, '').slice(0, 14); let p = v.match(/.{1,4}/g); this.value = p ? p.join('-') : '';" 
                                           placeholder="Contoh: 0808-9898-9898" 
                                           class="w-full h-10 font-mono text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3 bg-white outline-none">
                                </div>

                                <div class="sm:col-span-3">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Golongan Darah
                                    </label>
                                    <select wire:model.defer="keluarga.{{ $index }}.golongan_darah" class="w-full h-10 text-sm rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3 bg-white outline-none">
                                        <option value="-">-</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="AB">AB</option>
                                        <option value="O">O</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- ACTION BAR & SUBMIT -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-slate-500 leading-relaxed max-w-xl">
                    Dengan mengirimkan data ini, Anda menyatakan bahwa informasi kependudukan yang diisi adalah benar untuk kepentingan administrasi warga lingkungan RT/RW.
                </p>

                <button type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full sm:w-auto shrink-0 px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                    <span wire:loading.remove wire:target="submit">Kirim Formulir Sensus</span>
                    <span wire:loading wire:target="submit" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </form>
    @endif
</div>
