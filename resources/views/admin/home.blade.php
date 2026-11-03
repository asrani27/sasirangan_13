@extends('layouts.admin')

@section('title', 'Dashboard Administrator')

@section('header_title', 'Dashboard Utama')

@section('content')
<div class="space-y-8">
    <!-- Top Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Jumlah Pasar</span>
                <div class="text-3xl font-black text-slate-900">{{ $pasar }}</div>
                <div class="text-[11px] text-slate-400">Titik pantau komoditi</div>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shadow-xs">
                <i class="fas fa-shop"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kunjungan Hari Ini</span>
                <div class="text-3xl font-black text-slate-900">{{ number_format($pengunjungHariIni) }}</div>
                <div class="text-[11px] text-emerald-600 font-semibold">Trafik publik aktif</div>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-2xl shadow-xs">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Trafik Portal</span>
                <div class="text-3xl font-black text-slate-900">{{ number_format($totalPengunjung) }}</div>
                <div class="text-[11px] text-slate-400">Akumulasi seluruh sesi</div>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl shadow-xs">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Peringatan EWS</span>
                <div class="text-3xl font-black {{ count($ews) > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ count($ews) }}
                </div>
                <div class="text-[11px] {{ count($ews) > 0 ? 'text-rose-500' : 'text-slate-400' }} font-semibold">
                    {{ count($ews) > 0 ? 'Komoditi di atas batas' : 'Harga pangan stabil' }}
                </div>
            </div>
            <div class="w-14 h-14 rounded-2xl {{ count($ews) > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center text-2xl shadow-xs">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    <!-- Main Grid: EWS Alert Table + Monthly Traffic Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- EWS DEDIKASI Table -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ count($ews) > 0 ? 'bg-rose-500 animate-ping' : 'bg-emerald-500' }}"></span>
                            <h2 class="text-lg font-black text-slate-900">DEDIKASI (Early Warning System)</h2>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Pemantauan lonjakan harga hari ini: <span class="font-bold text-slate-700">{{ \Carbon\Carbon::now()->format('d-m-Y') }}</span></p>
                    </div>
                    <a href="/home/ews" onclick="return confirm('Kalkulasi ulang sistem DEDIKASI?');"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                        <i class="fas fa-arrows-rotate"></i>
                        <span>Hitung Ulang EWS</span>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-5 py-3 w-12 text-center">No</th>
                                <th class="px-5 py-3">Pasar</th>
                                <th class="px-5 py-3">Komoditi</th>
                                <th class="px-5 py-3 text-right">Acuan</th>
                                <th class="px-5 py-3 text-right">Hari Ini</th>
                                <th class="px-5 py-3 text-center">Kenaikan</th>
                                <th class="px-5 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @forelse ($ews as $idx => $item)
                                <tr class="hover:bg-rose-50/50 transition">
                                    <td class="px-5 py-3.5 text-center text-xs text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="px-5 py-3.5 font-bold text-slate-900">{{ $item->pasar->nama ?? '-' }}</td>
                                    <td class="px-5 py-3.5">{{ $item->bahan->nama ?? '-' }}</td>
                                    <td class="px-5 py-3.5 text-right font-mono text-slate-600">Rp {{ number_format($item->acuan) }}</td>
                                    <td class="px-5 py-3.5 text-right font-mono font-bold text-rose-600">Rp {{ number_format($item->harga) }}</td>
                                    <td class="px-5 py-3.5 text-center font-bold text-rose-600 font-mono">+{{ number_format($item->kenaikan, 2) }}%</td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500 text-white">NAIK</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        <i class="fas fa-circle-check text-2xl text-emerald-500 mb-2"></i>
                                        <div class="text-sm font-semibold text-slate-700">Tidak ada komoditi dalam status bahaya kenaikan hari ini.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Monthly Traffic Chart -->
        <div class="lg:col-span-4 bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Trafik Kunjungan</h3>
                        <p class="text-xs text-slate-500">Statistik harian bulan ini</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700">
                        {{ \Carbon\Carbon::now()->format('F Y') }}
                    </span>
                </div>

                <div class="relative h-64 mt-4">
                    <canvas id="trafficChart"></canvas>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 grid grid-cols-3 gap-2 text-center text-xs">
                <div class="p-2 rounded-xl bg-slate-50">
                    <span class="text-slate-400 block text-[10px]">2024</span>
                    <span class="font-extrabold text-slate-800">{{ number_format($tahun2021) }}</span>
                </div>
                <div class="p-2 rounded-xl bg-slate-50">
                    <span class="text-slate-400 block text-[10px]">2025</span>
                    <span class="font-extrabold text-slate-800">{{ number_format($tahun2022) }}</span>
                </div>
                <div class="p-2 rounded-xl bg-slate-50">
                    <span class="text-slate-400 block text-[10px]">2026</span>
                    <span class="font-extrabold text-slate-800">{{ number_format($tahun2023) }}</span>
                </div>
            </div>
        </div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('trafficChart');
        if (!ctx || !window.Chart) return;

        const trafficData = {!! json_encode($data) !!};
        const labels = trafficData.map(d => d.tanggal.split('-')[2]);
        const values = trafficData.map(d => d.value);

        new window.Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pengunjung',
                    data: values,
                    borderColor: '#0284c7',
                    backgroundColor: 'rgba(2, 132, 199, 0.08)',
                    borderWidth: 2.5,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endpush
    </div>
</div>
@endsection
