@extends('layouts.admin')

@section('title')
<div class="flex items-center gap-3">
    <a href="/data/bahan" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Edit Komoditi Bahan</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Perbarui data komoditi: {{ $data->nama }}</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <form action="/data/bahan/edit/{{ $data->id }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kelompok Komoditi</label>
                <select name="kelompok_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none transition">
                    @foreach ($kelompok as $k)
                        <option value="{{ $k->id }}" {{ $data->kelompok_id == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Komoditi Bahan Pokok</label>
                <input type="text" name="nama" value="{{ $data->nama }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Satuan Ukuran</label>
                <select name="satuan_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none transition">
                    @foreach ($satuan as $s)
                        <option value="{{ $s->id }}" {{ $data->satuan_id == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center gap-3">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                    <i class="fas fa-check"></i>
                    <span>Perbarui Komoditi</span>
                </button>
                <a href="/data/bahan" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
