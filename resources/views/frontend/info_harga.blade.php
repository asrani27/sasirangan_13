@extends('layouts.public')

@section('title', 'Info Harga Harian Komoditi - DEDIKASI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header Page Title -->
    <div class="space-y-1">
        <div class="flex items-center gap-2 text-xs font-semibold text-emerald-700 uppercase tracking-wider">
            <i class="fas fa-chart-simple"></i>
            <span>Monitoring Pasar</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Informasi Harga Harian Bahan Pokok</h1>
        <p class="text-sm text-slate-500">Pencatatan dan perbandingan harga bahan pokok terkini per pasar di Kota Banjarmasin.</p>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-xs">
        <form action="{{ route('info-harga.search') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
            <div class="sm:col-span-5 space-y-1.5">
                <label for="pasar_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Pilih Pasar Tradisional</label>
                <div class="relative">
                    <select id="pasar_id" name="pasar_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        <option value="">-- Pilih Pasar --</option>
                        @foreach($pasar as $p)
                            <option value="{{ $p->id }}" {{ (isset($pasar_id) && $pasar_id == $p->id) ? 'selected' : '' }}>
                                {{ $p->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="sm:col-span-4 space-y-1.5">
                <label for="tanggal" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Tanggal Pemantauan</label>
                <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal ?? date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
            </div>

            <div class="sm:col-span-3">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-xs transition hover:shadow-md text-sm">
                    <i class="fas fa-magnifying-glass"></i>
                    <span>Tampilkan Data</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Results Section -->
    @if(isset($data) && count($data) > 0)
        @php
            $selectedPasar = $pasar->firstWhere('id', $pasar_id);
        @endphp
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="p-6 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg font-black text-slate-900">
                        {{ $selectedPasar ? $selectedPasar->nama : 'Hasil Pencarian' }}
                    </h2>
                    <p class="text-xs text-slate-500">
                        Tanggal: <span class="font-semibold text-slate-700">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</span>
                    </p>
                </div>
                <div class="text-xs font-semibold px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 self-start sm:self-auto">
                    {{ count($data) }} Komoditi Terdaftar
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5 w-12 text-center">No</th>
                            <th class="px-5 py-3.5">Komoditi</th>
                            <th class="px-5 py-3.5 text-center">Satuan</th>
                            <th class="px-5 py-3.5 text-right">Harga Sebelum</th>
                            <th class="px-5 py-3.5 text-right">Harga Hari Ini</th>
                            <th class="px-5 py-3.5 text-right">Perubahan (Rp)</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($data as $key => $item)
                            @php
                                $sebelum = (float) ($item->harga_sebelum ?? $item->harga_kemarin ?? 0);
                                $hariIni = (float) $item->harga_terkini;
                                $diff = (float) $item->perubahan;
                                $pct = ($sebelum > 0) ? ($diff / $sebelum) * 100 : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-3.5 text-center text-xs text-slate-400 font-mono">{{ $key + 1 }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-slate-900">{{ $item->nama }}</div>
                                    @if($item->kelompok)
                                        <div class="text-[11px] text-slate-400">{{ $item->kelompok->nama }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center text-xs text-slate-500">{{ $item->satuan->nama ?? 'Kg' }}</td>
                                <td class="px-5 py-3.5 text-right font-mono text-slate-600">
                                    @if($sebelum > 0)
                                        <div>Rp {{ number_format($sebelum, 0, ',', '.') }}</div>
                                        @if(!empty($item->tanggal_sebelum))
                                            <div class="text-[11px] text-slate-400 font-sans">
                                                ({{ \Carbon\Carbon::parse($item->tanggal_sebelum)->translatedFormat('j M Y') }})
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right font-mono font-bold text-slate-900">
                                    @if($hariIni > 0)
                                        Rp {{ number_format($hariIni, 0, ',', '.') }}
                                    @else
                                        <span class="text-slate-400 font-normal">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right font-mono text-xs">
                                    @if($diff > 0)
                                        <span class="text-rose-600 font-bold">+Rp {{ number_format($diff, 0, ',', '.') }}</span>
                                    @elseif($diff < 0)
                                        <span class="text-emerald-600 font-bold">-Rp {{ number_format(abs($diff), 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-400">Rp 0</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($diff > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="fas fa-arrow-trend-up text-xs"></i>
                                            <span>Naik (+{{ number_format(abs($pct), 1) }}%)</span>
                                        </span>
                                    @elseif($diff < 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fas fa-arrow-trend-down text-xs"></i>
                                            <span>Turun (-{{ number_format(abs($pct), 1) }}%)</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                                            <i class="fas fa-minus text-xs"></i>
                                            <span>Stabil</span>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Empty State / Invitation -->
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 space-y-3">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mx-auto shadow-xs">
                <i class="fas fa-shop"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Silakan Pilih Pasar &amp; Tanggal</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">
                Pilih pasar tradisional dan tanggal pantauan di formulir atas, lalu klik tombol "Tampilkan Data" untuk meninjau rincian komoditi harga pangan.
            </p>
        </div>
    @endif
</div>
@endsection
