<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(): View
    {
        $data = Kategori::orderBy('id', 'DESC')->paginate(10);

        return view('admin.kategori.index', compact('data'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['nama' => 'required|string|max:255']);

        $check = Kategori::where('nama', $request->nama)->first();
        if ($check != null) {
            toastr()->error('Nama Kategori Sudah Ada');

            return back();
        }

        Kategori::create($request->all());
        toastr()->success('Nama Kategori Berhasil Di Simpan');

        return back();
    }

    public function edit(int|string $id): View
    {
        $data = Kategori::orderBy('id', 'DESC')->paginate(10);
        $edit = Kategori::findOrFail($id);

        return view('admin.kategori.edit', compact('data', 'edit'));
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $request->validate(['nama' => 'required|string|max:255']);

        $data = Kategori::findOrFail($id);
        $data->nama = $request->nama;
        $data->save();

        toastr()->success('Nama Kategori Berhasil Di Update');

        return redirect('/informasi/kategori');
    }

    public function delete(int|string $id): RedirectResponse
    {
        Kategori::findOrFail($id)->delete();
        toastr()->success('Nama Kategori Berhasil Di Hapus');

        return redirect('/informasi/kategori');
    }
}
