@extends('layouts.admin')

@section('title')
<div class="flex items-center gap-3">
    <a href="/input/stok" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-xl font-black text-slate-900">Stok Bahan di {{ $pasar->nama }}</h1>
        <p class="text-xs text-slate-500">Periode {{ sprintf('%02d', $month) }} / {{ $year }}</p>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
        <form method="get" action="/input/stok/month/{{ $id }}" class="flex flex-wrap items-center gap-2">
            @csrf
            <span class="text-xs font-bold text-slate-500">Periode:</span>
            <select name="bulan" required class="bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-700">
                @for ($m=1; $m<=12; $m++)
                    @php $k = sprintf('%02d', $m); @endphp
                    <option value="{{ $k }}" {{ (string)$month === (string)$k || (string)$month === (string)$m ? 'selected' : '' }}>Bulan {{ $k }}</option>
                @endfor
            </select>
            <select name="tahun" required class="bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-700">
                @foreach (['2023','2024','2025','2026','2027'] as $y)
                    <option value="{{ $y }}" {{ (string)$year === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            <a href="/input/stok/{{ $id }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold">Reset</a>
        </form>
        <span class="text-[11px] text-slate-400"><i class="fas fa-info-circle text-emerald-600 mr-1"></i> Klik angka untuk edit</span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3 text-center w-10">No</th>
                        <th class="py-3 px-4 min-w-[150px]">Nama Komoditi</th>
                        <th class="py-3 px-3 text-center w-20">Satuan</th>
                        <th class="py-3 px-3 text-center">Minggu I</th>
                        <th class="py-3 px-3 text-center">Minggu II</th>
                        <th class="py-3 px-3 text-center">Minggu III</th>
                        <th class="py-3 px-3 text-center">Minggu IV</th>
                        @if ($week == 5)
                        <th class="py-3 px-3 text-center">Minggu V</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $no = 1; @endphp
                    @forelse ($data as $b)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-2.5 px-3 text-center text-slate-400 font-medium">{{ $no++ }}</td>
                        <td class="py-2.5 px-4 font-semibold text-slate-900 whitespace-nowrap">{{ $b->nama }}</td>
                        <td class="py-2.5 px-3 text-center text-slate-500 font-medium">{{ $b->satuan ? $b->satuan->nama : '-' }}</td>
                        @for ($m = 1; $m <= ($week == 5 ? 5 : 4); $m++)
                        @php $prop = 'minggu_' . $m; $val = $b->$prop ?? 0; @endphp
                        <td class="py-2.5 px-3 text-center">
                            <button type="button" class="editable-stok group inline-flex items-center justify-center gap-1 min-w-[45px] px-2 py-1 rounded-lg hover:bg-emerald-50 text-slate-800 font-mono font-bold text-xs border border-transparent hover:border-emerald-300 transition"
                                data-pasar-id="{{ $id }}"
                                data-pk="{{ $b->id }}"
                                data-minggu="{{ $m }}"
                                data-bulan="{{ sprintf('%02d', $month) }}"
                                data-tahun="{{ $year }}"
                                data-value="{{ $val }}">
                                <span>{{ $val > 0 ? number_format((float)$val, 0, ',', '.') : '-' }}</span>
                                <i class="fas fa-pencil text-[8px] text-slate-300 group-hover:text-emerald-600"></i>
                            </button>
                        </td>
                        @endfor
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $week == 5 ? 8 : 7 }}" class="py-8 text-center text-slate-400">Tidak ada komoditi terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        document.querySelectorAll('.editable-stok').forEach(btn => {
            btn.addEventListener('click', async () => {
                const currentVal = btn.dataset.value || '0';
                const newVal = prompt('Masukkan volume stok (angka):', currentVal);
                if (newVal === null) return;
                const cleanVal = newVal.replace(/[^0-9]/g, '');
                if (cleanVal === '') return;

                const origHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i>';
                btn.disabled = true;

                try {
                    const fd = new FormData();
                    fd.append('_token', token);
                    fd.append('pasar_id', btn.dataset.pasarId);
                    fd.append('pk', btn.dataset.pk);
                    fd.append('minggu', btn.dataset.minggu);
                    fd.append('bulan', btn.dataset.bulan);
                    fd.append('tahun', btn.dataset.tahun);
                    fd.append('value', cleanVal);

                    const res = await fetch('/input/stok/update', {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (res.ok) {
                        btn.dataset.value = cleanVal;
                        const formatted = cleanVal > 0 ? new Intl.NumberFormat('id-ID').format(cleanVal) : '-';
                        btn.innerHTML = `<span>${formatted}</span><i class="fas fa-check text-[8px] text-emerald-600"></i>`;
                        btn.classList.add('bg-emerald-100');
                        setTimeout(() => btn.classList.remove('bg-emerald-100'), 800);
                    } else {
                        alert('Gagal memperbarui stok.');
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
