@extends('layouts.admin')

@section('title')
<div>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Tambah Berita</h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tulis artikel atau pengumuman seputar pangan & pasar</p>
</div>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <form method="POST" action="/informasi/berita/add" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kategori Berita</label>
                <select name="kategori_id" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:bg-white focus:border-emerald-600 outline-none">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($kategori as $k)
                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Judul Berita</label>
                <input type="text" name="judul" required placeholder="Masukkan judul berita utama" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-emerald-600 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Isi / Konten Berita</label>
                <textarea name="isi" rows="8" required placeholder="Tuliskan isi berita selengkapnya..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-normal text-slate-800 focus:bg-white focus:border-emerald-600 outline-none leading-relaxed"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Gambar Sampul / Thumbnail</label>
                <input type="file" name="file" accept="image/png,image/jpeg,image/jpg" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                <p class="text-[11px] text-slate-400 mt-1">Format: JPG, JPEG, PNG (Maksimal 2MB)</p>
            </div>

            <div class="pt-3 flex items-center gap-3 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                    <i class="fas fa-save"></i>
                    <span>Simpan & Publikasikan</span>
                </button>
                <a href="/informasi/berita" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
