<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Models\Harga;
use App\Models\Pasar;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RestController extends Controller
{
    public function user(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'message' => 'Data Ditemukan',
            'nama' => $user->name,
            'username' => $user->username,
            'pasar' => $user->pasar,
        ]);
    }

    public function gantipassword(Request $request): JsonResponse
    {
        $request->validate(['password' => 'required|min:6']);
        $resp = Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password Berhasil Diganti',
            'data' => $resp,
        ]);
    }

    public function komoditi(Request $request): JsonResponse
    {
        $harga = Harga::with('bahan')->where('pasar_id', $request->pasar_id)
            ->where('tanggal', $request->tanggal)
            ->get()
            ->map(function ($item) {
                $item->komoditi = $item->bahan?->nama;
                $item->harga_formatted = number_format($item->harga);

                return $item;
            });

        return response()->json([
            'message' => 'Data Ditemukan',
            'data' => $harga,
        ]);
    }

    public function updateKomoditi(Request $request): JsonResponse
    {
        Harga::where('id', $request->harga_id)->update([
            'harga' => $request->harga,
        ]);

        $databahan = Harga::with('bahan')->where('pasar_id', $request->pasar_id)
            ->where('tanggal', $request->tanggal)
            ->get()
            ->map(function ($item) {
                $item->komoditi = $item->bahan?->nama;
                $item->harga_formatted = number_format($item->harga);

                return $item;
            });

        return response()->json([
            'message' => 'Data Ditemukan',
            'data' => $databahan,
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $user = Auth::user();
            $user->tokens()->delete();
            $token = $user->createToken($request->username)->plainTextToken;

            return response()->json([
                'message' => 'Data Ditemukan',
                'data' => $user,
                'api_token' => $token,
            ]);
        }

        return response()->json([
            'message' => 'username atau password anda tidak ditemukan',
            'data' => null,
        ], 401);
    }

    public function pasar(): JsonResponse
    {
        $pasar = Pasar::orderBy('nama')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data Pasar Ditemukan',
            'total_data' => $pasar->count(),
            'data' => $pasar,
        ]);
    }

    public function infoBahan(Request $request): JsonResponse
    {
        if ($request->filled('id') || $request->filled('bahan_id')) {
            $id = $request->id ?? $request->bahan_id;
            $bahan = Bahan::select('id', 'nama')->find($id);

            if (! $bahan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Bahan Tidak Ditemukan',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data Bahan Ditemukan',
                'data' => $bahan,
            ]);
        }

        $bahan = Bahan::select('id', 'nama')->orderBy('nama')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data Bahan Ditemukan',
            'total_data' => $bahan->count(),
            'data' => $bahan,
        ]);
    }

    public function listPasar(Request $request): JsonResponse
    {
        if ($request->filled('bahan_id')) {
            return $this->infoHargaBahan($request);
        }

        if ($request->filled('pasar_id')) {
            $pasar = Pasar::select('id', 'nama')->with(['bahan' => function ($query) {
                $query->select('bahan.id', 'bahan.nama');
            }])->find($request->pasar_id);

            if (! $pasar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Pasar Tidak Ditemukan',
                    'data' => null,
                ], 404);
            }

            $pasar->bahan->makeHidden('pivot');

            return response()->json([
                'success' => true,
                'message' => 'Data Pasar Ditemukan',
                'data' => $pasar,
            ]);
        }

        $pasar = Pasar::select('id', 'nama')->with(['bahan' => function ($query) {
            $query->select('bahan.id', 'bahan.nama');
        }])->get();

        $pasar->each(function ($item) {
            $item->bahan->makeHidden('pivot');
        });

        return response()->json([
            'success' => true,
            'message' => 'Data Pasar Ditemukan',
            'data' => $pasar,
        ]);
    }

    public function infoHarga(Request $request): JsonResponse
    {
        if ($request->hasAny(['tanggal_mulai', 'tanggal_berakhir'])) {
            return $this->infoHargaTanggal($request);
        }

        if ($request->filled('bahan_id')) {
            return $this->infoHargaBahan($request);
        }

        $request->validate([
            'pasar_id' => 'nullable|exists:pasar,id',
            'tanggal' => 'nullable|date',
        ]);

        $pasar_id = $request->pasar_id;
        $tanggal = $request->tanggal ?? Carbon::today()->format('Y-m-d');
        $tanggal_sekarang = Carbon::parse($tanggal);
        $tanggal_kemarin = Carbon::parse($tanggal)->subDay();

        if ($pasar_id) {
            $pasar = Pasar::with('bahan.satuan')->find($pasar_id);

            if (! $pasar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pasar tidak ditemukan',
                ], 404);
            }

            $bahan = $pasar->bahan;

            if ($bahan->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada bahan pokok di pasar ini',
                    'data' => [],
                ]);
            }

            $hargas = Harga::where('pasar_id', $pasar_id)
                ->whereIn('tanggal', [$tanggal_sekarang->format('Y-m-d'), $tanggal_kemarin->format('Y-m-d')])
                ->get();

            $hargaLookup = [];
            foreach ($hargas as $h) {
                $key = $h->pasar_id.'_'.$h->bahan_id.'_'.$h->tanggal;
                $hargaLookup[$key] ??= $h;
            }

            $data = $this->formatBahanHarga($bahan, $tanggal_sekarang, $tanggal_kemarin, $hargaLookup, $pasar->id);

            return response()->json([
                'success' => true,
                'message' => 'Data Harga Ditemukan',
                'pasar' => [
                    'id' => $pasar->id,
                    'nama' => $pasar->nama,
                ],
                'tanggal' => $tanggal_sekarang->format('Y-m-d'),
                'tanggal_kemarin' => $tanggal_kemarin->format('Y-m-d'),
                'total_data' => $data->count(),
                'data' => $data->values(),
            ]);
        }

        $semuaPasar = Pasar::with('bahan.satuan')->orderBy('id', 'ASC')->get();

        $hargas = Harga::whereIn('tanggal', [$tanggal_sekarang->format('Y-m-d'), $tanggal_kemarin->format('Y-m-d')])
            ->get();

        $hargaLookup = [];
        foreach ($hargas as $h) {
            $key = $h->pasar_id.'_'.$h->bahan_id.'_'.$h->tanggal;
            $hargaLookup[$key] ??= $h;
        }

        $resultPasar = $semuaPasar->map(function ($pasar) use ($tanggal_sekarang, $tanggal_kemarin, $hargaLookup) {
            $dataBahan = $this->formatBahanHarga($pasar->bahan, $tanggal_sekarang, $tanggal_kemarin, $hargaLookup, $pasar->id);

            return [
                'pasar' => [
                    'id' => $pasar->id,
                    'nama' => $pasar->nama,
                ],
                'total_data' => $dataBahan->count(),
                'data' => $dataBahan->values(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data Harga Semua Pasar Ditemukan',
            'tanggal' => $tanggal_sekarang->format('Y-m-d'),
            'tanggal_kemarin' => $tanggal_kemarin->format('Y-m-d'),
            'total_pasar' => $resultPasar->count(),
            'data' => $resultPasar->values(),
        ]);
    }

    private function formatBahanHarga($bahan, Carbon $tanggal_sekarang, Carbon $tanggal_kemarin, array $hargaLookup, int|string $pasar_id)
    {
        $tglSekarangStr = $tanggal_sekarang->format('Y-m-d');
        $tglKemarinStr = $tanggal_kemarin->format('Y-m-d');

        return $bahan->map(function ($item) use ($tanggal_sekarang, $tanggal_kemarin, $tglSekarangStr, $tglKemarinStr, $hargaLookup, $pasar_id) {
            $keySekarang = $pasar_id.'_'.$item->id.'_'.$tglSekarangStr;
            $keyKemarin = $pasar_id.'_'.$item->id.'_'.$tglKemarinStr;

            $harga_terkini = $hargaLookup[$keySekarang] ?? null;
            $harga_kemarin = $hargaLookup[$keyKemarin] ?? null;

            $harga_terkini_val = $harga_terkini ? (float) $harga_terkini->harga : 0;
            $harga_kemarin_val = $harga_kemarin ? (float) $harga_kemarin->harga : 0;

            $perubahan = $harga_terkini_val - $harga_kemarin_val;

            if ($harga_kemarin_val == 0 && $harga_terkini_val != 0) {
                $perubahan_persen = 100;
            } elseif ($harga_terkini_val == 0 && $harga_kemarin_val == 0) {
                $perubahan_persen = 0;
            } elseif ($harga_kemarin_val == 0) {
                $perubahan_persen = 0;
            } else {
                $perubahan_persen = ($harga_terkini_val / $harga_kemarin_val) * 100 - 100;
            }

            return [
                'bahan_pokok' => $item->nama,
                'satuan' => $item->satuan ? $item->satuan->nama : null,
                'harga_kemarin' => [
                    'tanggal' => $tanggal_kemarin->format('d-M-Y'),
                    'harga' => $harga_kemarin_val,
                    'formatted' => 'Rp '.number_format($harga_kemarin_val),
                ],
                'harga_terkini' => [
                    'tanggal' => $tanggal_sekarang->format('d-M-Y'),
                    'harga' => $harga_terkini_val,
                    'formatted' => 'Rp '.number_format($harga_terkini_val),
                ],
                'perubahan' => [
                    'harga' => $perubahan,
                    'formatted' => 'Rp '.number_format($perubahan),
                ],
                'perubahan_persen' => round($perubahan_persen, 2).' %',
            ];
        });
    }

    public function infoHargaTanggal(Request $request): JsonResponse
    {
        $request->validate([
            'pasar_id' => 'required|exists:pasar,id',
            'bahan_id' => 'required|exists:bahan,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $pasar = Pasar::findOrFail($request->pasar_id);
        $bahan = Bahan::with('satuan')->findOrFail($request->bahan_id);

        $start = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $end = Carbon::parse($request->tanggal_berakhir)->startOfDay();

        $hargas = Harga::where('pasar_id', $pasar->id)
            ->where('bahan_id', $bahan->id)
            ->whereBetween('tanggal', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->orderBy('id', 'asc')
            ->pluck('harga', 'tanggal');

        $data = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $dateStr = $current->format('Y-m-d');
            $hargaVal = (int) ($hargas[$dateStr] ?? 0);

            $data[] = [
                'tanggal' => $dateStr,
                'harga' => $hargaVal,
                'formatted' => 'Rp '.number_format($hargaVal),
            ];

            $current->addDay();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Harga Berdasarkan Tanggal Ditemukan',
            'pasar' => [
                'id' => $pasar->id,
                'nama' => $pasar->nama,
            ],
            'bahan' => [
                'id' => $bahan->id,
                'nama' => $bahan->nama,
                'satuan' => $bahan->satuan?->nama,
            ],
            'tanggal_mulai' => $start->format('Y-m-d'),
            'tanggal_berakhir' => $end->format('Y-m-d'),
            'total_data' => count($data),
            'data' => $data,
        ]);
    }

    public function infoHargaBahan(Request $request): JsonResponse
    {
        $request->validate([
            'bahan_id' => 'required|exists:bahan,id',
            'tanggal' => 'nullable|date',
        ]);

        $bahan = Bahan::with(['satuan', 'pasar'])->findOrFail($request->bahan_id);
        $tanggal = $request->tanggal ? Carbon::parse($request->tanggal)->format('Y-m-d') : Carbon::today()->format('Y-m-d');

        $hargas = Harga::where('bahan_id', $bahan->id)
            ->where('tanggal', $tanggal)
            ->orderBy('id', 'desc')
            ->pluck('harga', 'pasar_id');

        $data = $bahan->pasar->map(function ($pasar) use ($hargas) {
            $hargaVal = (int) ($hargas[$pasar->id] ?? 0);

            return [
                'id' => $pasar->id,
                'nama' => $pasar->nama,
                'harga' => $hargaVal,
                'formatted' => 'Rp '.number_format($hargaVal),
            ];
        })->sort(function ($a, $b) {
            if ($a['harga'] === $b['harga']) {
                return strcmp($a['nama'], $b['nama']);
            }

            return $b['harga'] <=> $a['harga'];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Data Harga Bahan Berdasarkan Pasar Ditemukan',
            'nama_bahan' => $bahan->nama,
            'bahan' => [
                'id' => $bahan->id,
                'nama' => $bahan->nama,
                'satuan' => $bahan->satuan?->nama,
            ],
            'tanggal' => $tanggal,
            'total_pasar' => $data->count(),
            'data' => $data,
        ]);
    }

    public function riwayatHarga(Request $request): JsonResponse
    {
        $request->validate([
            'pasar_id' => 'nullable|exists:pasar,id',
            'bahan_id' => 'nullable|exists:bahan,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',
        ]);

        $startDate = $request->input('start_date') ?? $request->input('tanggal_mulai') ?? '2026-01-01';
        $endDate = $request->input('end_date') ?? $request->input('tanggal_berakhir') ?? Carbon::today()->format('Y-m-d');

        $bahanId = $request->input('bahan_id');
        if (! $bahanId) {
            $defaultBahan = Bahan::where('nama', 'like', '%Bawang Merah%')->first() ?? Bahan::first();
            $bahanId = $defaultBahan?->id;
        }

        $pasarId = $request->input('pasar_id');
        if (! $pasarId) {
            $defaultPasar = Pasar::where('nama', 'like', '%Pekauman%')->first() ?? Pasar::first();
            $pasarId = $defaultPasar?->id;
        }

        $bahan = $bahanId ? Bahan::with('satuan')->find($bahanId) : null;
        $pasar = $pasarId ? Pasar::find($pasarId) : null;

        if (! $bahan || ! $pasar) {
            return response()->json([
                'success' => false,
                'message' => 'Bahan atau Pasar tidak ditemukan',
                'data' => [],
            ], 404);
        }

        $hasil = Harga::getRiwayatPerubahan($bahan->id, $pasar->id, $startDate, $endDate);
        $riwayat = $hasil['riwayat'];

        $formattedData = collect($riwayat)->map(function ($item) {
            $carbonDate = Carbon::parse($item['tanggal']);
            $tglFormatted = strtolower($carbonDate->translatedFormat('j M Y'));
            $hargaFormatted = number_format($item['harga'], 0, ',', '.');
            $note = '';

            if ($item['status'] === 'up') {
                $note = ' (up '.number_format($item['selisih'], 0, ',', '.').')';
            } elseif ($item['status'] === 'down') {
                $note = ' (down '.number_format(abs($item['selisih']), 0, ',', '.').')';
            }

            $logText = "tanggal {$tglFormatted} : {$hargaFormatted}{$note}";

            return [
                'tanggal' => $item['tanggal'],
                'tanggal_formatted' => $carbonDate->translatedFormat('d M Y'),
                'harga' => (int) $item['harga'],
                'harga_formatted' => 'Rp '.number_format($item['harga'], 0, ',', '.'),
                'prev_harga' => $item['prev_harga'] !== null ? (int) $item['prev_harga'] : null,
                'prev_harga_formatted' => $item['prev_harga'] !== null ? 'Rp '.number_format($item['prev_harga'], 0, ',', '.') : null,
                'selisih' => (int) $item['selisih'],
                'selisih_formatted' => ($item['selisih'] > 0 ? '+Rp ' : ($item['selisih'] < 0 ? '-Rp ' : 'Rp ')).number_format(abs($item['selisih']), 0, ',', '.'),
                'persen' => round($item['persen'], 2),
                'persen_formatted' => ($item['persen'] > 0 ? '+' : '').number_format($item['persen'], 2).'%',
                'status' => $item['status'],
                'keterangan' => $logText,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data Riwayat Perubahan Harga Ditemukan',
            'bahan' => [
                'id' => $bahan->id,
                'nama' => $bahan->nama,
                'satuan' => $bahan->satuan?->nama,
            ],
            'pasar' => [
                'id' => $pasar->id,
                'nama' => $pasar->nama,
            ],
            'periode' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'ringkasan' => [
                'harga_awal' => $hasil['data_awal'] ? (int) $hasil['data_awal']['harga'] : null,
                'harga_terkini' => $hasil['data_terkini'] ? (int) $hasil['data_terkini']['harga'] : null,
                'total_perubahan' => count($riwayat) > 1 ? count($riwayat) - 1 : 0,
                'total_naik' => $hasil['total_naik'],
                'total_turun' => $hasil['total_turun'],
            ],
            'total_data' => $formattedData->count(),
            'log_format' => $formattedData->pluck('keterangan')->values(),
            'data' => $formattedData->values(),
        ]);
    }

    public function perubahanHarga(Request $request): JsonResponse
    {
        $request->validate([
            'pasar_id' => 'nullable|exists:pasar,id',
            'bahan_id' => 'nullable|exists:bahan,id',
        ]);

        if ($request->filled('bahan_id')) {
            return $this->perubahanHargaBahan($request);
        }

        return $this->perubahanHargaPasar($request);
    }

    private function perubahanHargaPasar(Request $request): JsonResponse
    {
        $pasarId = $request->input('pasar_id');
        if (! $pasarId) {
            $defaultPasar = Pasar::where('nama', 'like', '%Pekauman%')->first() ?? Pasar::first();
            $pasarId = $defaultPasar?->id;
        }

        $pasar = $pasarId ? Pasar::with('bahan.satuan')->find($pasarId) : null;

        if (! $pasar) {
            return response()->json([
                'success' => false,
                'message' => 'Pasar tidak ditemukan',
                'data' => [],
            ], 404);
        }

        $targetBahan = $pasar->bahan;
        $targetBahanIds = $targetBahan->pluck('id')->all();

        $query = DB::table('harga')
            ->where('pasar_id', $pasar->id)
            ->where('harga', '>', 0);

        if (! empty($targetBahanIds)) {
            $query->whereIn('bahan_id', $targetBahanIds);
        }

        $rows = $query->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get(['tanggal', 'harga', 'bahan_id']);

        $results = $this->extractLatestPriceChanges($rows, 'bahan_id');

        $bahanMap = $targetBahan->keyBy('id');
        $missingBahanIds = array_diff(array_keys($results), $bahanMap->keys()->all());
        if (! empty($missingBahanIds)) {
            $extraBahan = Bahan::with('satuan')->findMany($missingBahanIds)->keyBy('id');
            $bahanMap = $bahanMap->union($extraBahan);
        }

        $formattedData = collect($results)->map(function ($change, $bahanId) use ($bahanMap) {
            $bahan = $bahanMap->get($bahanId);
            $carbonDate = Carbon::parse($change['tanggal']);
            $tglFormatted = strtolower($carbonDate->translatedFormat('j M Y'));
            $hargaFormatted = number_format($change['harga'], 0, ',', '.');
            $note = '';

            if ($change['status'] === 'up') {
                $note = ' (up '.number_format($change['selisih'], 0, ',', '.').')';
            } elseif ($change['status'] === 'down') {
                $note = ' (down '.number_format(abs($change['selisih']), 0, ',', '.').')';
            }

            $logText = "tanggal {$tglFormatted} : {$hargaFormatted}{$note}";

            return [
                'bahan_id' => (int) $bahanId,
                'bahan_nama' => $bahan?->nama,
                'satuan' => $bahan?->satuan?->nama,
                'bahan' => [
                    'id' => (int) $bahanId,
                    'nama' => $bahan?->nama,
                    'satuan' => $bahan?->satuan?->nama,
                ],
                'tanggal' => $change['tanggal'],
                'tanggal_formatted' => $carbonDate->translatedFormat('d M Y'),
                'harga' => (int) $change['harga'],
                'harga_formatted' => 'Rp '.number_format($change['harga'], 0, ',', '.'),
                'prev_harga' => (int) $change['prev_harga'],
                'prev_harga_formatted' => 'Rp '.number_format($change['prev_harga'], 0, ',', '.'),
                'selisih' => (int) $change['selisih'],
                'selisih_formatted' => ($change['selisih'] > 0 ? '+Rp ' : ($change['selisih'] < 0 ? '-Rp ' : 'Rp ')).number_format(abs($change['selisih']), 0, ',', '.'),
                'persen' => round($change['persen'], 2),
                'persen_formatted' => ($change['persen'] > 0 ? '+' : '').number_format($change['persen'], 2).'%',
                'status' => $change['status'],
                'keterangan' => $logText,
            ];
        })->sort(function ($a, $b) {
            if ($a['tanggal'] === $b['tanggal']) {
                return strcmp($a['bahan_nama'] ?? '', $b['bahan_nama'] ?? '');
            }

            return strcmp($b['tanggal'], $a['tanggal']);
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Data Perubahan Harga Terakhir Ditemukan',
            'pasar' => [
                'id' => $pasar->id,
                'nama' => $pasar->nama,
            ],
            'ringkasan' => [
                'total_bahan_berubah' => $formattedData->count(),
                'total_naik' => $formattedData->where('status', 'up')->count(),
                'total_turun' => $formattedData->where('status', 'down')->count(),
            ],
            'total_data' => $formattedData->count(),
            'log_format' => $formattedData->pluck('keterangan')->values(),
            'data' => $formattedData,
        ]);
    }

    private function perubahanHargaBahan(Request $request): JsonResponse
    {
        $bahanId = $request->input('bahan_id');
        $bahan = Bahan::with(['satuan', 'pasar'])->find($bahanId);

        if (! $bahan) {
            return response()->json([
                'success' => false,
                'message' => 'Bahan tidak ditemukan',
                'data' => [],
            ], 404);
        }

        $targetPasars = $bahan->pasar;
        $pasarId = $request->input('pasar_id');

        if ($pasarId) {
            $targetPasarIds = [(int) $pasarId];
        } else {
            $targetPasarIds = $targetPasars->pluck('id')->all();
        }

        $query = DB::table('harga')
            ->where('bahan_id', $bahan->id)
            ->where('harga', '>', 0);

        if (! empty($targetPasarIds)) {
            $query->whereIn('pasar_id', $targetPasarIds);
        }

        $rows = $query->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get(['tanggal', 'harga', 'pasar_id']);

        $results = $this->extractLatestPriceChanges($rows, 'pasar_id');

        $pasarMap = $targetPasars->keyBy('id');
        $missingPasarIds = array_diff(array_keys($results), $pasarMap->keys()->all());
        if (! empty($missingPasarIds)) {
            $extraPasars = Pasar::findMany($missingPasarIds)->keyBy('id');
            $pasarMap = $pasarMap->union($extraPasars);
        }

        $formattedData = collect($results)->map(function ($change, $pasarId) use ($pasarMap, $bahan) {
            $pasar = $pasarMap->get($pasarId);
            $carbonDate = Carbon::parse($change['tanggal']);
            $tglFormatted = strtolower($carbonDate->translatedFormat('j M Y'));
            $hargaFormatted = number_format($change['harga'], 0, ',', '.');
            $note = '';

            if ($change['status'] === 'up') {
                $note = ' (up '.number_format($change['selisih'], 0, ',', '.').')';
            } elseif ($change['status'] === 'down') {
                $note = ' (down '.number_format(abs($change['selisih']), 0, ',', '.').')';
            }

            $logText = "tanggal {$tglFormatted} : {$hargaFormatted}{$note}";

            return [
                'pasar_id' => (int) $pasarId,
                'pasar_nama' => $pasar?->nama,
                'pasar' => [
                    'id' => (int) $pasarId,
                    'nama' => $pasar?->nama,
                ],
                'bahan_id' => (int) $bahan->id,
                'bahan_nama' => $bahan->nama,
                'satuan' => $bahan->satuan?->nama,
                'bahan' => [
                    'id' => (int) $bahan->id,
                    'nama' => $bahan->nama,
                    'satuan' => $bahan->satuan?->nama,
                ],
                'tanggal' => $change['tanggal'],
                'tanggal_formatted' => $carbonDate->translatedFormat('d M Y'),
                'harga' => (int) $change['harga'],
                'harga_formatted' => 'Rp '.number_format($change['harga'], 0, ',', '.'),
                'prev_harga' => (int) $change['prev_harga'],
                'prev_harga_formatted' => 'Rp '.number_format($change['prev_harga'], 0, ',', '.'),
                'selisih' => (int) $change['selisih'],
                'selisih_formatted' => ($change['selisih'] > 0 ? '+Rp ' : ($change['selisih'] < 0 ? '-Rp ' : 'Rp ')).number_format(abs($change['selisih']), 0, ',', '.'),
                'persen' => round($change['persen'], 2),
                'persen_formatted' => ($change['persen'] > 0 ? '+' : '').number_format($change['persen'], 2).'%',
                'status' => $change['status'],
                'keterangan' => $logText,
            ];
        })->sort(function ($a, $b) {
            if ($a['tanggal'] === $b['tanggal']) {
                return strcmp($a['pasar_nama'] ?? '', $b['pasar_nama'] ?? '');
            }

            return strcmp($b['tanggal'], $a['tanggal']);
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Data Perubahan Harga Terakhir Ditemukan',
            'bahan' => [
                'id' => $bahan->id,
                'nama' => $bahan->nama,
                'satuan' => $bahan->satuan?->nama,
            ],
            'ringkasan' => [
                'total_pasar_berubah' => $formattedData->count(),
                'total_naik' => $formattedData->where('status', 'up')->count(),
                'total_turun' => $formattedData->where('status', 'down')->count(),
            ],
            'total_pasar' => $formattedData->count(),
            'total_data' => $formattedData->count(),
            'log_format' => $formattedData->pluck('keterangan')->values(),
            'data' => $formattedData,
        ]);
    }

    private function extractLatestPriceChanges($rows, string $groupKey): array
    {
        $latestPrices = [];
        $changeDates = [];
        $prevDifferent = [];
        $lastSeenDates = [];
        $completed = [];

        foreach ($rows as $row) {
            $id = (int) $row->{$groupKey};
            if (isset($completed[$id])) {
                continue;
            }

            if (isset($lastSeenDates[$id]) && $lastSeenDates[$id] === $row->tanggal) {
                continue;
            }
            $lastSeenDates[$id] = $row->tanggal;

            $p = (float) $row->harga;

            if (! isset($latestPrices[$id])) {
                $latestPrices[$id] = $p;
                $changeDates[$id] = $row->tanggal;
            } elseif ($p === $latestPrices[$id]) {
                $changeDates[$id] = $row->tanggal;
            } else {
                $prevDifferent[$id] = $p;
                $completed[$id] = true;
            }
        }

        $results = [];
        foreach ($prevDifferent as $id => $prevP) {
            $currP = $latestPrices[$id];
            $selisih = $currP - $prevP;
            $results[$id] = [
                'tanggal' => $changeDates[$id],
                'harga' => $currP,
                'prev_harga' => $prevP,
                'selisih' => $selisih,
                'persen' => $prevP > 0 ? ($selisih / $prevP) * 100 : 0.0,
                'status' => $selisih > 0 ? 'up' : 'down',
            ];
        }

        return $results;
    }

    public function riwayatHargaTerakhir(Request $request): JsonResponse
    {
        return $this->perubahanHarga($request);
    }
}
