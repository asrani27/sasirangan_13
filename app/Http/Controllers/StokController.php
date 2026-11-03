<?php

namespace App\Http\Controllers;

use App\Models\Pasar;
use App\Models\Stok_kota;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StokController extends Controller
{
    public function index(): View
    {
        $pasar = Pasar::where('tampil_stok', 'Y')->orderBy('nama')->get();

        return view('admin.stok.index', compact('pasar'));
    }

    public function pasar(int|string $id): View
    {
        $pasar = Pasar::findOrFail($id);
        $bahan = $pasar->bahan;
        $week = Carbon::now()->weekOfMonth;

        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();
        $date = CarbonPeriod::create($start, $end)->toArray();

        // Optimized: pre-load all stock for this market, month, year in 1 query
        $stockMap = Stok_kota::where('bulan', $month)
            ->where('tahun', $year)
            ->where('pasar_id', $id)
            ->get()
            ->keyBy('bahan_id');

        $data = $bahan->map(function ($item) use ($stockMap) {
            $check = $stockMap->get($item->id);
            $item->minggu_1 = $check ? (float) $check->minggu_1 : 0;
            $item->minggu_2 = $check ? (float) $check->minggu_2 : 0;
            $item->minggu_3 = $check ? (float) $check->minggu_3 : 0;
            $item->minggu_4 = $check ? (float) $check->minggu_4 : 0;
            $item->minggu_5 = $check ? (float) $check->minggu_5 : 0;

            return $item;
        });

        $fullmonth = false;

        return view('admin.stok.stok', compact('data', 'pasar', 'date', 'month', 'year', 'fullmonth', 'week', 'id'));
    }

    public function month(int|string $id): View
    {
        $pasar = Pasar::findOrFail($id);
        $bahan = $pasar->bahan;

        $month = (int) request()->input('bulan', Carbon::now()->month);
        $year = (int) request()->input('tahun', Carbon::now()->year);
        $daysCount = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $week = Carbon::createFromDate($year, $month, $daysCount)->weekOfMonth;

        // Optimized: pre-load stock
        $stockMap = Stok_kota::where('bulan', $month)
            ->where('tahun', $year)
            ->where('pasar_id', $id)
            ->get()
            ->keyBy('bahan_id');

        $data = $bahan->map(function ($item) use ($stockMap) {
            $check = $stockMap->get($item->id);
            $item->minggu_1 = $check ? (float) $check->minggu_1 : 0;
            $item->minggu_2 = $check ? (float) $check->minggu_2 : 0;
            $item->minggu_3 = $check ? (float) $check->minggu_3 : 0;
            $item->minggu_4 = $check ? (float) $check->minggu_4 : 0;
            $item->minggu_5 = $check ? (float) $check->minggu_5 : 0;

            return $item;
        });

        $fullmonth = true;

        return view('admin.stok.stok', compact('data', 'month', 'year', 'fullmonth', 'week', 'id', 'pasar'));
    }

    public function updateService(Request $request): string
    {
        $s = Stok_kota::firstOrNew([
            'bahan_id' => $request->input('pk'),
            'bulan' => $request->input('bulan'),
            'tahun' => $request->input('tahun'),
            'pasar_id' => $request->input('pasar_id'),
        ]);

        $col = 'minggu_'.$request->input('minggu');
        if (in_array($col, ['minggu_1', 'minggu_2', 'minggu_3', 'minggu_4', 'minggu_5'])) {
            $s->$col = $request->input('value');
        }

        $s->save();

        return 'di simpan';
    }
}
