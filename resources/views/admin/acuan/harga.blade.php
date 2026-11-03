@extends('layouts.admin')

@section('title')
<div class="flex items-center gap-3">
    <a href="/data/hargaacuan" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-xl font-black text-slate-900 tracking-tight">
            Harga Acuan Periode {{ \Carbon\Carbon::createFromDate($bulantahun->tahun, (int)$bulantahun->bulan, 1)->translatedFormat('F Y') }}
        </h1>
        <p class="text-xs text-slate-500">Tetapkan patokan harga dasar tiap komoditi untuk early warning system</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl space-y-4">
    <form action="/data/hargaacuan/bapok/{{ $id }}" method="POST">
        @csrf
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Daftar Komoditi Pangan</h3>
                    <p class="text-[11px] text-slate-400">Tentukan harga acuan per komoditi (dalam Rupiah)</p>
                </div>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition">
                    <i class="fas fa-check"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Nama Komoditi</th>
                            <th class="py-3 px-4 w-64 text-right">Harga Acuan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $no = 1; @endphp
                        @forelse ($bahan as $item)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-2.5 px-4 text-center text-slate-400 font-medium">{{ $no++ }}</td>
                            <td class="py-2.5 px-4 font-semibold text-slate-900 text-sm">
                                {{ $item->nama }}
                                <input type="hidden" name="bapok_id[]" value="{{ $item->id }}">
                            </td>
                            <td class="py-2.5 px-4 text-right">
                                <div class="relative inline-block w-48">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                    <input type="number" name="harga[]" value="{{ $item->harga }}" min="0" required class="w-full pl-9 pr-3 py-1.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 focus:border-emerald-600 rounded-lg text-xs font-mono font-bold text-slate-800 text-right outline-none transition">
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-400">Tidak ada komoditi terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <a href="/data/hargaacuan" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Kembali
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition">
                    <i class="fas fa-check"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
