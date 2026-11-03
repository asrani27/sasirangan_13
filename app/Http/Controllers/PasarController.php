<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\BulanTahun;
use App\Models\Harga;
use App\Models\HargaAcuan;
use App\Models\Pasar;
use App\Models\PasarUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasarController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pasar::with(['user', 'bahan'])->orderBy('id', 'DESC');

        if ($request->filled('search')) {
            $query->where('nama', 'LIKE', '%'.$request->search.'%');
        }

        $data = $query->paginate(10)->withQueryString();
        $user = User::where('username', '!=', 'superadmin')->orderBy('name')->get();
        $totalPasar = Pasar::count();
        $totalAktifStok = Pasar::where('tampil_stok', 'Y')->count();
        $totalGps = Pasar::whereNotNull('lat')->where('lat', '!=', '')->count();

        return view('admin.pasar.index', compact('data', 'user', 'totalPasar', 'totalAktifStok', 'totalGps'));
    }

    public function generateHarga(): RedirectResponse
    {
        $today = Carbon::today()->format('Y-m-d');
        $now = Carbon::now();

        // Optimized: pre-load existing prices for today
        $existing = Harga::where('tanggal', $today)
            ->get()
            ->keyBy(fn ($h) => "{$h->pasar_id}_{$h->bahan_id}");

        $pasars = Pasar::with('bahan')->get();
        $newRecords = [];

        foreach ($pasars as $p) {
            foreach ($p->bahan as $b) {
                $key = "{$p->id}_{$b->id}";
                if (! isset($existing[$key])) {
                    $newRecords[] = [
                        'tanggal' => $today,
                        'pasar_id' => $p->id,
                        'bahan_id' => $b->id,
                        'harga' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (! empty($newRecords)) {
            foreach (array_chunk($newRecords, 500) as $chunk) {
                Harga::insert($chunk);
            }
        }

        toastr()->success('Berhasil digenerate');

        return back();
    }

    public function petugas(Request $request): RedirectResponse
    {
        $check = PasarUser::where('pasar_id', $request->pasar_id)
            ->where('user_id', $request->user_id)
            ->first();

        if ($check == null) {
            PasarUser::create([
                'pasar_id' => $request->pasar_id,
                'user_id' => $request->user_id,
            ]);
            toastr()->success('Petugas berhasil Ditambahkan');
        } else {
            toastr()->error('Petugas Sudah terdaftar di pasar ini');
        }

        return back();
    }

    public function hapusPetugas(int|string $id): RedirectResponse
    {
        Pasar::findOrFail($id)->user()->detach();
        toastr()->success('Petugas dihapus');

        return back();
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tampil_stok' => 'nullable|string|in:Y,T',
        ]);

        $check = Pasar::where('nama', $request->nama)->first();
        if ($check) {
            toastr()->error('Nama Pasar Sudah Ada');

            return back();
        }

        $p = Pasar::create([
            'nama' => $request->nama,
            'tampil_stok' => $request->tampil_stok ?? 'T',
        ]);
        $bahan_ids = Bahan::pluck('id');
        $p->bahan()->attach($bahan_ids);

        toastr()->success('Nama Pasar Berhasil Di Simpan');

        return back();
    }

    public function edit(int|string $id): View
    {
        $data = Pasar::orderBy('id', 'DESC')->paginate(10);
        $edit = Pasar::findOrFail($id);

        return view('admin.pasar.edit', compact('data', 'edit'));
    }

    public function bapok(int|string $id): View
    {
        $data = Pasar::findOrFail($id);
        $bahanku = $data->bahan->pluck('id')->toArray();
        $bapok = Bahan::orderBy('nama')->get();

        return view('admin.pasar.bapok', compact('data', 'bapok', 'bahanku'));
    }

    public function updateBapok(Request $request, int|string $id): RedirectResponse
    {
        Pasar::findOrFail($id)->bahan()->sync($request->bahan_id ?? []);
        toastr()->success('Berhasil Di Update');

        return back();
    }

    public function lokasi(int|string $id): View
    {
        $edit = Pasar::findOrFail($id);
        $latlong = [
            'lat' => $edit->lat ?? -3.327653847548605,
            'lng' => $edit->long ?? 114.5884147286779,
        ];
        $radius = $edit->radius ?? 100;

        return view('admin.pasar.lokasi', compact('edit', 'latlong', 'radius'));
    }

    public function harga(int|string $id): View
    {
        $pasar = Pasar::findOrFail($id);
        $bulantahun = BulanTahun::where('pasar_id', $id)->orderBy('tahun', 'DESC')->orderBy('bulan', 'DESC')->get();

        return view('admin.pasar.harga', compact('pasar', 'bulantahun'));
    }

    public function acuan(int|string $id, int|string $bulan_id): View
    {
        $pasar = Pasar::findOrFail($id);
        $bulantahun = BulanTahun::findOrFail($bulan_id);

        $acuanMap = HargaAcuan::where('bulan_tahun_id', $bulan_id)
            ->where('pasar_id', $id)
            ->pluck('harga', 'bahan_id');

        $bahan = $pasar->bahan->map(function ($item) use ($acuanMap) {
            $item->harga = (float) ($acuanMap[$item->id] ?? 0);

            return $item;
        });

        return view('admin.pasar.acuan', compact('bulantahun', 'id', 'bulan_id', 'bahan', 'pasar'));
    }

    public function storeAcuan(Request $request, int|string $id, int|string $bulan_id): RedirectResponse
    {
        if ($request->has('bapok_id')) {
            foreach ($request->bapok_id as $key => $item) {
                HargaAcuan::updateOrCreate(
                    [
                        'bulan_tahun_id' => $bulan_id,
                        'bahan_id' => $item,
                        'pasar_id' => $id,
                    ],
                    [
                        'harga' => $request->harga[$key] ?? 0,
                    ]
                );
            }
        }

        toastr()->success('Berhasil Di Update');

        return back();
    }

    public function deleteBulanTahun(int|string $id, int|string $bulan_id): RedirectResponse
    {
        BulanTahun::where('id', $bulan_id)->where('pasar_id', $id)->delete();
        toastr()->success('Berhasil Di Hapus');

        return back();
    }

    public function bulanTahun(Request $request, int|string $id): RedirectResponse
    {
        $check = BulanTahun::where('pasar_id', $id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->first();

        if ($check == null) {
            BulanTahun::create([
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'pasar_id' => $id,
            ]);
            toastr()->success('berhasil Disimpan');

            return redirect('/data/pasar/harga/'.$id);
        }

        toastr()->error('Sudah ada');

        return back();
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $request->validate(['nama' => 'required|string|max:255']);
        $data = Pasar::findOrFail($id);
        $data->nama = $request->nama;
        $data->tampil_stok = $request->tampil_stok ?? 'T';
        $data->save();

        toastr()->success('Nama Pasar Berhasil Di Update');

        return redirect('/data/pasar');
    }

    public function updateLokasi(Request $request, int|string $id): RedirectResponse
    {
        $data = Pasar::findOrFail($id);
        $data->lat = $request->lat;
        $data->long = $request->long;
        $data->radius = $request->radius;
        $data->save();

        toastr()->success('Nama Pasar Berhasil Di Update');

        return back();
    }

    public function delete(int|string $id): RedirectResponse
    {
        $pasar = Pasar::findOrFail($id);
        $pasar->bahan()->detach();
        $pasar->user()->detach();
        $pasar->delete();

        toastr()->success('Nama Pasar Berhasil Di Hapus');

        return redirect('/data/pasar');
    }
}
