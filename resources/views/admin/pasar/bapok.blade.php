@extends('layouts.admin')

@section('title')
<div class="flex items-center gap-3">
    <a href="/data/pasar" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Komoditi Pasar: {{ $data->nama }}</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tentukan komoditi bahan pokok yang aktif disurvei di pasar ini</p>
    </div>
</div>
@endsection

@section('content')
<form action="/data/pasar/bapok/{{ $data->id }}" method="POST">
    @csrf
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Pilih Komoditi</h3>
                <p class="text-xs text-slate-400">Centang bahan pokok yang dipantau di {{ $data->nama }}</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="btnSelectAll" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">Pilih Semua</button>
                <button type="button" id="btnUnselectAll" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">Batal Semua</button>
                <div class="relative w-48">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="bapokSearch" placeholder="Filter..." class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:bg-white focus:border-emerald-600 outline-none">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 max-h-[60vh] overflow-y-auto pr-1">
            @foreach ($bapok as $item)
            @php $isChecked = in_array($item->id, $bahanku); @endphp
            <label class="bapok-item flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200/80 hover:border-emerald-300 hover:bg-emerald-50/30 cursor-pointer transition select-none {{ $isChecked ? 'bg-emerald-50/40 border-emerald-300' : 'bg-slate-50/50' }}">
                <input type="checkbox" name="bahan_id[]" value="{{ $item->id }}" {{ $isChecked ? 'checked' : '' }} class="bapok-checkbox w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                <div class="min-w-0">
                    <span class="block text-xs font-bold text-slate-800 truncate bapok-name">{{ $item->nama }}</span>
                    <span class="block text-[10px] text-slate-400 font-medium">{{ $item->satuan ? $item->satuan->nama : 'N/A' }}</span>
                </div>
            </label>
            @endforeach
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs text-slate-400">Total komoditi: {{ $bapok->count() }} item</span>
            <div class="flex items-center gap-2">
                <a href="/data/pasar" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                    <i class="fas fa-save"></i>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>
        </div>
    </div>
</form>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const checkboxes = document.querySelectorAll('.bapok-checkbox');
        const items = document.querySelectorAll('.bapok-item');
        const search = document.getElementById('bapokSearch');

        document.getElementById('btnSelectAll')?.addEventListener('click', () => {
            checkboxes.forEach(cb => {
                if (cb.closest('.bapok-item').style.display !== 'none') {
                    cb.checked = true;
                }
            });
        });

        document.getElementById('btnUnselectAll')?.addEventListener('click', () => {
            checkboxes.forEach(cb => {
                if (cb.closest('.bapok-item').style.display !== 'none') {
                    cb.checked = false;
                }
            });
        });

        search?.addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase().trim();
            items.forEach(item => {
                const name = item.querySelector('.bapok-name')?.textContent.toLowerCase() || '';
                item.style.display = name.includes(val) ? '' : 'none';
            });
        });
    });
</script>
@endpush
@endsection
