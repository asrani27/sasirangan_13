<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SatuanController extends Controller
{
    public function index(): View
    {
        $data = Satuan::orderBy('id', 'DESC')->paginate(10);

        return view('admin.satuan.index', compact('data'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['nama' => 'required|string|max:255']);

        $check = Satuan::where('nama', $request->nama)->first();
        if ($check != null) {
            toastr()->error('Nama Satuan Sudah Ada');

            return back();
        }

        Satuan::create($request->all());
        toastr()->success('Nama Satuan Berhasil Di Simpan');

        return back();
    }

    public function edit(int|string $id): View
    {
        $data = Satuan::orderBy('id', 'DESC')->paginate(10);
        $edit = Satuan::findOrFail($id);

        return view('admin.satuan.edit', compact('data', 'edit'));
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $request->validate(['nama' => 'required|string|max:255']);

        $data = Satuan::findOrFail($id);
        $data->nama = $request->nama;
        $data->save();

        toastr()->success('Nama Satuan Berhasil Di Update');

        return redirect('/data/satuan');
    }

    public function delete(int|string $id): RedirectResponse
    {
        try {
            Satuan::findOrFail($id)->delete();
            toastr()->success('Nama Satuan Berhasil Di Hapus');

            return redirect('/data/satuan');
        } catch (\Throwable $e) {
            toastr()->error('Tidak bisa di hapus karena ada bahan yg mengacu pada data ini');

            return back();
        }
    }
}
