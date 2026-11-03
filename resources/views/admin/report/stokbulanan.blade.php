@extends('layouts.admin')

@section('title')
<div>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Laporan Stok Bulanan</h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Monitoring ketersediaan stok bahan pokok di Banjarmasin</p>
</div>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" action="/report/stok/bulanan/search" class="flex flex-wrap items-center gap-2">
            @php
                $bulanMap = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
            @endphp
            <select name="bulan" required class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 outline-none">
                <option value="">-- Bulan --</option>
                @foreach ($bulanMap as $k => $v)
                    <option value="{{ $k }}" {{ $month == $k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>
            <select name="tahun" required class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 outline-none">
                <option value="">-- Tahun --</option>
                @for ($y = 2021; $y <= 2027; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition">
                <i class="fas fa-filter text-xs mr-1"></i> Tampilkan
            </button>
        </form>
        <div class="text-xs text-slate-500">Periode: <b class="text-slate-800">{{ $bulanMap[$month] ?? $month }} {{ $year }}</b></div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Perbandingan Stok Bahan Pokok</h3>
            <span class="text-xs text-slate-400">{{ $data->count() }} Komoditi</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3 w-10 text-center">No</th>
                        <th class="py-3 px-3 min-w-[160px]">Komoditi</th>
                        <th class="py-3 px-2 text-center w-16">Satuan</th>
                        <th class="py-3 px-3 text-right">Stok Bulan Lalu</th>
                        <th class="py-3 px-3 text-right">Stok Terkini</th>
                        <th class="py-3 px-3 text-right">Perubahan</th>
                        <th class="py-3 px-3 text-center">Perubahan (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $no = 1; @endphp
                    @forelse ($data as $b)
                        @php
                            $pct = ($b->stok_bulanlalu > 0) ? round(($b->stok_terkini / $b->stok_bulanlalu) * 100 - 100, 2) : ($b->stok_terkini > 0 ? 100 : 0);
                        @endphp
                    <tr class="hover:bg-slate-50/70">
                        <td class="py-3 px-3 text-center text-slate-400 font-medium">{{ $no++ }}</td>
                        <td class="py-3 px-3 font-bold text-slate-900">{{ $b->nama }}</td>
                        <td class="py-3 px-2 text-center text-[11px] text-slate-500">{{ $b->satuan ? $b->satuan->nama : '-' }}</td>
                        <td class="py-3 px-3 text-right font-mono text-xs text-slate-700">{{ number_format($b->stok_bulanlalu) }}</td>
                        <td class="py-3 px-3 text-right font-mono text-xs font-bold text-slate-900">{{ number_format($b->stok_terkini) }}</td>
                        <td class="py-3 px-3 text-right font-mono text-xs font-semibold {{ $b->perubahan > 0 ? 'text-emerald-600' : ($b->perubahan < 0 ? 'text-rose-600' : 'text-slate-400') }}">
                            {{ $b->perubahan > 0 ? '+ ' . number_format($b->perubahan) : ($b->perubahan < 0 ? '- ' . number_format(abs($b->perubahan)) : '0') }}
                        </td>
                        <td class="py-3 px-3 text-center font-bold text-[11px]">
                            @if ($pct > 0)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    <i class="fas fa-arrow-up text-[9px]"></i> {{ $pct }}%
                                </span>
                            @elseif ($pct < 0)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 border border-rose-100">
                                    <i class="fas fa-arrow-down text-[9px]"></i> {{ abs($pct) }}%
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">0%</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada data untuk periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
