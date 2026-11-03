<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\BulanTahun;
use App\Models\HargaAcuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HargaAcuanController extends Controller
{
    public function index(): View
    {
        $data = BulanTahun::orderBy('id', 'DESC')->paginate(10);

        return view('admin.acuan.index', compact('data'));
    }

    public function store(Request $req): RedirectResponse
    {
        $check = BulanTahun::where('bulan', $req->input('bulan'))
            ->where('tahun', $req->input('tahun'))
            ->first();

        if ($check !== null) {
            toastr()->error('Bulan Tahun Sudah Ada');

            return back();
        }

        BulanTahun::create($req->all());
        toastr()->success('Berhasil Di Simpan');

        return back();
    }

    public function hargaacuan($id): View
    {
        $bulantahun = BulanTahun::findOrFail($id);
        $bahan = Bahan::all()->map(function ($item) use ($id) {
            $check = HargaAcuan::where('bulan_tahun_id', $id)
                ->where('bahan_id', $item->id)
                ->first();
            $item->harga = $check ? $check->harga : 0;

            return $item;
        });

        return view('admin.acuan.harga', compact('bulantahun', 'id', 'bahan'));
    }

    public function storeHargaAcuan(Request $req, $id): RedirectResponse
    {
        $bapokIds = $req->input('bapok_id', []);
        $hargas = $req->input('harga', []);

        foreach ($bapokIds as $key => $item) {
            HargaAcuan::updateOrCreate(
                [
                    'bulan_tahun_id' => $id,
                    'bahan_id' => $item,
                ],
                [
                    'harga' => $hargas[$key] ?? 0,
                ]
            );
        }

        toastr()->success('Berhasil Di Update');

        return back();
    }

    public function delete($id): RedirectResponse
    {
        $bulanTahun = BulanTahun::find($id);
        if ($bulanTahun) {
            HargaAcuan::where('bulan_tahun_id', $id)->delete();
            $bulanTahun->delete();
            toastr()->success('Berhasil Di Hapus');
        }

        return redirect('/data/hargaacuan');
    }
}
