<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KelompokController extends Controller
{
    public function index(): View
    {
        $data = Kelompok::orderBy('id', 'DESC')->paginate(10);

        return view('admin.kelompok.index', compact('data'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['nama' => 'required|string|max:255']);

        $check = Kelompok::where('nama', $request->nama)->first();
        if ($check != null) {
            toastr()->error('Nama Kelompok Sudah Ada');

            return back();
        }

        Kelompok::create($request->all());
        toastr()->success('Nama Kelompok Berhasil Di Simpan');

        return back();
    }

    public function edit(int|string $id): View
    {
        $data = Kelompok::orderBy('id', 'DESC')->paginate(10);
        $edit = Kelompok::findOrFail($id);

        return view('admin.kelompok.edit', compact('data', 'edit'));
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $request->validate(['nama' => 'required|string|max:255']);

        $data = Kelompok::findOrFail($id);
        $data->nama = $request->nama;
        $data->save();

        toastr()->success('Nama Kelompok Berhasil Di Update');

        return redirect('/data/kelompok');
    }

    public function delete(int|string $id): RedirectResponse
    {
        try {
            Kelompok::findOrFail($id)->delete();
            toastr()->success('Nama Kelompok Berhasil Di Hapus');

            return redirect('/data/kelompok');
        } catch (\Throwable $e) {
            toastr()->error('Tidak bisa di hapus karena ada bahan yg mengacu pada data ini');

            return back();
        }
    }
}
