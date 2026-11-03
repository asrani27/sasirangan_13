@extends('layouts.admin')

@section('title')
<div>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Banner Slider</h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola banner promosi dan tayangan slide pada beranda publik</p>
</div>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Top Action Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <a href="/informasi/slider/add" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
            <i class="fas fa-plus"></i>
            <span>Tambah Slider</span>
        </a>
        <div class="text-xs text-slate-500 font-medium">
            Total Slider: <span class="font-bold text-slate-800">{{ $data->total() }}</span>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3 w-10 text-center">No</th>
                        <th class="py-3 px-3 w-36">Gambar Banner</th>
                        <th class="py-3 px-3">Judul Slider</th>
                        <th class="py-3 px-3 w-28 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($data as $idx => $item)
                    <tr class="hover:bg-slate-50/70">
                        <td class="py-3 px-3 text-center text-slate-400 font-medium">{{ $data->firstItem() + $idx }}</td>
                        <td class="py-3 px-3">
                            @if (!empty($item->file) && (Storage::disk('public')->exists($item->file) || file_exists(public_path('storage/' . $item->file))))
                                <img src="/storage/{{ $item->file }}"
                                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=200&q=80';"
                                     alt="Banner" class="w-28 h-14 object-cover rounded-xl border border-slate-200 shadow-2xs">
                            @else
                                <div class="w-28 h-14 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 text-xs">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td class="py-3 px-3">
                            <span class="font-bold text-slate-900 text-xs">{{ $item->judul }}</span>
                            <span class="block text-[10px] text-slate-400 mt-0.5">{{ $item->created_at ? $item->created_at->format('d M Y, H:i') : '-' }}</span>
                        </td>
                        <td class="py-3 px-3 text-center">
                            <div class="inline-flex items-center gap-1">
                                <a href="/informasi/slider/edit/{{ $item->id }}" class="w-7 h-7 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 inline-flex items-center justify-center transition" title="Edit">
                                    <i class="fas fa-pen text-[10px]"></i>
                                </a>
                                <a href="/informasi/slider/delete/{{ $item->id }}" onclick="return confirm('Yakin menghapus Slider ini?');" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 inline-flex items-center justify-center transition" title="Hapus">
                                    <i class="fas fa-trash-can text-[10px]"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-slate-400">Belum ada slider terdaftar.</td>
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
@endsection
