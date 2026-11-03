@extends('layouts.admin')

@section('title')
<div class="flex items-center gap-3">
    <a href="{{ $fulldate ? '/input/harga/' . $pasar->id : '/input/harga' }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-xl font-black text-slate-900">Harga di {{ $pasar->nama }}</h1>
        <p class="text-xs text-slate-500">{{ $fulldate ? 'Matriks: ' . $start->translatedFormat('F Y') : 'Harian: ' . $month->translatedFormat('d M Y') }}</p>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Filter -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
        <form method="get" action="/input/harga/fulldate/{{ $pasar->id }}" class="flex flex-wrap items-center gap-2">
            @csrf
            <span class="text-xs font-bold text-slate-500">Periode:</span>
            <select name="bulan" required class="bg-slate-50 border border-slate-200 rounded-lg px-2 py-1 text-xs text-slate-700">
                @for ($m=1; $m<=12; $m++)
                    @php $k = sprintf('%02d', $m); @endphp
                    <option value="{{ $k }}" {{ (isset($bulan) && $bulan == $k) || (!$fulldate && date('m') == $k) ? 'selected' : '' }}>Bulan {{ $k }}</option>
                @endfor
            </select>
            <select name="tahun" required class="bg-slate-50 border border-slate-200 rounded-lg px-2 py-1 text-xs text-slate-700">
                @foreach (['2023','2024','2025','2026','2027'] as $y)
                    <option value="{{ $y }}" {{ (isset($tahun) && $tahun == $y) || (!$fulldate && date('Y') == $y) ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            @if($fulldate)
            <a href="/input/harga/{{ $pasar->id }}" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold">Reset</a>
            @endif
        </form>
        <span class="text-[11px] text-slate-400"><i class="fas fa-info-circle text-emerald-600 mr-1"></i> Klik harga untuk edit</span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            @if (!$fulldate)
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Komoditi</th>
                        <th class="py-3 px-4 text-right">Harga ({{ $month->translatedFormat('d M Y') }})</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $no = 1; @endphp
                    @forelse ($data as $b)
                    <tr class="hover:bg-slate-50/70">
                        <td class="py-2.5 px-4 text-center text-slate-400 text-xs">{{ $no++ }}</td>
                        <td class="py-2.5 px-4 font-semibold text-slate-900">{{ $b->nama }}</td>
                        <td class="py-2.5 px-4 text-right">
                            <button type="button" class="editable-cell inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-900 font-mono font-bold text-xs border border-emerald-200/60"
                                data-pasar="{{ $pasar->id }}" data-pk="{{ $b->id }}" data-tanggal="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" data-value="{{ $b->harga }}">
                                <span>Rp {{ number_format((float) $b->harga, 0, ',', '.') }}</span>
                                <i class="fas fa-pencil text-[9px] text-emerald-500"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="py-8 text-center text-slate-400">Tidak ada komoditi.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @else
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-2 px-2 text-center w-8">No</th>
                        <th class="py-2 px-3 min-w-[140px]">Komoditi</th>
                        @foreach ($date as $d)
                        <th class="py-2 px-1 text-center min-w-[40px] {{ $d->isToday() ? 'bg-emerald-100 font-black' : '' }}">{{ $d->format('d') }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $no = 1; @endphp
                    @foreach ($data as $b)
                    <tr class="hover:bg-slate-50/70">
                        <td class="py-2 px-2 text-center text-slate-400">{{ $no++ }}</td>
                        <td class="py-2 px-3 font-semibold text-slate-900 whitespace-nowrap">{{ $b->nama }}</td>
                        @foreach ($b->tanggal as $key => $s)
                        <td class="py-2 px-1 text-center">
                            <button type="button" class="editable-cell hover:bg-emerald-100 px-1 py-0.5 rounded font-mono text-[11px] text-slate-700"
                                data-pasar="{{ $pasar->id }}" data-pk="{{ $b->id }}" data-tanggal="{{ sprintf('%s-%s-%02d', $tahun, $bulan, $key + 1) }}" data-value="{{ $s }}">
                                {{ $s > 0 ? number_format((float) $s, 0, ',', '.') : '-' }}
                            </button>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@push('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        document.querySelectorAll('.editable-cell').forEach(btn => {
            btn.addEventListener('click', async () => {
                const currentVal = btn.dataset.value || '0';
                const newVal = prompt('Masukkan harga baru (angka):', currentVal);
                if (newVal === null) return;
                const cleanVal = newVal.replace(/[^0-9]/g, '');
                if (cleanVal === '') return;

                const origHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;

                try {
                    const fd = new FormData();
                    fd.append('_token', token);
                    fd.append('pasar', btn.dataset.pasar);
                    fd.append('pk', btn.dataset.pk);
                    fd.append('tanggal', btn.dataset.tanggal);
                    fd.append('value', cleanVal);

                    const res = await fetch('/input/harga/update', {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (res.ok) {
                        btn.dataset.value = cleanVal;
                        const formatted = new Intl.NumberFormat('id-ID').format(cleanVal);
                        btn.innerHTML = btn.closest('table').classList.contains('text-sm') 
                            ? `<span>Rp ${formatted}</span><i class="fas fa-check text-[9px] text-emerald-600"></i>` 
                            : formatted;
                        btn.classList.add('bg-emerald-200');
                        setTimeout(() => btn.classList.remove('bg-emerald-200'), 800);
                    } else {
                        alert('Gagal update harga');
                        btn.innerHTML = origHtml;
                    }
                } catch(e) {
                    alert('Gagal menyimpan.');
                    btn.innerHTML = origHtml;
                } finally {
                    btn.disabled = false;
                }
            });
        });
    });
</script>
@endpush

@endsection
