@extends('layouts.public')

@section('title', 'Login Operator & Admin - DEDIKASI')

@section('content')
<div class="min-h-[75vh] flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
        <!-- Card Header -->
        <div class="text-center space-y-3">
            <div class="inline-flex w-16 h-16 rounded-3xl bg-gradient-to-tr from-emerald-600 to-teal-700 text-white items-center justify-center text-3xl font-black shadow-lg shadow-emerald-500/20">
                D
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Masuk ke DEDIKASI</h1>
                <p class="text-xs text-slate-500 mt-1">Sistem Pemantauan Harga &amp; Ketersediaan Pangan Daerah</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200/90 shadow-sm space-y-6">
            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <i class="fas fa-circle-exclamation"></i>
                        <span>Gagal Masuk</span>
                    </div>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/login" class="space-y-5">
                @csrf
                <div class="space-y-1.5">
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fas fa-user text-sm"></i>
                        </span>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus autocomplete="username"
                               class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                               placeholder="Masukkan username anda">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                               class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                               placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-3 px-4 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 shadow-md shadow-emerald-600/20 hover:shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-right-to-bracket"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <div class="pt-4 border-t border-slate-100 text-center">
                <a href="{{ route('beranda') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-emerald-700 transition">
                    <i class="fas fa-arrow-left text-[10px]"></i>
                    <span>Kembali ke Beranda Publik</span>
                </a>
            </div>
        </div>

        <div class="text-center text-xs text-slate-400">
            Dinas Perdagangan dan Perindustrian Kota Banjarmasin
        </div>
    </div>
</div>
@endsection
