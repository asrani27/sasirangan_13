@extends('layouts.public')

@section('title', 'Early Warning System Kenaikan Bapok - DEDIKASI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Alert Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-rose-600 via-rose-500 to-amber-600 text-white p-6 sm:p-8 shadow-md">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-xs text-xs font-bold tracking-wide">
                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                    <span>EARLY WARNING SYSTEM</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">DEDIKASI Kenaikan Harga Bapok</h1>
                <p class="text-rose-100 text-xs sm:text-sm max-w-2xl leading-relaxed">
                    Sistem pemantauan dini otomatis untuk komoditi pangan yang mengalami kenaikan harga signifikan melebihi ambang batas toleransi inflasi terhadap harga acuan awal bulan.
                </p>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 text-center min-w-44">
                <div class="text-[11px] uppercase font-bold text-rose-200 tracking-wider">Tanggal Pantauan</div>
                <div class="text-lg font-black mt-0.5">{{ \Carbon\Carbon::parse($today ?? date('Y-m-d'))->translatedFormat('d F Y') }}</div>
            </div>
        </div>
    </div>

    <!-- EWS Table -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-6 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-lg font-black text-slate-900">Daftar Komoditi Terkena Peringatan DEDIKASI</h2>
                <p class="text-xs text-slate-500">Bahan pangan dengan kenaikan harga tertinggi hari ini</p>
            </div>
            <div class="text-xs font-semibold px-3 py-1 rounded-full bg-rose-100 text-rose-800">
                {{ count($ews) }} Komoditi Terdeteksi
            </div>
        </div>

        @if(count($ews) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5 w-12 text-center">No</th>
                            <th class="px-5 py-3.5">Pasar</th>
                            <th class="px-5 py-3.5">Komoditi</th>
                            <th class="px-5 py-3.5 text-right">Harga Acuan (Awal Bulan)</th>
                            <th class="px-5 py-3.5 text-right">Harga Hari Ini</th>
                            <th class="px-5 py-3.5 text-center">Kenaikan (%)</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($ews as $index => $item)
                            <tr class="hover:bg-rose-50/40 transition">
                                <td class="px-5 py-3.5 text-center text-xs text-slate-400 font-mono">{{ $index + 1 }}</td>
                                <td class="px-5 py-3.5 font-bold text-slate-900">{{ $item->pasar->nama ?? '-' }}</td>
                                <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $item->bahan->nama ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-right font-mono text-slate-600">
                                    Rp {{ number_format($item->acuan, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-mono font-bold text-rose-700">
                                    Rp {{ number_format($item->harga, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-1 font-mono font-black text-rose-600">
                                        <i class="fas fa-arrow-trend-up text-xs"></i>
                                        +{{ number_format($item->kenaikan, 2, ',', '.') }}%
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-black bg-rose-500 text-white shadow-xs tracking-wider">
                                        NAIK
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-16 text-center space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mx-auto shadow-xs">
                    <i class="fas fa-circle-check"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Kondisi Pasar Terkendali</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Tidak ada komoditi pangan yang mengalami kenaikan ekstrem melebihi batas acuan hari ini di pasar-pasar Kota Banjarmasin.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
