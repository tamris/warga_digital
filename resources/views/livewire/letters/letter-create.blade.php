<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-2xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <a href="{{ route('letters.index') }}" wire:navigate class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 flex items-center space-x-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Register Surat</span>
                </a>
                <span class="text-xs text-slate-300">•</span>
                <span class="text-xs text-slate-500 font-semibold">Formulir Penerbitan</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                Buat Surat Pengantar Warga
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Cari warga pemohon, lengkapi keperluan, dan sistem akan mengunci nomor urut serta mencetak surat resmi
            </p>
        </div>

        <!-- Pratinjau Nomor Surat -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-right shrink-0">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Estimasi No. Surat</span>
            <span class="text-sm font-mono font-extrabold text-slate-900 tracking-tight">{{ $previewNomorSurat }}</span>
        </div>
    </div>

    <form wire:submit="saveLetter" class="space-y-6">
        <!-- LANGKAH 1: IDENTITAS PEMOHON -->
        <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-2xs space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Langkah 1: Identitas Pemohon (Warga)</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Ketik nama atau NIK warga untuk mengisi otomatis biodata surat</p>
                </div>
                <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold flex items-center justify-center">1</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if($user->isAdminRw())
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Wilayah RT Pemohon <span class="text-rose-500">*</span>
                        </label>
                        <select wire:model.live="selectedRtId" 
                                class="w-full text-xs p-2.5 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 font-semibold text-slate-800">
                            @foreach($rts as $rt)
                                <option value="{{ $rt->id }}">RT {{ str_pad((string)((int)$rt->nomor_rt), 2, '0', STR_PAD_LEFT) }} ({{ $rt->nama_ketua ?? 'Ketua RT' }})</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Wilayah RT
                        </label>
                        <div class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-lg font-bold text-slate-700">
                            RT {{ $user->rt?->nomor_rt ? str_pad((string)((int)$user->rt->nomor_rt), 2, '0', STR_PAD_LEFT) : '01' }} - {{ $user->rt?->nama_ketua ?? $user->nama }}
                        </div>
                    </div>
                @endif

                <!-- Kolom Pencarian Warga -->
                <div class="relative">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Cari Warga Pemohon <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="searchResident"
                               placeholder="Ketik minimal 2 huruf nama pemohon atau NIK..."
                               {{ $selectedResidentId ? 'disabled' : '' }}
                               class="w-full pl-9 pr-3 py-2.5 text-xs bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 text-slate-900 placeholder-slate-400 {{ $selectedResidentId ? 'bg-slate-50 cursor-not-allowed font-bold' : '' }}">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    @error('selectedResidentId')
                        <span class="text-xs text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                    @enderror

                    <!-- Dropdown Hasil Pencarian Warga -->
                    @if($residentSuggestions->isNotEmpty())
                        <div class="absolute left-0 right-0 z-20 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto divide-y divide-slate-100">
                            @foreach($residentSuggestions as $suggest)
                                <button type="button" 
                                        wire:click="selectResident({{ $suggest->id }})"
                                        class="w-full text-left p-3 hover:bg-emerald-50/70 transition flex items-center justify-between text-xs">
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $suggest->nama_lengkap }}</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">NIK: <span class="font-mono">{{ $suggest->nik }}</span> • {{ $suggest->house?->nomor_rumah ?? 'Tanpa Rumah' }}</div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">Pilih</span>
                                </button>
                            @endforeach
                        </div>
                    @elseif($searchResident && strlen($searchResident) >= 2 && ! $selectedResidentId)
                        <div class="absolute left-0 right-0 z-20 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg p-3 text-xs text-slate-500 text-center">
                            Tidak ditemukan warga aktif dengan kata kunci tersebut di RT ini.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kartu Konfirmasi Warga Terpilih -->
            @if($selectedResident)
                <div class="p-4 bg-emerald-50/60 border border-emerald-200 rounded-xl mt-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-extrabold text-slate-900">{{ $selectedResident->nama_lengkap }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                {{ $selectedResident->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-600 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-0.5">
                            <div>NIK: <span class="font-mono font-bold">{{ $selectedResident->nik }}</span></div>
                            <div>No. KK: <span class="font-mono font-bold">{{ $selectedResident->no_kk }}</span></div>
                            <div>TTL: <span>{{ $selectedResident->tempat_lahir }}, {{ \Carbon\Carbon::parse($selectedResident->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}</span></div>
                            <div>Agama: <span>{{ $selectedResident->agama }}</span></div>
                            <div>Pekerjaan: <span>{{ $selectedResident->pekerjaan }}</span></div>
                            <div>Unit Rumah: <span>{{ $selectedResident->house?->nomor_rumah ?? '-' }}</span></div>
                        </div>
                        @if($selectedResident->no_wa)
                            <div class="pt-1 text-xs text-emerald-800 font-semibold flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5 fill-[#25D366]" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                <span>No. WhatsApp: {{ $selectedResident->formatted_wa }}</span>
                            </div>
                        @endif
                    </div>

                    <button type="button" 
                            wire:click="resetSelectedResident"
                            class="h-8 px-3 rounded-lg bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-semibold shrink-0 transition shadow-2xs">
                        Ganti Warga
                    </button>
                </div>
            @endif
        </div>

        <!-- LANGKAH 2: DETAIL KEPERLUAN SURAT -->
        <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-2xs space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Langkah 2: Detail Keperluan & Penandatangan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih keperluan dari daftar cepat atau tuliskan rincian keperluan surat</p>
                </div>
                <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold flex items-center justify-center">2</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tanggal Surat -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal Surat <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" 
                           wire:model="tanggalSurat" 
                           class="w-full text-xs p-2.5 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 text-slate-900">
                    @error('tanggalSurat') <span class="text-xs text-rose-600 font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Jenis Keperluan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Jenis Keperluan <span class="text-rose-500">*</span>
                    </label>
                    <select wire:model.live="selectedJenisKeperluan" 
                            class="w-full text-xs p-2.5 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 font-medium text-slate-900">
                        <option value="">-- Pilih Jenis Keperluan Surat --</option>
                        @foreach($letterTypes as $type)
                            <option value="{{ $type->nama_keperluan }}">{{ $type->nama_keperluan }}</option>
                        @endforeach
                        <option value="Lainnya">Lainnya (Tulis Manual)</option>
                    </select>
                    @error('selectedJenisKeperluan') <span class="text-xs text-rose-600 font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Input Keperluan Kustom jika memilih Lainnya -->
            @if($selectedJenisKeperluan === 'Lainnya')
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tuliskan Jenis Keperluan Spesifik <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           wire:model="customKeperluan" 
                           placeholder="Contoh: Pengantar Surat Keterangan Ahli Waris"
                           class="w-full text-xs p-2.5 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 text-slate-900">
                    @error('customKeperluan') <span class="text-xs text-rose-600 font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>
            @endif

            <!-- Keterangan Tambahan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Keterangan Tambahan / Spesifikasi Keperluan (Opsional)
                </label>
                <textarea wire:model="keteranganKeperluan" 
                          rows="2"
                          placeholder="Contoh: Untuk melengkapi persyaratan melamar pekerjaan di PT ABC..."
                          class="w-full text-xs p-2.5 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 text-slate-900 placeholder-slate-400"></textarea>
                @error('keteranganKeperluan') <span class="text-xs text-rose-600 font-semibold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Pejabat Penandatangan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Ketua RT Penandatangan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           wire:model="namaKetuaRt" 
                           placeholder="Nama Ketua RT..."
                           class="w-full text-xs p-2.5 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 text-slate-900 font-semibold">
                    @error('namaKetuaRt') <span class="text-xs text-rose-600 font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Mengetahui Ketua RW
                    </label>
                    <input type="text" 
                           wire:model="namaKetuaRw" 
                           placeholder="AMAT MAJA"
                           class="w-full text-xs p-2.5 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 text-slate-900 font-semibold">
                    @error('namaKetuaRw') <span class="text-xs text-rose-600 font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('letters.index') }}" 
               wire:navigate
               class="h-10 px-5 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center transition shadow-2xs">
                Batal
            </a>

            <button type="submit" 
                    class="h-10 px-6 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold inline-flex items-center space-x-2 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Terbitkan & Siapkan Cetak</span>
            </button>
        </div>
    </form>
</div>
