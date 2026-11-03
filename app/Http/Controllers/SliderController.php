<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SliderController extends Controller
{
    public function index(): View
    {
        $data = Slider::orderBy('id', 'DESC')->paginate(10);

        return view('admin.slider.index', compact('data'));
    }

    public function create(): View
    {
        return view('admin.slider.add');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|image|mimes:png,jpg,jpeg,webp|max:2048',
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

        Slider::create($attr);
        toastr()->success('Slider Berhasil Di Simpan');

        return redirect('/informasi/slider');
    }

    public function edit(int|string $id): View
    {
        $data = Slider::findOrFail($id);

        return view('admin.slider.edit', compact('data'));
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $request->validate([
            'file' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
        ], [
            'file.max' => 'File Harus Gambar Dan Max 2MB',
            'file.image' => 'File Harus Gambar',
        ]);

        $slider = Slider::findOrFail($id);
        $attr = $request->except('file');

        if ($request->hasFile('file')) {
            if ($slider->file && Storage::disk('public')->exists($slider->file)) {
                Storage::disk('public')->delete($slider->file);
            }

            $file = $request->file('file');
            $filename = date('d-m-Y-').rand(1, 9999).'_'.preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->storeAs('', $filename, 'public');
            $attr['file'] = $filename;
        }

        $slider->update($attr);
        toastr()->success('Slider Berhasil Di Update');

        return redirect('/informasi/slider');
    }

    public function delete(int|string $id): RedirectResponse
    {
        $slider = Slider::findOrFail($id);
        if ($slider->file && Storage::disk('public')->exists($slider->file)) {
            Storage::disk('public')->delete($slider->file);
        }
        $slider->delete();

        toastr()->success('Slider Berhasil Di Hapus');

        return back();
    }
}
