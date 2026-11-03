@extends('layouts.admin')

@section('title')
<div>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Pencatatan Stok Bahan Pokok</h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Monitoring dan pencatatan volume ketersediaan pangan per minggu di pasar</p>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h3 class="text-base font-bold text-slate-900">Pasar Pemantauan Stok</h3>
            <p class="text-xs text-slate-500">Pilih titik pasar untuk memperbarui data stok mingguan</p>
        </div>
        <div class="relative w-full sm:w-64">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="filterStokPasar" placeholder="Cari nama pasar..." class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none">
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="stokPasarGrid">
        @forelse ($pasar as $p)
        <div class="stok-pasar-card group bg-white rounded-2xl border border-slate-200/80 hover:border-emerald-500/50 shadow-xs hover:shadow-md transition p-5 flex flex-col justify-between">
            <div class="space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-base group-hover:bg-teal-600 group-hover:text-white transition shadow-xs">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-teal-50 text-teal-700 border border-teal-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                        Stok Aktif
                    </span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Titik Pasar</span>
                    <h3 class="text-base font-bold text-slate-900 group-hover:text-emerald-700 transition pasar-title">{{ $p->nama }}</h3>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] text-slate-400"><i class="fas fa-calendar-week mr-1"></i>Minggu ke-{{ now()->weekOfMonth }} ({{ now()->translatedFormat('M Y') }})</span>
                <a href="/input/stok/{{ $p->id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition">
                    <span>Input Stok</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-slate-200 text-slate-400 text-sm">
            Tidak ada pasar yang diset aktif stok.
        </div>
        @endforelse
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('filterStokPasar');
        const cards = document.querySelectorAll('.stok-pasar-card');
        if (input) {
            input.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase().trim();
                cards.forEach(c => {
                    const name = c.querySelector('.pasar-title')?.textContent.toLowerCase() || '';
                    c.style.display = name.includes(term) ? '' : 'none';
                });
            });
        }
    });
</script>
@endpush
@endsection
