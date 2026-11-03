@extends('layouts.public')

@section('title', 'Grafik Stok Komoditi - DEDIKASI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <div class="space-y-1">
        <div class="flex items-center gap-2 text-xs font-semibold text-teal-700 uppercase tracking-wider">
            <i class="fas fa-boxes-stacked"></i>
            <span>Monitoring Pasokan</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Grafik Ketersediaan Stok Pangan</h1>
        <p class="text-sm text-slate-500">Visualisasi dinamika tren ketersediaan stok komoditi per bulan dalam satu tahun.</p>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-xs">
        <form action="{{ route('grafik.stok.search') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
            <div class="sm:col-span-6 space-y-1.5">
                <label for="pasar_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Pasar / Sentra Distribusi</label>
                <select id="pasar_id" name="pasar_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-teal-500 transition">
                    <option value="">-- Pilih Pasar --</option>
                    @foreach($pasar as $p)
                        <option value="{{ $p->id }}" {{ (isset($pasar_id) && $pasar_id == $p->id) ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-4 space-y-1.5">
                <label for="tahun" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Tahun Pemantauan</label>
                <select id="tahun" name="tahun" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-teal-500 transition">
                    @php
                        $curYear = isset($tahun) ? (int)$tahun : (int)date('Y');
                    @endphp
                    @for($y = date('Y') + 1; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $curYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="sm:col-span-2">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl shadow-xs transition hover:shadow-md text-sm">
                    <i class="fas fa-chart-bar"></i>
                    <span>Tampilkan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Chart Visualization Section -->
    @if(isset($data['datasets']) && count($data['datasets']) > 0)
        @php
            $selectedPasar = $pasar->firstWhere('id', $pasar_id);
        @endphp
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
                <div>
                    <h2 class="text-lg font-black text-slate-900">
                        {{ $selectedPasar ? $selectedPasar->nama : 'Hasil Stok' }}
                    </h2>
                    <p class="text-xs text-slate-500">
                        Tahun: <span class="font-semibold text-slate-700">{{ $tahun }}</span>
                    </p>
                </div>
                <div class="text-xs font-semibold px-3 py-1 rounded-full bg-teal-100 text-teal-800">
                    {{ count($data['datasets']) }} Komoditi Ditampilkan
                </div>
            </div>

            <div class="relative w-full h-80 sm:h-96">
                <canvas id="stokChart"></canvas>
            </div>
        </div>
    @else
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 space-y-3">
            <div class="w-16 h-16 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl mx-auto shadow-xs">
                <i class="fas fa-chart-column"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Pilih Parameter Grafik Stok</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">
                Silakan pilih lokasi pasar dan tahun pada formulir di atas untuk melihat perbandingan ketersediaan stok bulanan.
            </p>
        </div>
    @endif
</div>

@if(isset($data['datasets']) && count($data['datasets']) > 0)
@push('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('stokChart');
        if (!ctx || !window.Chart) return;

        const chartData = {!! json_encode($data) !!};

        new window.Chart(ctx, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(val) {
                                return new Intl.NumberFormat('id-ID').format(val);
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
