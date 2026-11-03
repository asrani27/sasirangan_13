@extends('layouts.admin')

@section('title')
<div>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Ganti Password</h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Perbarui kata sandi akun administrator Anda</p>
</div>
@endsection

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-xs">
                <i class="fas fa-key"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900">Keamanan Akun</h3>
                <p class="text-xs text-slate-500">Pastikan gunakan kombinasi sandi yang kuat dan aman</p>
            </div>
        </div>

        <form action="/data/gantipassword" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Username Administrator</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" value="{{ Auth::user()->name }}" readonly class="w-full pl-10 pr-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-semibold text-slate-600 cursor-not-allowed">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password Baru</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" required placeholder="Masukkan kata sandi baru" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-emerald-600 outline-none transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Konfirmasi Password Baru</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"><i class="fas fa-shield-halved"></i></span>
                    <input type="password" name="password2" required placeholder="Ulangi kata sandi baru" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-emerald-600 outline-none transition">
                </div>
            </div>

            <div class="pt-3">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                    <i class="fas fa-check"></i>
                    <span>Simpan Perubahan Password</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
