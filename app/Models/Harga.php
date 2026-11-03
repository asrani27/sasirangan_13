<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Harga extends Model
{
    protected $table = 'harga';

    protected $guarded = ['id'];

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

    public function pasar(): BelongsTo
    {
        return $this->belongsTo(Pasar::class, 'pasar_id');
    }

    /**
     * Mengambil riwayat fluktuasi perubahan harga bahan pokok di pasar tertentu.
     * Hanya mencatat tanggal saat harga berbeda dari harga aktif hari sebelumnya.
     *
     * @return array{
     *     riwayat: array<int, array{tanggal: string, harga: float, prev_harga: ?float, selisih: float, persen: float, status: string}>,
     *     total_naik: int,
     *     total_turun: int,
     *     data_awal: ?array{tanggal: string, harga: float, prev_harga: ?float, selisih: float, persen: float, status: string},
     *     data_terkini: ?array{tanggal: string, harga: float, prev_harga: ?float, selisih: float, persen: float, status: string}
     * }
     */
    public static function getRiwayatPerubahan(int|string $bahanId, int|string $pasarId, string $startDate, string $endDate): array
    {
        $rawPrices = static::where('bahan_id', $bahanId)
            ->where('pasar_id', $pasarId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->where('harga', '>', 0)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get(['id', 'tanggal', 'harga'])
            ->unique('tanggal');

        $prevHarga = null;
        $changes = [];
        $totalNaik = 0;
        $totalTurun = 0;

        foreach ($rawPrices as $record) {
            $currentHarga = (float) $record->harga;

            if ($prevHarga === null) {
                // Data awal pada periode yang dipilih
                $changes[] = [
                    'tanggal' => $record->tanggal,
                    'harga' => $currentHarga,
                    'prev_harga' => null,
                    'selisih' => 0.0,
                    'persen' => 0.0,
                    'status' => 'awal',
                ];
                $prevHarga = $currentHarga;
            } elseif ($currentHarga != $prevHarga) {
                // Bandingkan dengan harga aktif hari sebelumnya.
                // Jika harganya sama dengan hari sebelumnya, tidak perlu dicatat.
                $selisih = $currentHarga - $prevHarga;
                $persen = $prevHarga > 0 ? ($selisih / $prevHarga) * 100 : 0.0;
                $status = $selisih > 0 ? 'up' : 'down';

                if ($status === 'up') {
                    $totalNaik++;
                } else {
                    $totalTurun++;
                }

                $changes[] = [
                    'tanggal' => $record->tanggal,
                    'harga' => $currentHarga,
                    'prev_harga' => $prevHarga,
                    'selisih' => $selisih,
                    'persen' => $persen,
                    'status' => $status,
                ];
                $prevHarga = $currentHarga;
            }
        }

        $dataAwal = ! empty($changes) ? $changes[0] : null;
        $dataTerkini = ! empty($changes) ? $changes[count($changes) - 1] : null;

        return [
            'riwayat' => array_reverse($changes),
            'total_naik' => $totalNaik,
            'total_turun' => $totalTurun,
            'data_awal' => $dataAwal,
            'data_terkini' => $dataTerkini,
        ];
    }
}
