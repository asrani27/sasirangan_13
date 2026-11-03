<?php

namespace App\Console\Commands;

use App\Models\Harga;
use Carbon\Carbon;
use Illuminate\Console\Command;

class HargaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perbaikanharga {--tanggal=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perbaikan harga 0 dari tanggal kemarin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tanggal = $this->option('tanggal');

        if (! $tanggal) {
            $this->error('Parameter --tanggal harus diisi. Contoh: php artisan perbaikanharga --tanggal=2024-04-26');

            return 1;
        }

        $tglKemarin = Carbon::parse($tanggal)->subDay()->format('Y-m-d');

        $this->info('Memulai proses perbaikan harga...');
        $this->info("Tanggal Target  : {$tanggal}");
        $this->info("Tanggal Kemarin : {$tglKemarin}");

        $data = Harga::where('tanggal', $tanggal)->get();
        $total = $data->count();

        $this->info("Total data ditemukan: {$total}");

        if ($total === 0) {
            $this->warn("Tidak ada data harga yang ditemukan pada tanggal {$tanggal}.");

            return 0;
        }

        // Pre-load yesterday's prices into a lookup dictionary keyed by "pasar_bahan"
        $prevPrices = Harga::where('tanggal', $tglKemarin)
            ->get()
            ->keyBy(fn ($item) => "{$item->pasar_id}_{$item->bahan_id}");

        $updated = 0;
        $skipped = 0;

        foreach ($data as $key => $i) {
            $keyLookup = "{$i->pasar_id}_{$i->bahan_id}";
            $prev = $prevPrices->get($keyLookup);

            if ($prev) {
                $hargaSebelumnya = $prev->harga;
                $hargaLama = $i->harga;
                $i->update(['harga' => $hargaSebelumnya]);
                $this->line('['.($key + 1)."/{$total}] Pasar ID: {$i->pasar_id}, Bahan ID: {$i->bahan_id} | Harga lama: {$hargaLama} -> Harga baru: {$hargaSebelumnya}");
                $updated++;
            } else {
                $this->warn('['.($key + 1)."/{$total}] Pasar ID: {$i->pasar_id}, Bahan ID: {$i->bahan_id} | Data tanggal {$tglKemarin} tidak ditemukan.");
                $skipped++;
            }
        }

        $this->info("Perbaikan harga selesai! (Diperbarui: {$updated}, Dilewati: {$skipped})");

        return 0;
    }
}
