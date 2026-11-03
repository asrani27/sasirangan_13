<?php

namespace App\Http\Controllers;

use App\Models\Harga;
use App\Models\Pasar;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HargaController extends Controller
{
    public function index(): View
    {
        $pasar = Pasar::orderBy('nama')->get();

        return view('admin.harga.index', compact('pasar'));
    }

    public function fullDate(int|string $id): View
    {
        $pasar = Pasar::findOrFail($id);
        $bulan = str_pad(request()->input('bulan', Carbon::now()->month), 2, '0', STR_PAD_LEFT);
        $tahun = request()->input('tahun', Carbon::now()->year);

        $start = Carbon::createFromDate((int) $tahun, (int) $bulan, 1)->startOfMonth();
        $end = Carbon::createFromDate((int) $tahun, (int) $bulan, 1)->endOfMonth();
        $date = CarbonPeriod::create($start, $end)->toArray();

        // Optimized: single query for the entire month
        $prices = Harga::where('pasar_id', $id)
            ->whereBetween('tanggal', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->get()
            ->keyBy(fn ($h) => "{$h->bahan_id}_{$h->tanggal}");

        $data = $pasar->bahan->map(function ($item) use ($date, $prices) {
            $item->tanggal = collect($date)->map(function ($d) use ($item, $prices) {
                $key = "{$item->id}_".$d->format('Y-m-d');

                return isset($prices[$key]) ? (float) $prices[$key]->harga : 0;
            });

            return $item;
        });

        $fulldate = true;
        toastr()->success('Berhasil Di Tampilkan');

        return view('admin.harga.harga', compact('data', 'pasar', 'date', 'fulldate', 'bulan', 'tahun', 'start'));
    }

    public function pasar(int|string $id): View
    {
        $pasar = Pasar::findOrFail($id);
        $bahan = $pasar->bahan;

        $month = Carbon::today();
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();
        $date = CarbonPeriod::create($start, $end)->toArray();

        // Optimized: single query for today's prices
        $pricesToday = Harga::where('pasar_id', $id)
            ->where('tanggal', $month->format('Y-m-d'))
            ->pluck('harga', 'bahan_id');

        $data = $bahan->map(function ($item) use ($pricesToday) {
            $item->harga = (float) ($pricesToday[$item->id] ?? 0);

            return $item;
        });

        $fulldate = false;

        return view('admin.harga.harga', compact('data', 'pasar', 'date', 'month', 'fulldate'));
    }

    public function storeHargaBahan(): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $tglKemarin = Carbon::yesterday()->format('Y-m-d');
            $tglSekarang = Carbon::today()->format('Y-m-d');

            $yesterdayPrices = Harga::where('tanggal', $tglKemarin)->get();
            $todayPrices = Harga::where('tanggal', $tglSekarang)->get()->keyBy(fn ($h) => "{$h->pasar_id}_{$h->bahan_id}");

            $newRecords = [];
            $now = Carbon::now();

            foreach ($yesterdayPrices as $item) {
                $key = "{$item->pasar_id}_{$item->bahan_id}";
                if (! isset($todayPrices[$key])) {
                    $newRecords[] = [
                        'tanggal' => $tglSekarang,
                        'harga' => $item->harga,
                        'pasar_id' => $item->pasar_id,
                        'bahan_id' => $item->bahan_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (! empty($newRecords)) {
                foreach (array_chunk($newRecords, 500) as $chunk) {
                    Harga::insert($chunk);
                }
            }

            DB::commit();
            toastr()->success('Berhasil Di Simpan Harga hari Ini');

            return redirect('/input/harga');
        } catch (\Throwable $e) {
            DB::rollback();
            toastr()->error('Terdapat Error Hubungi Admin');

            return back();
        }
    }

    public function updateService(Request $request): string
    {
        $pasar_id = $request->input('pasar');
        $bahan_id = $request->input('pk');
        $tanggal = $request->input('tanggal');
        $value = $request->input('value', 0);

        Harga::updateOrCreate(
            [
                'tanggal' => $tanggal,
                'bahan_id' => $bahan_id,
                'pasar_id' => $pasar_id,
            ],
            [
                'harga' => $value,
            ]
        );

        return 'ok';
    }
}
