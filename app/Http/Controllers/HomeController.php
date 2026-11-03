<?php

namespace App\Http\Controllers;

use App\Models\Kenaikan;
use App\Models\Pasar;
use App\Models\Traffic;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today()->format('Y-m-d');
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

        // Optimized: single query to fetch all daily traffic counts for this month
        $monthlyTraffic = Traffic::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->selectRaw('tanggal, COUNT(*) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);
        $data = [];
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $data[] = [
                'tanggal' => $dateStr,
                'value' => (int) ($monthlyTraffic[$dateStr] ?? 0),
            ];
        }

        $pasar = Pasar::count();
        $tahun2021 = Traffic::whereYear('tanggal', 2024)->count();
        $tahun2022 = Traffic::whereYear('tanggal', 2025)->count();
        $tahun2023 = Traffic::whereYear('tanggal', 2026)->count();
        $totalPengunjung = Traffic::count();
        $pengunjungHariIni = (int) ($monthlyTraffic[$today] ?? Traffic::where('tanggal', $today)->count());

        // Optimized: eager load relations to prevent N+1 queries in view
        $ews = Kenaikan::with(['pasar', 'bahan'])
            ->where('tanggal', $today)
            ->get();

        return view('admin.home', compact(
            'data',
            'pasar',
            'tahun2021',
            'tahun2022',
            'tahun2023',
            'pengunjungHariIni',
            'totalPengunjung',
            'ews'
        ));
    }
}
