@extends('layouts.admin')

@section('title')
<div>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Daftar Penerima Notifikasi EWS</h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola nomor WhatsApp stakeholder & pimpinan untuk broadcast peringatan dini kenaikan harga</p>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs sticky top-24">
            <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-bell text-emerald-600"></i>
                <span>Tambah Nomor Penerima</span>
            </h3>

            <form action="/data/notifikasi/add" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor WhatsApp</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                            <i class="fab fa-whatsapp"></i>
                        </span>
                        <input type="text" name="nomor" placeholder="08xxxxxxxxxx" required class="w-full pl-8 pr-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none transition">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Masukkan format 08 atau 62 tanpa spasi</p>
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                    <i class="fas fa-plus"></i>
                    <span>Simpan Nomor</span>
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Daftar Kontak EWS</h3>
                    <p class="text-[11px] text-slate-400">Penerima rekap kenaikan komoditi harian</p>
                </div>
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
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3 px-4 text-center text-slate-400 font-medium">{{ $no++ }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 text-sm">
                                <i class="fab fa-whatsapp text-emerald-600 mr-1.5"></i>{{ $item->nomor }}
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="/data/notifikasi/kirim/{{ $item->id }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white rounded-lg text-xs font-semibold border border-emerald-200 transition" title="Kirim Notifikasi Tes">
                                        <i class="fas fa-paper-plane text-[10px]"></i>
                                        <span>Tes</span>
                                    </a>
                                    <a href="/data/notifikasi/edit/{{ $item->id }}" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 inline-flex items-center justify-center transition">
                                        <i class="fas fa-pencil text-xs"></i>
                                    </a>
                                    <a href="/data/notifikasi/delete/{{ $item->id }}" onclick="return confirm('Hapus nomor ini dari daftar notifikasi?')" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white inline-flex items-center justify-center border border-rose-200 transition">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-400">Belum ada nomor terdaftar.</td>
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
