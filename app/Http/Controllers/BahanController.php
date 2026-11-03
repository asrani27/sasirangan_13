<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Kelompok;
use App\Models\Satuan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BahanController extends Controller
{
    public function index(): View
    {
        $data = Bahan::with(['satuan', 'kelompok'])->orderBy('id')->paginate(15);

        return view('admin.bahan.index', compact('data'));
    }

    public function create(): View
    {
        $satuan = Satuan::orderBy('nama')->get();
        $kelompok = Kelompok::orderBy('nama')->get();

        return view('admin.bahan.add', compact('satuan', 'kelompok'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'satuan_id' => 'nullable|integer',
            'kelompok_id' => 'nullable|integer',
        ]);

        $check = Bahan::where('nama', $request->nama)->first();
        if ($check != null) {
            toastr()->error('Nama Bahan Sudah Ada');

            return back();
        }

        Bahan::create($request->all());
        toastr()->success('Nama Bahan Berhasil Di Simpan');

        return redirect('/data/bahan');
    }

    public function edit(int|string $id): View
    {
        $data = Bahan::findOrFail($id);
        $satuan = Satuan::orderBy('nama')->get();
        $kelompok = Kelompok::orderBy('nama')->get();

        return view('admin.bahan.edit', compact('satuan', 'kelompok', 'data'));
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $bahan = Bahan::findOrFail($id);
        $bahan->update($request->all());

        toastr()->success('Bahan Berhasil Di Update');

        return redirect('/data/bahan');
    }

    public function delete(int|string $id): RedirectResponse
    {
        $b = Bahan::findOrFail($id);
        $b->pasar()->detach();
        $b->delete();

        toastr()->success('Bahan Berhasil Di Hapus');

        return back();
    }

    public function updateUser(Request $request): JsonResponse
    {
        $data = Bahan::findOrFail($request->pk);
        $data->nama = $request->value;
        $data->save();

        return response()->json(['success' => 'done']);
    }
}
