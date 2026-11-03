<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Harga;
use App\Models\Pasar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function harian(): View
    {
        $tanggal = Carbon::today()->format('Y-m-d');
        $pasar = Pasar::orderBy('nama')->get();

        $prices = Harga::where('tanggal', $tanggal)
            ->get()
            ->keyBy(fn ($h) => "{$h->bahan_id}_{$h->pasar_id}");

        $data = Bahan::with('pasar')->orderBy('nama')->get()->map(function ($item) use ($prices) {
            $item->pasar = $item->pasar->map(function ($p) use ($item, $prices) {
                $key = "{$item->id}_{$p->id}";
                $p->hargaToday = isset($prices[$key]) ? (float) $prices[$key]->harga : 0;

                return $p;
            });

            return $item;
        });

        return view('admin.report.harian', compact('data', 'pasar', 'tanggal'));
    }

    public function searchHarian(Request $request): View
    {
        $tanggal = $request->input('tanggal') ?: Carbon::today()->format('Y-m-d');
        $pasar = Pasar::orderBy('nama')->get();

        $prices = Harga::where('tanggal', $tanggal)
            ->get()
            ->keyBy(fn ($h) => "{$h->bahan_id}_{$h->pasar_id}");

        $data = Bahan::with('pasar')->orderBy('nama')->get()->map(function ($item) use ($prices) {
            $item->pasar = $item->pasar->map(function ($p) use ($item, $prices) {
                $key = "{$item->id}_{$p->id}";
                $p->hargaToday = isset($prices[$key]) ? (float) $prices[$key]->harga : 0;

                return $p;
            });

            return $item;
        });

        toastr()->success('Data Tanggal '.Carbon::parse($tanggal)->format('d M Y').' ditampilkan');

        return view('admin.report.harian', compact('data', 'pasar', 'tanggal'));
    }

    public function bulanan(): View
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $subMonth = Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d');
        $today = Carbon::today()->format('Y-m-d');

        $pricesLalu = Harga::where('tanggal', $subMonth)->pluck('harga', 'bahan_id');
        $pricesKini = Harga::where('tanggal', $today)->pluck('harga', 'bahan_id');

        $data = Bahan::with('pasar')->orderBy('nama')->get()->map(function ($item) use ($pricesLalu, $pricesKini) {
            $item->pasar = $item->pasar->map(function ($p) use ($item, $pricesLalu, $pricesKini) {
                $p->bulanLalu = (float) ($pricesLalu[$item->id] ?? 0);
                $p->bulanIni = (float) ($pricesKini[$item->id] ?? 0);

                return $p;
            });

            $pasarCount = count($item->pasar);
            $item->bulanLalu = $pasarCount > 0 ? (int) ceil($item->pasar->sum('bulanLalu') / $pasarCount) : 0;
            $item->bulanIni = $pasarCount > 0 ? (int) ceil($item->pasar->sum('bulanIni') / $pasarCount) : 0;
            $item->perubahan = $item->bulanIni - $item->bulanLalu;

            return $item;
        });

        return view('admin.report.bulanan', compact('month', 'year', 'data'));
    }

    public function searchBulanan(Request $request): View
    {
        $month = (int) $request->input('bulan', Carbon::now()->month);
        $year = (int) $request->input('tahun', Carbon::now()->year);

        $subMonth = Carbon::createFromDate($year, $month, 1)->subMonth()->endOfMonth()->format('Y-m-d');
        $thisMonth = Carbon::today()->format('m-Y');

        if ($thisMonth == sprintf('%02d-%d', $month, $year)) {
            $today = Carbon::today()->format('Y-m-d');
        } else {
            $today = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');
        }

        $pricesLalu = Harga::where('tanggal', $subMonth)->pluck('harga', 'bahan_id');
        $pricesKini = Harga::where('tanggal', $today)->pluck('harga', 'bahan_id');

        $data = Bahan::with('pasar')->orderBy('nama')->get()->map(function ($item) use ($pricesLalu, $pricesKini) {
            $item->pasar = $item->pasar->map(function ($p) use ($item, $pricesLalu, $pricesKini) {
                $p->bulanLalu = (float) ($pricesLalu[$item->id] ?? 0);
                $p->bulanIni = (float) ($pricesKini[$item->id] ?? 0);

                return $p;
            });

            $pasarCount = count($item->pasar);
            $item->bulanLalu = $pasarCount > 0 ? (int) ceil($item->pasar->sum('bulanLalu') / $pasarCount) : 0;
            $item->bulanIni = $pasarCount > 0 ? (int) ceil($item->pasar->sum('bulanIni') / $pasarCount) : 0;
            $item->perubahan = $item->bulanIni - $item->bulanLalu;

            return $item;
        });

        return view('admin.report.bulanan', compact('month', 'year', 'data'));
    }

    public function riwayat(Request $request): View
    {
        $pasar = Pasar::orderBy('nama')->get();
        $bahan = Bahan::with('satuan')->orderBy('nama')->get();

        $bahan_id = $request->input('bahan_id');
        $pasar_id = $request->input('pasar_id');

        $startDate = $request->input('start_date') ?: '2026-01-01';
        $endDate = $request->input('end_date') ?: Carbon::today()->format('Y-m-d');

        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        // Default ke Bawang Merah & Pasar Pekauman jika belum dipilih pengguna
        if (! $bahan_id && $bahan->isNotEmpty()) {
            $defaultBahan = $bahan->first(fn ($b) => stripos($b->nama, 'Bawang Merah') !== false) ?? $bahan->first();
            $bahan_id = $defaultBahan?->id;
        }

        if (! $pasar_id && $pasar->isNotEmpty()) {
            $defaultPasar = $pasar->first(fn ($p) => stripos($p->nama, 'Pekauman') !== false) ?? $pasar->first();
            $pasar_id = $defaultPasar?->id;
        }

        $selectedBahan = $bahan_id ? $bahan->firstWhere('id', (int) $bahan_id) : null;
        $selectedPasar = $pasar_id ? $pasar->firstWhere('id', (int) $pasar_id) : null;

        $riwayat = [];
        $totalNaik = 0;
        $totalTurun = 0;
        $dataAwal = null;
        $dataTerkini = null;

        if ($bahan_id && $pasar_id) {
            $hasil = Harga::getRiwayatPerubahan($bahan_id, $pasar_id, $startDate, $endDate);
            $riwayat = $hasil['riwayat'];
            $totalNaik = $hasil['total_naik'];
            $totalTurun = $hasil['total_turun'];
            $dataAwal = $hasil['data_awal'];
            $dataTerkini = $hasil['data_terkini'];
        }

        return view('admin.report.riwayat', compact(
            'pasar',
            'bahan',
            'selectedBahan',
            'selectedPasar',
            'bahan_id',
            'pasar_id',
            'startDate',
            'endDate',
            'riwayat',
            'totalNaik',
            'totalTurun',
            'dataAwal',
            'dataTerkini'
        ));
    }

    public function stok(): View
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        $week = Carbon::now()->weekOfMonth;
        $bulanLalu = Carbon::createFromDate($year, $month, 1)->subMonth();
        $bl = $bulanLalu->month;
        $tl = $bulanLalu->year;

        $data = Bahan::with('stok_kota')->orderBy('nama')->get()->map(function ($item) use ($month, $year, $week, $bl, $tl) {
            $stokTerkini = $item->stok_kota->where('bulan', $month)->where('tahun', $year)->first();
            $stokBulanLalu = $item->stok_kota->where('bulan', $bl)->where('tahun', $tl)->first();

            $keyTerkini = 'minggu_'.$week;
            $item->stok_terkini = $stokTerkini ? (float) ($stokTerkini->$keyTerkini ?? 0) : 0;
            $item->stok_bulanlalu = $stokBulanLalu ? (float) ($stokBulanLalu->minggu_4 ?? 0) : 0;
            $item->perubahan = $item->stok_terkini - $item->stok_bulanlalu;

            return $item;
        });

        return view('admin.report.stokbulanan', compact('month', 'year', 'data'));
    }

    public function grafik_stok(): View
    {
        $data = [];
        $pasar = Pasar::orderBy('nama')->get();

        return view('admin.report.grafik_stok', compact('data', 'pasar'));
    }

    public function search_grafik_stok(Request $request): View
    {
        $pasar_id = $request->input('pasar_id');
        $tahun = $request->input('tahun', Carbon::now()->year);

        $bahan = Bahan::with(['stok' => fn ($q) => $q->where('tahun', $tahun)])->orderBy('nama')->get();
        $pasar = Pasar::orderBy('nama')->get();

        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];
        $ci = 0;
        $data = [];

        foreach ($bahan as $item) {
            $stokBulan = [];
            for ($b = 1; $b <= 12; $b++) {
                $entry = $item->stok->firstWhere('bulan', $b);
                $stokBulan[] = $entry ? (float) $entry->minggu_1 : 0;
            }

            $color = $colors[$ci % count($colors)];
            $ci++;

            $data[] = [
                'label' => $item->nama,
                'fill' => false,
                'data' => $stokBulan,
                'borderColor' => $color,
                'borderWidth' => 2,
            ];
        }

        $request->flash();

        return view('admin.report.grafik_stok', compact('data', 'pasar', 'pasar_id', 'tahun'));
    }
}
