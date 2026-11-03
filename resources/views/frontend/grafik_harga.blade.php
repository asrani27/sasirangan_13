@extends('layouts.public')

@section('title', 'Grafik Perkembangan Harga - DEDIKASI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <div class="space-y-1">
        <div class="flex items-center gap-2 text-xs font-semibold text-emerald-700 uppercase tracking-wider">
            <i class="fas fa-chart-line"></i>
            <span>Analisis Fluktuasi</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Grafik Fluktuasi Harga Komoditi</h1>
        <p class="text-sm text-slate-500">Visualisasi historis tren perkembangan harga komoditi harian dalam satu bulan.</p>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-xs">
        <form action="{{ route('grafik.harga.search') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
            <div class="sm:col-span-3 space-y-1.5">
                <label for="pasar_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Pasar</label>
                <select id="pasar_id" name="pasar_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 transition">
                    <option value="">-- Pilih Pasar --</option>
                    @foreach($pasar as $p)
                        <option value="{{ $p->id }}" {{ (isset($pasar_id) && $pasar_id == $p->id) ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3 space-y-1.5">
                <label for="bahan_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Komoditi Bahan</label>
                <select id="bahan_id" name="bahan_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 transition">
                    <option value="">-- Pilih Komoditi --</option>
                    @foreach($bahan as $b)
                        <option value="{{ $b->id }}" {{ (isset($bahan_id) && $bahan_id == $b->id) ? 'selected' : '' }}>
                            {{ $b->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 space-y-1.5">
                <label for="bulan" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Bulan</label>
                <select id="bulan" name="bulan" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 transition">
                    @php
                        $curMonth = isset($bulan) ? (int)$bulan : (int)date('m');
                        $bulanNames = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                    @endphp
                    @foreach($bulanNames as $num => $namaBulan)
                        <option value="{{ $num }}" {{ $curMonth == $num ? 'selected' : '' }}>{{ $namaBulan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 space-y-1.5">
                <label for="tahun" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Tahun</label>
                <select id="tahun" name="tahun" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 transition">
                    @php
                        $curYear = isset($tahun) ? (int)$tahun : (int)date('Y');
                    @endphp
                    @for($y = date('Y') + 1; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $curYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="sm:col-span-2">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-xs transition hover:shadow-md text-sm">
                    <i class="fas fa-chart-line"></i>
                    <span>Tampilkan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Chart Visualization Section -->
    @if(isset($dataset) && isset($data['tanggal']))
        @php
            $selectedPasar = $pasar->firstWhere('id', $pasar_id);
            $selectedBahan = $bahan->firstWhere('id', $bahan_id);
        @endphp
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
                <div>
                    <h2 class="text-lg font-black text-slate-900">
                        {{ $selectedBahan->nama ?? 'Komoditi' }} &bull; {{ $selectedPasar->nama ?? 'Pasar' }}
                    </h2>
                    <p class="text-xs text-slate-500">
                        Periode: <span class="font-semibold text-slate-700">{{ $bulanNames[(int)$bulan] ?? $bulan }} {{ $tahun }}</span>
                    </p>
                </div>
            </div>

            <div class="relative w-full h-80 sm:h-96">
                <canvas id="hargaChart"></canvas>
            </div>
        </div>
    @else
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 space-y-3">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mx-auto shadow-xs">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Pilih Parameter Grafik</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">
                Silakan pilih pasar, komoditi pangan, bulan, dan tahun pada formulir di atas untuk menampilkan grafik garis fluktuasi harga harian.
            </p>
        </div>
    @endif
</div>

@if(isset($dataset) && isset($data['tanggal']))
@push('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('hargaChart');
        if (!ctx || !window.Chart) return;

        const rawLabels = {!! json_encode($data['tanggal']) !!};
        const formattedLabels = rawLabels.map(d => {
            const parts = d.split('-');
            return parts[2] + '/' + parts[1];
        });

        const rawDatasets = {!! json_encode($dataset) !!};

        new window.Chart(ctx, {
            type: 'line',
            data: {
                labels: formattedLabels,
                datasets: rawDatasets.map(ds => ({
                    ...ds,
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.08)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#059669',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endif
@endsection
