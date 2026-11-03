@extends('layouts.admin')

@section('title')
<div>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Kategori Berita</h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola kategori publikasi berita dan artikel bapok</p>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Form Tambah -->
    <div class="lg:col-span-5">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fas fa-plus"></i>
                </span>
                <h3 class="text-sm font-bold text-slate-900">Tambah Kategori</h3>
            </div>

            <form action="/informasi/kategori/add" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kategori</label>
                    <input type="text" name="nama" required placeholder="Contoh: Info Pangan, Operasi Pasar" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-emerald-600 outline-none">
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                    <i class="fas fa-save"></i>
                    <span>Simpan Kategori</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Tabel Daftar -->
    <div class="lg:col-span-7">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Daftar Kategori Berita</h3>
                <span class="text-xs text-slate-400">Total: {{ $data->total() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3 w-10 text-center">No</th>
                            <th class="py-3 px-3">Nama Kategori</th>
                            <th class="py-3 px-3 w-24 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($data as $idx => $item)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3 px-3 text-center text-slate-400 font-medium">{{ $data->firstItem() + $idx }}</td>
                            <td class="py-3 px-3 font-bold text-slate-900">{{ $item->nama }}</td>
                            <td class="py-3 px-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="/informasi/kategori/edit/{{ $item->id }}" class="w-7 h-7 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 inline-flex items-center justify-center transition" title="Edit">
                                        <i class="fas fa-pen text-[10px]"></i>
                                    </a>
                                    <a href="/informasi/kategori/delete/{{ $item->id }}" onclick="return confirm('Akan menghapus berita yang terkait dengan kategori ini, Yakin?');" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 inline-flex items-center justify-center transition" title="Hapus">
                                        <i class="fas fa-trash-can text-[10px]"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-400">Belum ada kategori berita.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($data->hasPages())
            <div class="p-3 border-t border-slate-100">
                {{ $data->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
