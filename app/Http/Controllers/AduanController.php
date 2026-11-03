<?php

namespace App\Http\Controllers;

use App\Models\NomorAduan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AduanController extends Controller
{
    public function index(): View
    {
        $data = NomorAduan::first();

        return view('admin.nomoraduan.index', compact('data'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate(['nomor' => 'required|string']);

        $aduan = NomorAduan::first();
        if ($aduan == null) {
            NomorAduan::create(['nomor' => $request->nomor]);
        } else {
            $aduan->update(['nomor' => $request->nomor]);
        }

        toastr()->success('Nomor Aduan Berhasil Di Update');

        return back();
    }
}
