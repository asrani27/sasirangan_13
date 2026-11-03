<?php

namespace App\Console\Commands;

use App\Models\BulanTahun;
use App\Models\Cronjob;
use App\Models\Harga;
use App\Models\HargaAcuan;
use App\Models\Kenaikan;
use App\Models\Notifikasi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EWSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Early Warning System (EWS) untuk deteksi lonjakan harga komoditas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;
        $todayStr = $now->format('Y-m-d');

        $this->info("Menjalankan EWS untuk tanggal: {$todayStr}");

        // Optimized: pre-load BulanTahun for current month/year
        $bulanTahunMap = BulanTahun::where('bulan', $month)
            ->where('tahun', $year)
            ->get()
            ->keyBy('pasar_id');

        $btIds = $bulanTahunMap->pluck('id')->all();

        // Optimized: pre-load HargaAcuan for current month
        $hargaAcuanMap = HargaAcuan::whereIn('bulan_tahun_id', $btIds)
            ->get()
            ->keyBy(fn ($item) => "{$item->pasar_id}_{$item->bahan_id}");

        // Optimized: pre-load existing Kenaikan for today
        $existingKenaikan = Kenaikan::where('tanggal', $todayStr)
            ->get()
            ->keyBy(fn ($item) => "{$item->pasar_id}_{$item->bahan_id}");

        $hargaToday = Harga::where('tanggal', $todayStr)->get();

        $kenaikanCount = 0;

        foreach ($hargaToday as $item) {
            $key = "{$item->pasar_id}_{$item->bahan_id}";
            $hargaAcuan = $hargaAcuanMap->get($key);

            if (! $hargaAcuan || (float) $hargaAcuan->harga <= 0) {
                continue;
            }

            $acuan = (float) $hargaAcuan->harga;
            $currentHarga = (float) $item->harga;
            $kenaikan = (($currentHarga - $acuan) / $acuan) * 100;

            if ($kenaikan > 5) {
                $check = $existingKenaikan->get($key);
                if ($check) {
                    $check->update([
                        'harga' => $currentHarga,
                        'kenaikan' => $kenaikan,
                    ]);
                } else {
                    $newK = Kenaikan::create([
                        'tanggal' => $todayStr,
                        'pasar_id' => $item->pasar_id,
                        'bahan_id' => $item->bahan_id,
                        'acuan' => $acuan,
                        'harga' => $currentHarga,
                        'kenaikan' => $kenaikan,
                    ]);
                    $existingKenaikan->put($key, $newK);
                }
                $kenaikanCount++;
            }
        }

        Cronjob::create([
            'cronjob' => $now->format('Y-m-d H:i:s'),
        ]);

        // Send notifications if there are price spikes
        if ($kenaikanCount > 0) {
            $nomor = Notifikasi::all();
            foreach ($nomor as $item) {
                try {
                    $pesan = [
                        'sendTo' => $item->nomor,
                        'content' => [
                            'text' => $now->translatedFormat('d F Y').
                                " \nEarly Warning System (EWS), \nHarga Bahan Pokok Yang mengalami Kenaikan: \nLink: https://dedikasi.banjarmasinkota.go.id/kenaikan \n",
                        ],
                    ];

                    Http::timeout(5)->withHeaders([
                        'Authorization' => 'Bearer IjF-1UCnC-Vf0iOkYU568oKL0YY_FET7tSCmh5VsnIZeuB6Pyy9_QvP0Uc5z0Z7amQG8s6uTecVSS1czxgBcF2IzQxfROxsgHHtGnRH1bjVXUpPuuZX8C9KkB8GsJFFZ0_R3qsLntIhfdR_V-RlFo-rCI2U9sjDk-Q5QqI6pe_efBaEGAx4ZuCp6sCAIIhO29grFy2gkTwbd0ZLJabhM4UPLKJihkrvebmhGR3e6BOrWBEsEqIni11yFNSOZnhbG5Hwy0S84kgah03QGo9pLEhw3HTR4tLQ8VWy2EMn_6u6QwF6jnTln8I_ddFCgn4cvkvPmh464cEzUUvHbIlRuow',
                    ])->post('https://whatsapp.banjarmasinkota.go.id/api/service/send', $pesan);
                } catch (\Throwable $e) {
                    Log::warning('Gagal kirim notifikasi WhatsApp EWS: '.$e->getMessage());
                }
            }
        }

        $this->info("EWS selesai. Ditemukan {$kenaikanCount} komoditas dengan kenaikan > 5%.");

        return 0;
    }
}
