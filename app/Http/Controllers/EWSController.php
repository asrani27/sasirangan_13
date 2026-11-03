<?php

namespace App\Http\Controllers;

use App\Models\BulanTahun;
use App\Models\Harga;
use App\Models\HargaAcuan;
use App\Models\Kenaikan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;

class EWSController extends Controller
{
    public function ews(): RedirectResponse
    {
        $now = Carbon::today()->format('Y-m-d');
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        Kenaikan::where('tanggal', $now)->delete();

        // Optimized: pre-load BulanTahun for this month & year
        $bulanTahuns = BulanTahun::where('bulan', $month)
            ->where('tahun', $year)
            ->get()
            ->keyBy('pasar_id');

        if ($bulanTahuns->isEmpty()) {
            toastr()->info('Belum ada data bulan tahun untuk acuan bulan ini');

            return back();
        }

        // Optimized: pre-load HargaAcuan in 1 query
        $acuans = HargaAcuan::whereIn('bulan_tahun_id', $bulanTahuns->pluck('id'))
            ->get()
            ->keyBy(fn ($a) => "{$a->bulan_tahun_id}_{$a->pasar_id}_{$a->bahan_id}");

        $todayPrices = Harga::where('tanggal', $now)
            ->where('harga', '>', 0)
            ->get();

        $rowsToInsert = [];
        $timestamp = Carbon::now();

        foreach ($todayPrices as $item) {
            $bt = $bulanTahuns->get($item->pasar_id);
            if (! $bt) {
                continue;
            }

            $key = "{$bt->id}_{$item->pasar_id}_{$item->bahan_id}";
            $hargaAcuan = $acuans->get($key);

            if ($hargaAcuan && $hargaAcuan->harga > 0) {
                $acuan = (float) $hargaAcuan->harga;
                $kenaikan = (($item->harga - $acuan) / $acuan) * 100;

                if ($kenaikan > 5) {
                    $rowsToInsert[] = [
                        'tanggal' => $now,
                        'pasar_id' => $item->pasar_id,
                        'bahan_id' => $item->bahan_id,
                        'acuan' => $acuan,
                        'harga' => $item->harga,
                        'kenaikan' => $kenaikan,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }
            }
        }

        if (! empty($rowsToInsert)) {
            foreach (array_chunk($rowsToInsert, 500) as $chunk) {
                Kenaikan::insert($chunk);
            }
        }

        toastr()->success('Berhasil Digenerate');

        return back();
    }
}
