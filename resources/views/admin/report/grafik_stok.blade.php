@extends('layouts.admin')

@section('title')
<div>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Grafik Stok Komoditi</h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tren ketersediaan stok bahan pokok tahunan Kota Banjarmasin</p>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" action="/report/stok/grafik/search" class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center px-3 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl">
                <i class="fas fa-location-dot text-emerald-600 mr-1.5"></i> Banjarmasin Kota
            </span>
            <select name="tahun" required class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 outline-none">
                <option value="">-- Pilih Tahun --</option>
                @for ($y = 2021; $y <= 2027; $y++)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition">
                <i class="fas fa-chart-line text-xs mr-1"></i> Tampilkan Grafik
            </button>
        </form>

        @if(request('tahun'))
        <div class="text-xs text-slate-500 font-medium">
            Tahun Pantauan: <span class="font-bold text-slate-800">{{ request('tahun') }}</span>
        </div>
        @endif
    </div>

    @if (count($data) == 0)
    <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-2xl mb-3">
            <i class="fas fa-chart-area"></i>
        </div>
        <h3 class="text-sm font-bold text-slate-800">Pilih Tahun Pemantauan</h3>
        <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Pilih tahun pada filter di atas dan klik tombol Tampilkan Grafik untuk melihat fluktuasi rata-rata stok bulanan.</p>
    </div>
    @else
    <!-- Chart Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
        <h3 class="text-sm font-bold text-slate-900 mb-4">Grafik Rata-Rata Stok Bahan Pokok Tahun {{ request('tahun') }}</h3>
        <div class="w-full relative min-h-[300px]">
            <canvas id="myChart" class="w-full" height="110"></canvas>
        </div>
    </div>

    <!-- Data Table Breakdown -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Rincian Rata-Rata Bulanan Tahun {{ request('tahun') }}</h3>
                <p class="text-[11px] text-slate-400">Rata-rata volume stok per komoditi setiap bulan</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3 w-10 text-center">No</th>
                        <th class="py-3 px-3 min-w-[150px]">Bahan</th>
                        <th class="py-3 px-2 text-right">Jan</th>
                        <th class="py-3 px-2 text-right">Feb</th>
                        <th class="py-3 px-2 text-right">Mar</th>
                        <th class="py-3 px-2 text-right">Apr</th>
                        <th class="py-3 px-2 text-right">Mei</th>
                        <th class="py-3 px-2 text-right">Jun</th>
                        <th class="py-3 px-2 text-right">Jul</th>
                        <th class="py-3 px-2 text-right">Agu</th>
                        <th class="py-3 px-2 text-right">Sep</th>
                        <th class="py-3 px-2 text-right">Okt</th>
                        <th class="py-3 px-2 text-right">Nov</th>
                        <th class="py-3 px-2 text-right">Des</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $no = 1; @endphp
                    @foreach ($data as $item)
                    <tr class="hover:bg-slate-50/70">
                        <td class="py-3 px-3 text-center text-slate-400 font-medium">{{ $no++ }}</td>
                        <td class="py-3 px-3 font-bold text-slate-900">{{ $item['label'] }}</td>
                        @for ($m = 0; $m < 12; $m++)
                            <td class="py-3 px-2 text-right font-mono text-xs {{ ($item['data'][$m] ?? 0) > 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                {{ ($item['data'][$m] ?? 0) > 0 ? number_format($item['data'][$m]) : '-' }}
                            </td>
                        @endfor
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

@push('js')
@if (count($data) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var canvas = document.getElementById('myChart');
        if (canvas) {
            var ctx = canvas.getContext('2d');
            var chartData = {!! json_encode($data) !!};
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'],
                    datasets: chartData
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        }
    });
</script>
@endif
@endpush

