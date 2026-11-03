@extends('layouts.admin')

@section('title')
<div>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Edit Kategori Berita</h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Perbarui nama kategori berita</p>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Form Edit -->
    <div class="lg:col-span-5">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fas fa-pen"></i>
                </span>
                <h3 class="text-sm font-bold text-slate-900">Perbarui Kategori</h3>
            </div>

            <form action="/informasi/kategori/edit/{{ $edit->id }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kategori</label>
                    <input type="text" name="nama" value="{{ $edit->nama }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-emerald-600 outline-none">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                        <i class="fas fa-check"></i>
                        <span>Update</span>
                    </button>
                    <a href="/informasi/kategori" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition">
                        <span>Batal</span>
                    </a>
                </div>
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
                        <tr class="hover:bg-slate-50/70 {{ $item->id == $edit->id ? 'bg-amber-50/50' : '' }}">
                            <td class="py-3 px-3 text-center text-slate-400 font-medium">{{ $data->firstItem() + $idx }}</td>
                            <td class="py-3 px-3 font-bold text-slate-900">
                                {{ $item->nama }}
                                @if ($item->id == $edit->id)
                                    <span class="ml-1 text-[10px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">Sedang Diedit</span>
                                @endif
                            </td>
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
