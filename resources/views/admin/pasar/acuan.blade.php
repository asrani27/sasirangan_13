@extends('layouts.admin')

@section('title')
<div class="flex items-center gap-3">
    <a href="/data/pasar/harga/{{ $id }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Harga Acuan Komoditi</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
            {{ $pasar->nama }} &bull; Periode {{ \Carbon\Carbon::createFromFormat('d/m','01/'.$bulantahun->bulan)->translatedFormat('F') }} {{ $bulantahun->tahun }}
        </p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Input Harga Acuan Komoditi</h3>
                <p class="text-xs text-slate-400">Harga acuan menjadi patokan ambang batas peringatan dini EWS</p>
            </div>
            <span class="text-xs font-semibold text-slate-400">Total: {{ $bahan->count() }} komoditi</span>
        </div>

        <form action="/data/pasar/harga/{{ $id }}/acuan/{{ $bulan_id }}" method="POST">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Nama Komoditi</th>
                            <th class="py-3 px-4 w-56 text-right">Harga Acuan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $no = 1; @endphp
                        @forelse ($bahan as $item)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3 px-4 text-center text-slate-400 font-medium">{{ $no++ }}</td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900 text-sm">{{ $item->nama }}</span>
                                <span class="text-[11px] text-slate-400 font-medium block">Satuan: {{ $item->satuan ? $item->satuan->nama : 'N/A' }}</span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <input type="hidden" name="bapok_id[]" value="{{ $item->id }}">
                                <div class="relative inline-block w-44">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                    <input type="number" name="harga[]" value="{{ $item->harga }}" min="0" required class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-900 text-right focus:bg-white focus:border-emerald-600 outline-none transition">
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-400">Belum ada komoditi bahan terdaftar di pasar ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <a href="/data/pasar/harga/{{ $id }}" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-xs font-semibold transition">
                    Kembali
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                    <i class="fas fa-save"></i>
                    <span>Simpan Harga Acuan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
