<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BeritaController extends Controller
{
    public function index(): View
    {
        $data = Berita::with('kategori')->orderBy('id', 'DESC')->paginate(10);

        return view('admin.berita.index', compact('data'));
    }

    public function create(): View
    {
        $kategori = Kategori::orderBy('nama')->get();

        return view('admin.berita.add', compact('kategori'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required',
            'kategori_id' => 'required',
            'file' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
        ], [
            'file.max' => 'File Harus Gambar Dan Max 2MB',
            'file.image' => 'File Harus Gambar',
        ]);

        $attr = $request->except('file');

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = date('d-m-Y-').rand(1, 9999).'_'.preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->storeAs('', $filename, 'public');
            $attr['file'] = $filename;
        }

        Berita::create($attr);
        toastr()->success('Berita Berhasil Di Simpan');

        return redirect('/informasi/berita');
    }

    public function edit(int|string $id): View
    {
        $kategori = Kategori::orderBy('nama')->get();
        $data = Berita::findOrFail($id);

        return view('admin.berita.edit', compact('kategori', 'data'));
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required',
            'kategori_id' => 'required',
            'file' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
        ], [
            'file.max' => 'File Harus Gambar Dan Max 2MB',
            'file.image' => 'File Harus Gambar',
        ]);

        $berita = Berita::findOrFail($id);
        $attr = $request->except('file');

        if ($request->hasFile('file')) {
            if ($berita->file && Storage::disk('public')->exists($berita->file)) {
                Storage::disk('public')->delete($berita->file);
            }

            $file = $request->file('file');
            $filename = date('d-m-Y-').rand(1, 9999).'_'.preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->storeAs('', $filename, 'public');
            $attr['file'] = $filename;
        }

        $berita->update($attr);
        toastr()->success('Berita Berhasil Di Update');

        return redirect('/informasi/berita');
    }

    public function delete(int|string $id): RedirectResponse
    {
        $berita = Berita::findOrFail($id);
        if ($berita->file && Storage::disk('public')->exists($berita->file)) {
            Storage::disk('public')->delete($berita->file);
        }
        $berita->delete();

        toastr()->success('Berita Berhasil Di Hapus');

        return back();
    }
}
