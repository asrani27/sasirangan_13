@extends('layouts.admin')

@section('title')
<div>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Master Harga Acuan Pangan</h1>
    <p class="text-xs text-slate-500 mt-0.5">Atur harga acuan dasar bahan pokok bulanan untuk indikator warning system</p>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Tambah Periode -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs sticky top-24">
            <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-calendar-plus text-emerald-600"></i>
                <span>Tambah Periode Acuan</span>
            </h3>

            <form action="/data/hargaacuan/add" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bulan</label>
                    <select name="bulan" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 outline-none">
                        <option value="">- Pilih Bulan -</option>
                        @foreach (['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $k => $m)
                            <option value="{{ $k }}" {{ date('m') == $k ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tahun</label>
                    <input type="number" name="tahun" value="{{ date('Y') }}" min="2020" max="2035" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 outline-none">
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                    <i class="fas fa-plus"></i>
                    <span>Simpan Periode</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Tabel Daftar Periode -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Riwayat Periode Acuan</h3>
                <span class="text-xs font-semibold text-slate-400">Total: {{ $data->total() }}</span>
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
                        @php $no = $data->firstItem() ?? 1; @endphp
                        @forelse ($data as $item)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3 px-4 text-center text-slate-400 font-medium">{{ $no++ }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900 text-sm">
                                {{ \Carbon\Carbon::createFromDate($item->tahun, (int)$item->bulan, 1)->translatedFormat('F Y') }}
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="/data/hargaacuan/bapok/{{ $item->id }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white rounded-lg font-bold text-xs border border-emerald-200 transition">
                                        <i class="fas fa-list-check"></i>
                                        <span>Atur Acuan</span>
                                    </a>
                                    <a href="/data/hargaacuan/delete/{{ $item->id }}" onclick="return confirm('Hapus data acuan ini?')" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white inline-flex items-center justify-center border border-rose-200 transition">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-400">Belum ada periode acuan.</td>
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
