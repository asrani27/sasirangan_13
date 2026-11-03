@extends('layouts.admin')

@section('title')
<div class="flex items-center gap-3">
    <a href="/data/pasar" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Harga Acuan: {{ $pasar->nama }}</h1>
        <p class="text-xs text-slate-500">Kelola periode penetapan harga acuan bapok pasar ini</p>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-5">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs sticky top-24">
            <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-calendar-plus text-emerald-600"></i>
                <span>Tambah Periode Acuan</span>
            </h3>

            <form action="/data/pasar/harga/{{ $pasar->id }}/tahunbulan" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Bulan</label>
                    <select name="bulan" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-700 focus:bg-white focus:border-emerald-600 outline-none transition">
                        <option value="">-- Pilih Bulan --</option>
                        @php
                            $months = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                        @endphp
                        @foreach ($months as $k => $m)
                            <option value="{{ $k }}" {{ date('m') == $k ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tahun</label>
                    <input type="number" name="tahun" value="{{ date('Y') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-700 focus:bg-white focus:border-emerald-600 outline-none transition">
                </div>

                <div class="pt-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                        <i class="fas fa-plus"></i>
                        <span>Buat Periode</span>
                    </button>
                    <a href="/data/pasar" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <div class="lg:col-span-7">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Periode Acuan Terdaftar</h3>
                    <p class="text-[11px] text-slate-400">Total: {{ $bulantahun->count() }} periode</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Bulan & Tahun</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $no = 1; @endphp
                        @forelse ($bulantahun as $item)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 text-center text-slate-400 font-medium">{{ $no++ }}</td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900 text-sm">
                                    {{ \Carbon\Carbon::createFromFormat('d/m','01/'.$item->bulan)->translatedFormat('F') }} {{ $item->tahun }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="/data/pasar/harga/{{ $pasar->id }}/acuan/{{ $item->id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white rounded-lg text-xs font-bold border border-emerald-200 transition">
                                        <i class="fas fa-tags text-[11px]"></i>
                                        <span>Set Harga Komoditi</span>
                                    </a>
                                    <a href="/data/pasar/harga/{{ $pasar->id }}/delete/{{ $item->id }}" onclick="return confirm('Hapus periode ini beserta data acuannya?')" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white inline-flex items-center justify-center border border-rose-200 transition" title="Hapus Periode">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-400">Belum ada periode acuan terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
