@extends('layouts.admin')

@section('title')
<div>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Master Komoditi Bahan Pokok</h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola daftar komoditi pangan yang dipantau di pasar-pasar Kota Banjarmasin</p>
</div>
@endsection

@section('content')
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <a href="/data/bahan/add" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition">
            <i class="fas fa-plus"></i>
            <span>Tambah Komoditi</span>
        </a>
        <div class="relative w-full sm:w-64">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="bahanFilter" placeholder="Cari nama komoditi..." class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:bg-white focus:border-emerald-600 outline-none">
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600" id="bahanTable">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Komoditi</th>
                        <th class="py-3 px-4">Satuan</th>
                        <th class="py-3 px-4">Kelompok</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $no = $data->firstItem() ?? 1; @endphp
                    @forelse ($data as $item)
                    <tr class="hover:bg-slate-50/70 bahan-row">
                        <td class="py-3 px-4 text-center text-slate-400 font-medium">{{ $no++ }}</td>
                        <td class="py-3 px-4 font-bold text-slate-900 text-sm bahan-nama">{{ $item->nama }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                {{ $item->satuan ? $item->satuan->nama : '-' }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 font-semibold text-[11px] border border-emerald-200/60">
                                {{ $item->kelompok ? $item->kelompok->nama : '-' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="/data/bahan/edit/{{ $item->id }}" class="w-7 h-7 rounded-lg bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white inline-flex items-center justify-center border border-emerald-200 transition">
                                    <i class="fas fa-pencil text-xs"></i>
                                </a>
                                <a href="/data/bahan/delete/{{ $item->id }}" onclick="return confirm('Riwayat stok dan harga komoditi ini akan ikut terhapus. Lanjutkan?')" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white inline-flex items-center justify-center border border-rose-200 transition">
                                    <i class="fas fa-trash-can text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400">Belum ada data komoditi bahan pokok.</td>
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

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('bahanFilter');
        const rows = document.querySelectorAll('.bahan-row');
        if (input) {
            input.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase().trim();
                rows.forEach(r => {
                    const text = r.querySelector('.bahan-nama')?.textContent.toLowerCase() || '';
                    r.style.display = text.includes(term) ? '' : 'none';
                });
            });
        }
    });
</script>
@endpush
@endsection
