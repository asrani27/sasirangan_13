@extends('layouts.admin')

@section('title')
<div>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Pencatatan Harga Bahan Pokok</h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola data harga harian komoditi pangan di pasar Kota Banjarmasin</p>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-emerald-600 to-teal-800 rounded-3xl p-6 text-white shadow-lg flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="space-y-1">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/20 text-white text-[11px] font-semibold">
                <i class="fas fa-bolt text-amber-300"></i>
                <span>Sinkronisasi Otomatis</span>
            </span>
            <h2 class="text-lg sm:text-xl font-bold">Inisialisasi Data Harga Hari Ini</h2>
            <p class="text-xs text-emerald-100 max-w-xl">
                Salin harga komoditi acuan kemarin ke hari ini serentak untuk seluruh pasar agar operator langsung mengedit harga terkini.
            </p>
        </div>
        <a href="/input/harga/bahan/sekarang" onclick="return confirm('Inisialisasi catatan harga komoditi hari ini?')" class="px-5 py-2.5 bg-white text-emerald-800 hover:bg-emerald-50 font-bold text-xs rounded-xl shadow-md transition shrink-0 inline-flex items-center gap-2">
            <i class="fas fa-arrows-rotate"></i>
            <span>Store Harga Hari Ini</span>
        </a>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h3 class="text-base font-bold text-slate-900">Daftar Titik Pasar</h3>
            <p class="text-xs text-slate-500">Pilih pasar untuk membuka lembar kerja pencatatan harga</p>
        </div>
        <div class="relative w-full sm:w-64">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="pasarFilterInput" placeholder="Cari pasar..." class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none">
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="pasarGrid">
        @forelse ($pasar as $p)
        <div class="pasar-card group bg-white rounded-2xl border border-slate-200/80 hover:border-emerald-500/50 shadow-xs hover:shadow-md transition p-5 flex flex-col justify-between">
            <div class="space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base group-hover:bg-emerald-600 group-hover:text-white transition shadow-xs">
                        <i class="fas fa-shop"></i>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $p->tampil_stok == 'Y' ? 'bg-teal-50 text-teal-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ $p->tampil_stok == 'Y' ? 'Aktif Stok' : 'Harga Saja' }}
                    </span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pasar Pantau</span>
                    <h3 class="text-base font-bold text-slate-900 group-hover:text-emerald-700 transition pasar-name">{{ $p->nama }}</h3>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] text-slate-400"><i class="fas fa-calendar mr-1"></i>{{ now()->translatedFormat('d M Y') }}</span>
                <a href="/input/harga/{{ $p->id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-emerald-600 text-white rounded-lg text-xs font-semibold transition">
                    <span>Input</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-slate-200 text-slate-400 text-sm">
            Belum ada data pasar.
        </div>
        @endforelse
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('pasarFilterInput');
        const cards = document.querySelectorAll('.pasar-card');
        if (input) {
            input.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase().trim();
                cards.forEach(c => {
                    const name = c.querySelector('.pasar-name')?.textContent.toLowerCase() || '';
                    c.style.display = name.includes(term) ? '' : 'none';
                });
            });
        }
    });
</script>
@endpush
@endsection
