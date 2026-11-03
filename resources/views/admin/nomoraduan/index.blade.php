@extends('layouts.admin')

@section('title')
<div>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Nomor Layanan Aduan</h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola nomor kontak WhatsApp resmi pengaduan harga & ketersediaan bahan pangan</p>
</div>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
        <div class="flex items-start gap-4 mb-6">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0 shadow-xs">
                <i class="fab fa-whatsapp"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-900">Saluran Aduan Masyarakat</h2>
                <p class="text-xs text-slate-500 leading-relaxed mt-0.5">
                    Nomor ini ditampilkan pada portal publik dan footer website agar masyarakat dapat langsung menyampaikan keluhan harga atau kelangkaan bahan pokok.
                </p>
            </div>
        </div>

        <form action="/data/nomoraduan" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nomor WhatsApp Pengaduan
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-mono text-xs font-bold">
                        <i class="fas fa-phone mr-1"></i>
                    </span>
                    <input type="text" name="nomor" value="{{ $data == null ? '' : $data->nomor }}" required placeholder="Contoh: 081234567890 atau 6281234567890" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none transition">
                </div>
                <p class="text-[11px] text-slate-400 mt-1.5">
                    Gunakan format nomor aktif dengan awalan 08 atau 62 tanpa spasi dan tanda minus.
                </p>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition">
                    <i class="fas fa-save"></i>
                    <span>Simpan Nomor Aduan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
