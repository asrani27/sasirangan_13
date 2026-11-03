@extends('layouts.admin')

@section('title')
<div>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Manajemen Akun Petugas</h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola akun login enumerator / petugas pencatat harga pasar</p>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs sticky top-24">
            <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-user-plus text-emerald-600"></i>
                <span>Tambah Akun Petugas</span>
            </h3>

            <form action="/data/user/add" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" placeholder="Nama petugas" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 focus:bg-white focus:border-emerald-600 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Username</label>
                    <input type="text" name="username" placeholder="Username login" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-mono font-semibold text-slate-700 focus:bg-white focus:border-emerald-600 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 focus:bg-white focus:border-emerald-600 outline-none">
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                    <i class="fas fa-plus"></i>
                    <span>Simpan Petugas</span>
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Daftar Akun Petugas</h3>
                    <p class="text-[11px] text-slate-400">Enumerator survei harga pasar</p>
                </div>
                <span class="text-xs font-semibold text-slate-400">Total: {{ $data->total() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Nama & Username</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $no = $data->firstItem() ?? 1; @endphp
                        @forelse ($data as $item)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3 px-4 text-center text-slate-400 font-medium">{{ $no++ }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $item->name }}</div>
                                <span class="text-xs text-emerald-600 font-mono font-medium">@ {{ $item->username }}</span>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="/data/user/reset/{{ $item->id }}" onclick="return confirm('Reset password akun ini menjadi 123123?')" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-amber-50 hover:bg-amber-600 text-amber-700 hover:text-white rounded-lg text-xs font-semibold border border-amber-200 transition" title="Reset Password">
                                        <i class="fas fa-key text-[10px]"></i>
                                        <span>Reset Pass</span>
                                    </a>
                                    <a href="/data/user/delete/{{ $item->id }}" onclick="return confirm('Hapus akun petugas ini?')" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white inline-flex items-center justify-center border border-rose-200 transition">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-400">Belum ada akun petugas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($data->hasPages())
            <div class="p-3 border-t border-slate-100 bg-slate-50/50">
                {{ $data->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
