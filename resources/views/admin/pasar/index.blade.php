@extends('layouts.admin')

@section('title')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Master Data Pasar</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola titik lokasi pasar pantauan, enumerator survei, dan komoditi bahan pokok</p>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-5">
    <!-- Metric Cards Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                <i class="fas fa-shop text-lg"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Titik Pasar</p>
                <div class="text-xl font-black text-slate-900">{{ $totalPasar ?? $data->total() }} <span class="text-xs font-semibold text-slate-400">Lokasi</span></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 border border-sky-100">
                <i class="fas fa-map-location-dot text-lg"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Koordinat GPS Aktif</p>
                <div class="text-xl font-black text-slate-900">{{ $totalGps ?? 0 }} <span class="text-xs font-semibold text-slate-400">Titik</span></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100">
                <i class="fas fa-chart-line text-lg"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tampil Di Info Stok</p>
                <div class="text-xl font-black text-slate-900">{{ $totalAktifStok ?? 0 }} <span class="text-xs font-semibold text-slate-400">Pasar</span></div>
            </div>
        </div>
    </div>

    <!-- Toolbar: Action Buttons & Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2.5">
            <button type="button" onclick="openTambahModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                <i class="fas fa-plus"></i>
                <span>Tambah Pasar Baru</span>
            </button>
            <a href="/data/pasar/generateharga" onclick="return confirm('Generate entri harga bapok kosong hari ini untuk semua pasar?')" class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition shadow-xs">
                <i class="fas fa-arrows-rotate text-emerald-400"></i>
                <span>Generate Harga Hari Ini</span>
            </a>
        </div>

        <form method="GET" action="/data/pasar" class="relative w-full md:w-80">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pasar..." class="w-full pl-9 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 placeholder-slate-400 focus:bg-white focus:border-emerald-600 outline-none transition">
            @if(request('search'))
                <a href="/data/pasar" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs" title="Reset filter">
                    <i class="fas fa-circle-xmark"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <h3 class="text-sm font-bold text-slate-900">Daftar Titik Pasar Pantauan</h3>
            </div>
            <span class="text-xs text-slate-400 font-medium">Menampilkan {{ $data->count() }} dari {{ $data->total() }} pasar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4 min-w-[220px]">Nama Pasar & Lokasi</th>
                        <th class="py-3 px-4 min-w-[200px]">Petugas Enumerator</th>
                        <th class="py-3 px-4 w-28 text-center">Info Stok</th>
                        <th class="py-3 px-4 w-44 text-center">Aksi & Konfigurasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($data as $idx => $item)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3.5 px-4 text-center text-slate-400 font-medium">{{ $data->firstItem() + $idx }}</td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-slate-900 text-sm mb-1">{{ $item->nama }}</div>
                            <div class="flex flex-wrap items-center gap-1.5">
                                @if($item->lat && $item->long)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 text-[10px] font-semibold border border-sky-200/60" title="Radius: {{ $item->radius ?? 100 }}m">
                                        <i class="fas fa-location-dot text-[9px]"></i>
                                        <span>GPS Aktif ({{ $item->radius ?? 100 }}m)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 text-[10px] font-medium">
                                        <i class="fas fa-location-dot text-[9px] text-slate-400"></i>
                                        <span>Belum set GPS</span>
                                    </span>
                                @endif
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-[10px] font-semibold border border-indigo-200/60">
                                    <i class="fas fa-basket-shopping text-[9px]"></i>
                                    <span>{{ $item->bahan->count() }} Komoditi</span>
                                </span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="space-y-1.5">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($item->user as $u)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 font-semibold text-[11px] border border-emerald-200/60">
                                            <i class="fas fa-user text-[9px] text-emerald-600"></i>
                                            <span>{{ $u->name }}</span>
                                        </span>
                                    @empty
                                        <span class="text-slate-400 italic text-[11px]">Belum ada petugas</span>
                                    @endforelse
                                </div>
                                <div class="flex items-center gap-2 pt-0.5">
                                    <button type="button" onclick="openPetugasModal('{{ $item->id }}', '{{ addslashes($item->nama) }}')" class="inline-flex items-center gap-1 text-[11px] text-emerald-700 hover:text-emerald-800 font-bold hover:underline cursor-pointer">
                                        <i class="fas fa-user-plus text-[9px]"></i>
                                        <span>+ Petugas</span>
                                    </button>
                                    @if($item->user->count() > 0)
                                        <span class="text-slate-200">|</span>
                                        <a href="/data/pasar/{{ $item->id }}/hapus-petugas" onclick="return confirm('Hapus semua penugasan petugas untuk pasar ini?')" class="text-[11px] text-rose-500 hover:text-rose-700 hover:underline">
                                            Reset
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($item->tampil_stok == 'Y')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Aktif</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 font-medium text-[10px]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    <span>Tidak</span>
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="inline-flex items-center justify-center gap-1">
                                <a href="/data/pasar/lokasi/{{ $item->id }}" class="w-8 h-8 rounded-xl bg-sky-50 hover:bg-sky-600 text-sky-700 hover:text-white inline-flex items-center justify-center border border-sky-200/80 transition shadow-2xs" title="Atur Titik & Radius GPS">
                                    <i class="fas fa-map-location-dot text-xs"></i>
                                </a>
                                <a href="/data/pasar/bapok/{{ $item->id }}" class="w-8 h-8 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white inline-flex items-center justify-center border border-indigo-200/80 transition shadow-2xs" title="Kelola Komoditi Pantauan">
                                    <i class="fas fa-layer-group text-xs"></i>
                                </a>
                                <a href="/data/pasar/harga/{{ $item->id }}" class="w-8 h-8 rounded-xl bg-amber-50 hover:bg-amber-600 text-amber-700 hover:text-white inline-flex items-center justify-center border border-amber-200/80 transition shadow-2xs" title="Kelola Periode & Harga Acuan">
                                    <i class="fas fa-tag text-xs"></i>
                                </a>
                                <a href="/data/pasar/edit/{{ $item->id }}" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-700 text-slate-700 hover:text-white inline-flex items-center justify-center border border-slate-200 transition shadow-2xs" title="Edit Informasi Pasar">
                                    <i class="fas fa-pencil text-xs"></i>
                                </a>
                                <a href="/data/pasar/delete/{{ $item->id }}" onclick="return confirm('Apakah Anda yakin ingin menghapus pasar {{ addslashes($item->nama) }}?')" class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white inline-flex items-center justify-center border border-rose-200/80 transition shadow-2xs" title="Hapus Pasar">
                                    <i class="fas fa-trash-can text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-lg">
                                    <i class="fas fa-shop-slash"></i>
                                </span>
                                <p class="font-medium text-slate-500">Tidak ada data pasar ditemukan.</p>
                                @if(request('search'))
                                    <a href="/data/pasar" class="text-xs text-emerald-600 font-bold hover:underline">Reset filter pencarian</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($data->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $data->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Pasar -->
<div id="tambahPasarModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fas fa-shop"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900">Tambah Pasar Baru</h4>
                    <p class="text-[11px] text-slate-400">Daftarkan titik pasar pantauan baru</p>
                </div>
            </div>
            <button type="button" onclick="closeTambahModal()" class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center text-xs cursor-pointer">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <form action="/data/pasar/add" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Pasar</label>
                <input type="text" name="nama" placeholder="Contoh: Pasar Antasari" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:bg-white focus:border-emerald-600 outline-none transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tampilkan Di Menu Info Stok</label>
                <select name="tampil_stok" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-700 focus:bg-white focus:border-emerald-600 outline-none transition">
                    <option value="T">Tidak</option>
                    <option value="Y">Ya (Tampilkan)</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeTambahModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition cursor-pointer">Batal</button>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                    <i class="fas fa-plus"></i>
                    <span>Simpan Pasar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Petugas -->
<div id="petugasModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900">Atur Petugas Pasar</h4>
                    <p class="text-[11px] text-slate-400" id="modalPasarNama">Pilih enumerator</p>
                </div>
            </div>
            <button type="button" onclick="closePetugasModal()" class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center text-xs cursor-pointer">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="/data/pasar/petugas" class="space-y-4">
            @csrf
            <input type="hidden" name="pasar_id" id="modalPasarId">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Enumerator</label>
                <select name="user_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-700 focus:bg-white focus:border-emerald-600 outline-none transition">
                    <option value="">-- Pilih Petugas --</option>
                    @foreach ($user as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closePetugasModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition cursor-pointer">Batal</button>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                    <i class="fas fa-check"></i>
                    <span>Tugaskan</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
    function openTambahModal() {
        document.getElementById('tambahPasarModal').classList.remove('hidden');
    }
    function closeTambahModal() {
        document.getElementById('tambahPasarModal').classList.add('hidden');
    }
    function openPetugasModal(id, nama) {
        document.getElementById('modalPasarId').value = id;
        document.getElementById('modalPasarNama').textContent = 'Pasar: ' + nama;
        document.getElementById('petugasModal').classList.remove('hidden');
    }
    function closePetugasModal() {
        document.getElementById('petugasModal').classList.add('hidden');
    }

    // Close modals with Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeTambahModal();
            closePetugasModal();
        }
    });

    // Close on background click
    document.getElementById('tambahPasarModal')?.addEventListener('click', (e) => {
        if (e.target === document.getElementById('tambahPasarModal')) {
            closeTambahModal();
        }
    });
    document.getElementById('petugasModal')?.addEventListener('click', (e) => {
        if (e.target === document.getElementById('petugasModal')) {
            closePetugasModal();
        }
    });
</script>
@endpush
@endsection
