<?php

namespace App\Console\Commands;

use App\Models\Bahan;
use App\Models\Harga;
use App\Models\Pasar;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateHargaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:harga 
                            {--tanggal= : Tanggal target (format: Y-m-d, default: hari ini)} 
                            {--pasar_id= : ID Pasar tertentu (opsional)} 
                            {--bahan_id= : ID Bahan tertentu (opsional)} 
                            {--force : Timpa harga jika data sudah ada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate harga bahan pokok berdasarkan tanggal, bahan_id, dan pasar_id dari tanggal sebelumnya';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $inputTanggal = $this->option('tanggal');
        try {
            $tanggal = $inputTanggal
                ? Carbon::parse($inputTanggal)->format('Y-m-d')
                : Carbon::today()->format('Y-m-d');
        } catch (\Exception $e) {
            $this->error('Format tanggal tidak valid. Gunakan format Y-m-d (contoh: 2026-09-02).');

            return 1;
        }

        $optPasarId = $this->option('pasar_id');
        $optBahanId = $this->option('bahan_id');
        $isForce = (bool) $this->option('force');

        $this->info('==================================================');
        $this->info('   GENERATE HARGA BAHAN POKOK');
        $this->info('==================================================');
        $this->info("Tanggal Target : {$tanggal}");
        if ($optPasarId) {
            $this->info("Filter Pasar ID: {$optPasarId}");
        }
        if ($optBahanId) {
            $this->info("Filter Bahan ID: {$optBahanId}");
        }
        $this->info('Mode Force     : '.($isForce ? 'Aktif (timpa data lama)' : 'Nonaktif (lewati data yang sudah ada)'));
        $this->info('--------------------------------------------------');

        if ($optPasarId) {
            $pasarList = Pasar::where('id', $optPasarId)->get();
            if ($pasarList->isEmpty()) {
                $this->error("Pasar dengan ID {$optPasarId} tidak ditemukan.");

                return 1;
            }
        } else {
            $pasarList = Pasar::all();
        }

        if ($optBahanId) {
            $checkBahan = Bahan::find($optBahanId);
            if (! $checkBahan) {
                $this->error("Bahan dengan ID {$optBahanId} tidak ditemukan.");

                return 1;
            }
        }

        $allBahan = Bahan::all();
        $totalGenerated = 0;
        $totalUpdated = 0;
        $totalSkipped = 0;

        foreach ($pasarList as $pasar) {
            $this->line("<fg=cyan;options=bold>Memproses Pasar: [{$pasar->id}] {$pasar->nama}</>");

            if ($optBahanId) {
                $bahanList = Bahan::where('id', $optBahanId)->get();
            } else {
                $bahanList = $pasar->bahan;
                if ($bahanList->isEmpty()) {
                    $bahanList = $allBahan;
                }
            }

            foreach ($bahanList as $bahan) {
                $existing = Harga::where('tanggal', $tanggal)
                    ->where('pasar_id', $pasar->id)
                    ->where('bahan_id', $bahan->id)
                    ->first();

                // Jika harga sudah ada dan bukan 0, maka jangan di-update (lewati)
                if ($existing && (int) ($existing->harga ?? 0) > 0) {
                    $this->line("  [SKIP] {$bahan->nama} (ID: {$bahan->id}) - Data tanggal {$tanggal} sudah ada dan bukan 0 (Harga: Rp ".number_format((float) ($existing->harga ?? 0), 0, ',', '.').')');
                    $totalSkipped++;

                    continue;
                }

                $prev = Harga::where('pasar_id', $pasar->id)
                    ->where('bahan_id', $bahan->id)
                    ->where('tanggal', '<', $tanggal)
                    ->orderBy('tanggal', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                $hargaBaru = $prev ? (int) ($prev->harga ?? 0) : 0;
                $tglSumber = $prev ? $prev->tanggal : 'tidak ada data sebelumnya (set 0)';

                if ($existing) {
                    // Harga sudah ada namun nilainya 0:
                    // Namun jika harga sebelumnya juga 0, maka skip
                    if ($hargaBaru === 0) {
                        $this->line("  [SKIP] {$bahan->nama} (ID: {$bahan->id}) - Data tanggal {$tanggal} bernilai Rp 0 dan harga sebelumnya juga 0");
                        $totalSkipped++;

                        continue;
                    }

                    // Update harganya sama dengan harga hari sebelumnya
                    $hargaLama = (int) ($existing->harga ?? 0);
                    $existing->update(['harga' => $hargaBaru]);
                    $this->info("  [UPDATE] {$bahan->nama} (ID: {$bahan->id}) - Rp ".number_format($hargaLama, 0, ',', '.').' -> Rp '.number_format($hargaBaru, 0, ',', '.')." (Sumber: {$tglSumber})");
                    $totalUpdated++;
                } else {
                    Harga::create([
                        'tanggal' => $tanggal,
                        'pasar_id' => $pasar->id,
                        'bahan_id' => $bahan->id,
                        'harga' => $hargaBaru,
                    ]);
                    $this->info("  [CREATE] {$bahan->nama} (ID: {$bahan->id}) - Rp ".number_format($hargaBaru, 0, ',', '.')." (Sumber: {$tglSumber})");
                    $totalGenerated++;
                }
            }
        }

        $this->info('--------------------------------------------------');
        $this->info('Selesai!');
        $this->info("Total Data Dibuat   : {$totalGenerated}");
        $this->info("Total Data Diupdate : {$totalUpdated}");
        $this->info("Total Data Dilewati : {$totalSkipped}");
        $this->info('==================================================');

        return 0;
    }
}
