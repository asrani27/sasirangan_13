@extends('layouts.admin')

@section('title')
<div>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Laporan Rata-Rata Harian</h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pantauan harga komoditi bahan pokok antar pasar per tanggal</p>
</div>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" action="/report/harga/rata-rata/harian/search" class="flex items-center gap-2">
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"><i class="fas fa-calendar-day"></i></span>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:bg-white focus:border-emerald-600 outline-none">
            </div>
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                <i class="fas fa-filter text-xs"></i>
                <span>Tampilkan Data</span>
            </button>
        </form>

        <div class="text-xs text-slate-500 font-medium">
            Tanggal Pantauan: <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM Y') }}</span>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Rekapitulasi Harga Harian Antar Pasar</h3>
                <p class="text-[11px] text-slate-400">Total komoditi: {{ $data->count() }} item</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3 w-10 text-center">No</th>
                        <th class="py-3 px-3 min-w-[160px]">Komoditi Bahan</th>
                        <th class="py-3 px-2 text-center w-16">Satuan</th>
                        @foreach ($pasar as $p)
                            <th class="py-3 px-3 text-right font-semibold">{{ $p->nama }}</th>
                        @endforeach
                        <th class="py-3 px-3 text-right bg-emerald-50/70 text-emerald-900 font-bold border-l border-emerald-100">Rata-Rata (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $no = 1; @endphp
                    @forelse ($data as $b)
                    <tr class="hover:bg-slate-50/70">
                        <td class="py-3 px-3 text-center text-slate-400 font-medium">{{ $no++ }}</td>
                        <td class="py-3 px-3 font-bold text-slate-900 text-xs">{{ $b->nama }}</td>
                        <td class="py-3 px-2 text-center text-[11px] text-slate-500 font-medium">{{ $b->satuan ? $b->satuan->nama : '-' }}</td>
                        @foreach ($b->pasar as $ht)
                            <td class="py-3 px-3 text-right font-mono text-xs {{ $ht->hargaToday > 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                {{ $ht->hargaToday == 0 ? '-' : number_format($ht->hargaToday) }}
                            </td>
                        @endforeach
                        @php
                            $avg = count($pasar) > 0 ? ceil($b->pasar->sum('hargaToday') / count($pasar)) : 0;
                        @endphp
                        <td class="py-3 px-3 text-right font-mono font-bold text-xs bg-emerald-50/40 text-emerald-800 border-l border-emerald-100">
                            {{ $avg > 0 ? 'Rp ' . number_format($avg) : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 4 + count($pasar) }}" class="py-8 text-center text-slate-400">Tidak ada data untuk tanggal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
