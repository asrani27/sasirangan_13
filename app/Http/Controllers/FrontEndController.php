<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Berita;
use App\Models\Harga;
use App\Models\Kenaikan;
use App\Models\NomorAduan;
use App\Models\Pasar;
use App\Models\Slider;
use App\Models\Stok_kota;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FrontEndController extends Controller
{
    public function beranda(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect('/home');
        }

        $berita = Berita::with('kategori')->orderBy('id', 'DESC')->limit(4)->get();
        $slider = Slider::orderBy('id', 'DESC')->get();
        $aduan = NomorAduan::first();
        $pasarCount = Pasar::count();
        $bahanCount = Bahan::count();

        $today = Carbon::today()->format('Y-m-d');
        $topPrices = Harga::with(['bahan.satuan', 'pasar'])
            ->where('tanggal', $today)
            ->where('harga', '>', 0)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return view('frontend.beranda', compact('berita', 'slider', 'aduan', 'pasarCount', 'bahanCount', 'topPrices'));
    }

    public function kenaikan(): View
    {
        $today = Carbon::today()->format('Y-m-d');
        $ews = Kenaikan::with(['pasar', 'bahan.satuan'])
            ->where('tanggal', $today)
            ->orderBy('kenaikan', 'desc')
            ->get();

        $aduan = NomorAduan::first();

        return view('frontend.kenaikan', compact('ews', 'aduan', 'today'));
    }

    public function detailArtikel(int|string $id): View
    {
        $berita = Berita::with('kategori')->findOrFail($id);
        $recentBerita = Berita::where('id', '!=', $id)->orderBy('id', 'desc')->limit(5)->get();
        $aduan = NomorAduan::first();

        return view('frontend.detail', compact('berita', 'recentBerita', 'aduan'));
    }

    public function artikel(): View
    {
        $aduan = NomorAduan::first();
        $berita = Berita::with('kategori')->orderBy('id', 'DESC')->paginate(6);

        return view('frontend.artikel', compact('berita', 'aduan'));
    }

    public function info_harga(): View
    {
        $data = collect();
        $pasar = Pasar::orderBy('nama')->get();
        $aduan = NomorAduan::first();

        return view('frontend.info_harga', compact('data', 'aduan', 'pasar'));
    }

    public function info_harga_search(Request $request): View
    {
        $pasar_id = $request->input('pasar_id');
        $tanggal = $request->input('tanggal') ?: Carbon::today()->format('Y-m-d');
        $tanggal_sekarang = Carbon::parse($tanggal)->format('Y-m-d');

        $pasarDetail = Pasar::with(['bahan.satuan', 'bahan.kelompok'])->find($pasar_id);

        if (! $pasarDetail) {
            toastr()->error('Pasar tidak ditemukan');
            $data = collect();
        } else {
            $targetBahanIds = $pasarDetail->bahan->pluck('id')->all();

            $pricesToday = DB::table('harga')
                ->where('pasar_id', $pasar_id)
                ->where('tanggal', $tanggal_sekarang)
                ->whereIn('bahan_id', $targetBahanIds)
                ->orderBy('id', 'desc')
                ->get(['bahan_id', 'harga'])
                ->unique('bahan_id')
                ->pluck('harga', 'bahan_id');

            $neededCommodities = [];
            foreach ($targetBahanIds as $bId) {
                if (isset($pricesToday[$bId]) && (float) $pricesToday[$bId] > 0) {
                    $neededCommodities[$bId] = (float) $pricesToday[$bId];
                }
            }

            $prevDifferent = [];
            $latestPrior = [];

            if (! empty($neededCommodities)) {
                $rows = DB::table('harga')
                    ->where('pasar_id', $pasar_id)
                    ->where('tanggal', '<', $tanggal_sekarang)
                    ->where('harga', '>', 0)
                    ->whereIn('bahan_id', array_keys($neededCommodities))
                    ->orderBy('tanggal', 'desc')
                    ->orderBy('id', 'desc')
                    ->get(['bahan_id', 'tanggal', 'harga']);

                $completed = [];
                $lastSeenDate = [];

                foreach ($rows as $row) {
                    $bId = (int) $row->bahan_id;
                    if (isset($completed[$bId])) {
                        continue;
                    }

                    if (isset($lastSeenDate[$bId]) && $lastSeenDate[$bId] === $row->tanggal) {
                        continue;
                    }
                    $lastSeenDate[$bId] = $row->tanggal;

                    if (! isset($latestPrior[$bId])) {
                        $latestPrior[$bId] = [
                            'harga' => (float) $row->harga,
                            'tanggal' => $row->tanggal,
                        ];
                    }

                    $currPrice = $neededCommodities[$bId];
                    $rowPrice = (float) $row->harga;

                    if ($rowPrice !== $currPrice) {
                        $prevDifferent[$bId] = [
                            'harga' => $rowPrice,
                            'tanggal' => $row->tanggal,
                        ];
                        $completed[$bId] = true;

                        if (count($completed) === count($neededCommodities)) {
                            break;
                        }
                    }
                }
            }

            $data = $pasarDetail->bahan->map(function ($item) use ($pricesToday, $prevDifferent, $latestPrior) {
                $item->harga_terkini = (float) ($pricesToday[$item->id] ?? 0);

                if ($item->harga_terkini > 0) {
                    if (isset($prevDifferent[$item->id])) {
                        $item->harga_sebelum = (float) $prevDifferent[$item->id]['harga'];
                        $item->tanggal_sebelum = $prevDifferent[$item->id]['tanggal'];
                    } elseif (isset($latestPrior[$item->id])) {
                        $item->harga_sebelum = (float) $latestPrior[$item->id]['harga'];
                        $item->tanggal_sebelum = $latestPrior[$item->id]['tanggal'];
                    } else {
                        $item->harga_sebelum = null;
                        $item->tanggal_sebelum = null;
                    }
                } else {
                    $item->harga_sebelum = null;
                    $item->tanggal_sebelum = null;
                }

                $item->harga_kemarin = (float) ($item->harga_sebelum ?? 0);

                if ($item->harga_sebelum !== null && $item->harga_sebelum > 0 && $item->harga_terkini > 0) {
                    $item->perubahan = $item->harga_terkini - $item->harga_sebelum;
                } else {
                    $item->perubahan = 0;
                }

                return $item;
            });
        }

        $pasar = Pasar::orderBy('nama')->get();
        $aduan = NomorAduan::first();

        return view('frontend.info_harga', compact('data', 'pasar', 'aduan', 'pasar_id', 'tanggal'));
    }

    public function info_stok(): View
    {
        $data = collect();
        $pasar = Pasar::where('tampil_stok', 'Y')->orderBy('nama')->get();
        $aduan = NomorAduan::first();
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        return view('frontend.info_stok', compact('data', 'aduan', 'pasar', 'month', 'year'));
    }

    public function info_stok_search(Request $request): View
    {
        $pasar_id = $request->input('pasar_id');
        $month = (int) $request->input('bulan', Carbon::now()->month);
        $year = (int) $request->input('tahun', Carbon::now()->year);

        $dateInstance = Carbon::createFromDate($year, $month, 1);
        $daysCount = $dateInstance->daysInMonth;
        $week = Carbon::createFromDate($year, $month, $daysCount)->weekOfMonth;

        $pasarDetail = Pasar::with(['bahan.satuan', 'bahan.kelompok'])->find($pasar_id);

        if (! $pasarDetail) {
            toastr()->error('Pasar tidak ditemukan');
            $data = collect();
        } else {
            $stokMap = Stok_kota::where('bulan', $month)
                ->where('tahun', $year)
                ->where('pasar_id', $pasar_id)
                ->get()
                ->keyBy('bahan_id');

            $data = $pasarDetail->bahan->map(function ($item) use ($stokMap) {
                $check = $stokMap->get($item->id);
                $item->minggu_1 = $check ? (float) $check->minggu_1 : 0;
                $item->minggu_2 = $check ? (float) $check->minggu_2 : 0;
                $item->minggu_3 = $check ? (float) $check->minggu_3 : 0;
                $item->minggu_4 = $check ? (float) $check->minggu_4 : 0;
                $item->minggu_5 = $check ? (float) $check->minggu_5 : 0;

                return $item;
            });
        }

        $pasar = Pasar::where('tampil_stok', 'Y')->orderBy('nama')->get();
        $aduan = NomorAduan::first();

        return view('frontend.info_stok', compact('data', 'pasar', 'aduan', 'pasar_id', 'month', 'year', 'week'));
    }

    public function grafik_harga(): View
    {
        $data = [];
        $pasar = Pasar::orderBy('nama')->get();
        $bahan = Bahan::orderBy('nama')->get();
        $aduan = NomorAduan::first();

        return view('frontend.grafik_harga', compact('data', 'pasar', 'aduan', 'bahan'));
    }

    public function grafik_harga_search(Request $request): View
    {
        $pasar_id = $request->input('pasar_id');
        $bulan = str_pad($request->input('bulan'), 2, '0', STR_PAD_LEFT);
        $tahun = $request->input('tahun');
        $bahan_id = $request->input('bahan_id');

        $start = Carbon::createFromDate((int) $tahun, (int) $bulan, 1)->startOfMonth();
        $end = Carbon::createFromDate((int) $tahun, (int) $bulan, 1)->endOfMonth();

        $datePeriod = CarbonPeriod::create($start, $end);
        $dates = [];
        foreach ($datePeriod as $d) {
            $dates[] = $d->format('Y-m-d');
        }

        $data['tanggal'] = $dates;

        $prices = Harga::where('pasar_id', $pasar_id)
            ->where('bahan_id', $bahan_id)
            ->whereBetween('tanggal', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->pluck('harga', 'tanggal');

        $dataAngka = [];
        foreach ($dates as $item) {
            $dataAngka[] = (float) ($prices[$item] ?? 0);
        }

        $bahanItem = Bahan::find($bahan_id);
        $dataset = [
            [
                'label' => $bahanItem ? $bahanItem->nama : 'Bahan Pokok',
                'fill' => false,
                'data' => $dataAngka,
                'borderColor' => '#0284c7',
                'backgroundColor' => 'rgba(2, 132, 199, 0.1)',
                'borderWidth' => 2,
                'tension' => 0.3,
            ],
        ];

        $bahan = Bahan::orderBy('nama')->get();
        $pasar = Pasar::orderBy('nama')->get();
        $aduan = NomorAduan::first();

        return view('frontend.grafik_harga', compact('data', 'pasar', 'aduan', 'pasar_id', 'bulan', 'tahun', 'bahan', 'bahan_id', 'dataset'));
    }

    public function grafik_stok(): View
    {
        $data = [];
        $pasar = Pasar::orderBy('nama')->get();
        $aduan = NomorAduan::first();

        return view('frontend.grafik_stok', compact('data', 'pasar', 'aduan'));
    }

    public function grafik_stok_search(Request $request): View
    {
        $pasar_id = $request->input('pasar_id');
        $tahun = $request->input('tahun', Carbon::now()->year);

        $pasar = Pasar::orderBy('nama')->get();
        $aduan = NomorAduan::first();

        $stocks = Stok_kota::where('pasar_id', $pasar_id)
            ->where('tahun', $tahun)
            ->get();

        $bahan = Bahan::orderBy('nama')->get();
        $datasets = [];
        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];
        $ci = 0;

        foreach ($bahan as $item) {
            $itemStocks = $stocks->where('bahan_id', $item->id);
            if ($itemStocks->isEmpty()) {
                continue;
            }

            $monthlyTotals = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthRecord = $itemStocks->firstWhere('bulan', $m);
                $monthlyTotals[] = $monthRecord ? (float) $monthRecord->minggu_1 : 0;
            }

            $color = $colors[$ci % count($colors)];
            $ci++;

            $datasets[] = [
                'label' => $item->nama,
                'data' => $monthlyTotals,
                'borderColor' => $color,
                'backgroundColor' => $color.'22',
                'borderWidth' => 2,
            ];
        }

        $data = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'datasets' => $datasets,
        ];

        return view('frontend.grafik_stok', compact('data', 'pasar', 'pasar_id', 'tahun', 'aduan'));
    }

    public function login(): View
    {
        $aduan = NomorAduan::first();

        return view('frontend.login', compact('aduan'));
    }
}
