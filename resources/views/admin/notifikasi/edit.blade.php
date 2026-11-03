@extends('layouts.admin')

@section('title')
<div class="flex items-center gap-3">
    <a href="/data/notifikasi" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Edit Kontak Notifikasi</h1>
        <p class="text-xs text-slate-500">Perbarui nomor penerima broadcast EWS</p>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs sticky top-24">
            <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-pencil text-emerald-600"></i>
                <span>Perbarui Nomor</span>
            </h3>

            <form action="/data/notifikasi/edit/{{ $edit->id }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor WhatsApp</label>
                    <input type="text" name="nomor" value="{{ $edit->nomor }}" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none transition">
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                        <i class="fas fa-check"></i>
                        <span>Update</span>
                    </button>
                    <a href="/data/notifikasi" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Daftar Kontak Lainnya</h3>
                <span class="text-xs font-semibold text-slate-400">Total: {{ $data->total() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Nomor WhatsApp</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $no = $data->firstItem() ?? 1; @endphp
                        @forelse ($data as $item)
                        <tr class="hover:bg-slate-50/70 {{ $item->id == $edit->id ? 'bg-emerald-50/50' : '' }}">
                            <td class="py-3 px-4 text-center text-slate-400 font-medium">{{ $no++ }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 text-sm">
                                <i class="fab fa-whatsapp text-emerald-600 mr-1.5"></i>{{ $item->nomor }}
                                @if ($item->id == $edit->id)
                                    <span class="ml-2 text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-sans font-semibold">Aktif</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="/data/notifikasi/edit/{{ $item->id }}" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 inline-flex items-center justify-center transition">
                                        <i class="fas fa-pencil text-xs"></i>
                                    </a>
                                    <a href="/data/notifikasi/delete/{{ $item->id }}" onclick="return confirm('Hapus nomor ini?')" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white inline-flex items-center justify-center border border-rose-200 transition">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-400">Belum ada data.</td>
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
