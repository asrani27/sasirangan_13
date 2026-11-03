@extends('layouts.public')

@section('title', 'Info Ketersediaan Stok Pangan - DEDIKASI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <div class="space-y-1">
        <div class="flex items-center gap-2 text-xs font-semibold text-teal-700 uppercase tracking-wider">
            <i class="fas fa-boxes-stacked"></i>
            <span>Cadangan Pangan</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Informasi Stok Bahan Pokok</h1>
        <p class="text-sm text-slate-500">Monitoring estimasi ketersediaan pasokan bahan pokok per minggu di Kota Banjarmasin.</p>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-xs">
        <form action="{{ route('info-stok.search') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
            <div class="sm:col-span-5 space-y-1.5">
                <label for="pasar_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Pilih Pasar / Lokasi Stok</label>
                <select id="pasar_id" name="pasar_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                    <option value="">-- Pilih Pasar --</option>
                    @foreach($pasar as $p)
                        <option value="{{ $p->id }}" {{ (isset($pasar_id) && $pasar_id == $p->id) ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3 space-y-1.5">
                <label for="bulan" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Bulan</label>
                <select id="bulan" name="bulan" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                    @php
                        $curMonth = isset($month) ? (int)$month : (int)date('m');
                        $bulanNames = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                    @endphp
                    @foreach($bulanNames as $num => $namaBulan)
                        <option value="{{ $num }}" {{ $curMonth == $num ? 'selected' : '' }}>
                            {{ $namaBulan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 space-y-1.5">
                <label for="tahun" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Tahun</label>
                <select id="tahun" name="tahun" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                    @php
                        $curYear = isset($year) ? (int)$year : (int)date('Y');
                    @endphp
                    @for($y = date('Y') + 1; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $curYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="sm:col-span-2">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl shadow-xs transition hover:shadow-md text-sm">
                    <i class="fas fa-magnifying-glass"></i>
                    <span>Tampilkan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Results Section -->
    @if(isset($data) && count($data) > 0)
        @php
            $selectedPasar = $pasar->firstWhere('id', $pasar_id);
            $namaBulanSelected = $bulanNames[(int)$month] ?? $month;
        @endphp
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="p-6 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg font-black text-slate-900">
                        {{ $selectedPasar ? $selectedPasar->nama : 'Hasil Stok' }}
                    </h2>
                    <p class="text-xs text-slate-500">
                        Periode: <span class="font-semibold text-slate-700">{{ $namaBulanSelected }} {{ $year }}</span>
                    </p>
                </div>
                <div class="text-xs font-semibold px-3 py-1 rounded-full bg-teal-100 text-teal-800 self-start sm:self-auto">
                    {{ count($data) }} Komoditi Terdata
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5 w-12 text-center">No</th>
                            <th class="px-5 py-3.5">Komoditi</th>
                            <th class="px-5 py-3.5 text-center">Satuan</th>
                            <th class="px-5 py-3.5 text-right">Minggu 1</th>
                            <th class="px-5 py-3.5 text-right">Minggu 2</th>
                            <th class="px-5 py-3.5 text-right">Minggu 3</th>
                            <th class="px-5 py-3.5 text-right">Minggu 4</th>
                            <th class="px-5 py-3.5 text-right">Minggu 5</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($data as $key => $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-3.5 text-center text-xs text-slate-400 font-mono">{{ $key + 1 }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-slate-900">{{ $item->nama }}</div>
                                    @if($item->kelompok)
                                        <div class="text-[11px] text-slate-400">{{ $item->kelompok->nama }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center text-xs text-slate-500">{{ $item->satuan->nama ?? 'Kg' }}</td>
                                <td class="px-5 py-3.5 text-right font-mono text-slate-800">{{ number_format($item->minggu_1, 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-right font-mono text-slate-800">{{ number_format($item->minggu_2, 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-right font-mono text-slate-800">{{ number_format($item->minggu_3, 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-right font-mono text-slate-800">{{ number_format($item->minggu_4, 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-right font-mono text-slate-800">{{ number_format($item->minggu_5, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 space-y-3">
            <div class="w-16 h-16 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl mx-auto shadow-xs">
                <i class="fas fa-boxes-stacked"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Silakan Tentukan Filter Stok</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">
                Pilih lokasi pasar serta periode bulan dan tahun di atas, lalu klik "Tampilkan" untuk melihat pemantauan stok mingguan.
            </p>
        </div>
    @endif
</div>
@endsection
