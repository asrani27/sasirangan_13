@extends('layouts.admin')

@section('title_text', 'Riwayat Perubahan Harga')

@section('title')
<div>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Riwayat Perubahan Harga</h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Catatan fluktuasi naik dan turunnya harga komoditi berdasarkan bahan dan pasar</p>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('report.riwayat') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    <i class="fas fa-carrot text-orange-500 mr-1"></i> Komoditi Bahan
                </label>
                <select name="bahan_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 outline-none focus:bg-white focus:border-emerald-600">
                    <option value="">-- Pilih Komoditi --</option>
                    @foreach ($bahan as $b)
                        <option value="{{ $b->id }}" {{ (string)$bahan_id === (string)$b->id ? 'selected' : '' }}>
                            {{ $b->nama }} {{ $b->satuan ? '(' . $b->satuan->nama . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    <i class="fas fa-shop text-amber-500 mr-1"></i> Titik Pasar
                </label>
                <select name="pasar_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 outline-none focus:bg-white focus:border-emerald-600">
                    <option value="">-- Pilih Pasar --</option>
                    @foreach ($pasar as $p)
                        <option value="{{ $p->id }}" {{ (string)$pasar_id === (string)$p->id ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    <i class="fas fa-calendar-alt text-sky-500 mr-1"></i> Tanggal Mulai
                </label>
                <input type="date" name="start_date" value="{{ $startDate }}" required
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 outline-none focus:bg-white focus:border-emerald-600">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    <i class="fas fa-calendar-check text-emerald-500 mr-1"></i> Tanggal Akhir
                </label>
                <input type="date" name="end_date" value="{{ $endDate }}" required
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 outline-none focus:bg-white focus:border-emerald-600">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                    <i class="fas fa-filter text-xs"></i>
                    <span>Tampilkan</span>
                </button>
                <a href="{{ route('report.riwayat') }}" title="Reset ke default (1 Jan 2026 - Sekarang)"
                    class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition">
                    <i class="fas fa-rotate-right"></i>
                </a>
            </div>
        </form>
    </div>

    @if ($selectedBahan && $selectedPasar)
    <!-- Information & Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-tag"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[11px] font-bold uppercase text-slate-400 block truncate">Bahan &amp; Pasar</span>
                <div class="text-sm font-black text-slate-900 truncate">{{ $selectedBahan->nama }}</div>
                <div class="text-xs text-slate-500 truncate">{{ $selectedPasar->nama }} ({{ $selectedBahan->satuan->nama ?? 'Satuan' }})</div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[11px] font-bold uppercase text-slate-400 block truncate">Data Awal ({{ $dataAwal ? \Carbon\Carbon::parse($dataAwal['tanggal'])->translatedFormat('d M Y') : '-' }})</span>
                <div class="text-base font-black text-slate-900 font-mono">
                    {{ $dataAwal ? 'Rp ' . number_format($dataAwal['harga'], 0, ',', '.') : '-' }}
                </div>
                <div class="text-[11px] text-slate-500">Patokan awal periode</div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-coins"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[11px] font-bold uppercase text-slate-400 block truncate">Harga Terkini ({{ $dataTerkini ? \Carbon\Carbon::parse($dataTerkini['tanggal'])->translatedFormat('d M Y') : '-' }})</span>
                <div class="text-base font-black text-slate-900 font-mono">
                    {{ $dataTerkini ? 'Rp ' . number_format($dataTerkini['harga'], 0, ',', '.') : '-' }}
                </div>
                <div class="text-[11px] text-slate-500">Harga aktif paling mutakhir</div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[11px] font-bold uppercase text-slate-400 block truncate">Frekuensi Perubahan</span>
                <div class="text-base font-black text-slate-900">
                    {{ count($riwayat) > 1 ? (count($riwayat) - 1) . ' Kali Berubah' : 'Harga Tetap' }}
                </div>
                <div class="text-[11px] font-semibold flex items-center gap-2 mt-0.5">
                    <span class="text-rose-600"><i class="fas fa-arrow-up text-[10px]"></i> {{ $totalNaik }} Naik</span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-emerald-600"><i class="fas fa-arrow-down text-[10px]"></i> {{ $totalTurun }} Turun</span>
                </div>
            </div>
        </div>
    </div>

    @if (count($riwayat) > 0)
    <!-- Highlighted Log Card Sesuai Format Contoh Prompt -->
    <div class="bg-slate-900 rounded-2xl border border-slate-800 shadow-md p-5 text-slate-100">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <h3 class="text-sm font-bold text-white tracking-wide">Ringkasan Riwayat Perubahan Harga</h3>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">
                    Komoditi <b class="text-emerald-400">{{ $selectedBahan->nama }}</b> di <b class="text-emerald-400">{{ $selectedPasar->nama }}</b>
                    &bull; Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="salinFormatTeks()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white rounded-xl text-xs font-semibold transition border border-slate-700 shadow-xs">
                    <i class="fas fa-copy text-emerald-400 text-xs"></i>
                    <span id="btnSalinText">Salin Format Teks</span>
                </button>
                <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white rounded-xl text-xs font-semibold transition border border-slate-700 shadow-xs">
                    <i class="fas fa-print text-sky-400 text-xs"></i>
                    <span>Cetak</span>
                </button>
            </div>
        </div>

        <!-- Log Content View -->
        <div class="mt-4 font-mono text-xs sm:text-sm bg-slate-950/70 p-4 rounded-xl border border-slate-800/80 space-y-2">
            @php
                $textLines = [];
            @endphp
            @foreach ($riwayat as $item)
                @php
                    $tglFormatted = strtolower(\Carbon\Carbon::parse($item['tanggal'])->translatedFormat('j M Y'));
                    $hargaFormatted = number_format($item['harga'], 0, ',', '.');
                    $note = '';

                    if ($item['status'] === 'up') {
                        $note = ' (up ' . number_format($item['selisih'], 0, ',', '.') . ')';
                    } elseif ($item['status'] === 'down') {
                        $note = ' (down ' . number_format(abs($item['selisih']), 0, ',', '.') . ')';
                    }

                    $line = "tanggal {$tglFormatted} : {$hargaFormatted}{$note}";
                    $textLines[] = $line;
                @endphp
                <div class="flex items-center justify-between py-1 border-b border-slate-900/80 last:border-0 hover:bg-slate-800/40 px-2 rounded transition">
                    <div class="flex items-center gap-2">
                        <span class="text-slate-400">tanggal <span class="text-slate-200 font-bold">{{ $tglFormatted }}</span></span>
                        <span class="text-slate-500">:</span>
                        <span class="text-amber-300 font-bold">{{ $hargaFormatted }}</span>
                        @if ($item['status'] === 'up')
                            <span class="text-rose-400 font-semibold">(up {{ number_format($item['selisih'], 0, ',', '.') }})</span>
                        @elseif ($item['status'] === 'down')
                            <span class="text-emerald-400 font-semibold">(down {{ number_format(abs($item['selisih']), 0, ',', '.') }})</span>
                        @else
                            <span class="text-sky-400/90 text-xs">(data awal)</span>
                        @endif
                    </div>
                    <div class="text-[11px] text-slate-500 hidden sm:block">
                        @if ($item['status'] === 'up')
                            <span class="text-rose-400/80 font-sans"><i class="fas fa-arrow-trend-up mr-1"></i>+{{ number_format($item['persen'], 2) }}%</span>
                        @elseif ($item['status'] === 'down')
                            <span class="text-emerald-400/80 font-sans"><i class="fas fa-arrow-trend-down mr-1"></i>{{ number_format($item['persen'], 2) }}%</span>
                        @else
                            <span class="text-slate-500 font-sans">Patokan</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <textarea id="rawTextContent" class="hidden">{{ implode("\n", $textLines) }}</textarea>
    </div>

    <!-- Data Table Breakdown -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Tabel Rincian Riwayat Perubahan</h3>
                <p class="text-[11px] text-slate-400">Hanya menampilkan tanggal saat terjadi perubahan harga &amp; data awal patokan</p>
            </div>
            <div class="text-xs text-slate-500">
                Total baris data: <span class="font-bold text-slate-800">{{ count($riwayat) }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3 w-12 text-center">No</th>
                        <th class="py-3 px-4 min-w-[160px]">Tanggal</th>
                        <th class="py-3 px-4 text-right">Harga Baru</th>
                        <th class="py-3 px-4 text-right">Harga Sebelumnya</th>
                        <th class="py-3 px-4 text-center">Selisih &amp; Arah</th>
                        <th class="py-3 px-4 text-center">Persentase</th>
                        <th class="py-3 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $no = 1; @endphp
                    @foreach ($riwayat as $row)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3 px-3 text-center text-slate-400 font-medium">{{ $no++ }}</td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900">
                                {{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('l, d F Y') }}
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono">
                                {{ $row['tanggal'] }}
                            </div>
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 text-xs">
                            Rp {{ number_format($row['harga'], 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-slate-500 text-xs">
                            @if ($row['prev_harga'] !== null)
                                Rp {{ number_format($row['prev_harga'], 0, ',', '.') }}
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if ($row['status'] === 'up')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200 font-mono">
                                    <i class="fas fa-arrow-up text-[10px]"></i>
                                    <span>up {{ number_format($row['selisih'], 0, ',', '.') }}</span>
                                </span>
                            @elseif ($row['status'] === 'down')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono">
                                    <i class="fas fa-arrow-down text-[10px]"></i>
                                    <span>down {{ number_format(abs($row['selisih']), 0, ',', '.') }}</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                    <i class="fas fa-flag text-[10px]"></i>
                                    <span>Data Awal</span>
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-[11px] font-mono">
                            @if ($row['status'] === 'up')
                                <span class="text-rose-600">+{{ number_format($row['persen'], 2) }}%</span>
                            @elseif ($row['status'] === 'down')
                                <span class="text-emerald-600">{{ number_format($row['persen'], 2) }}%</span>
                            @else
                                <span class="text-slate-400">0%</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-slate-600 text-xs">
                            @if ($row['status'] === 'up')
                                Naik <b class="text-rose-600">Rp {{ number_format($row['selisih'], 0, ',', '.') }}</b> dibanding hari aktif sebelumnya
                            @elseif ($row['status'] === 'down')
                                Turun <b class="text-emerald-600">Rp {{ number_format(abs($row['selisih']), 0, ',', '.') }}</b> dibanding hari aktif sebelumnya
                            @else
                                <span class="text-slate-500">Harga patokan awal periode yang dipilih</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-2xl mb-3">
            <i class="fas fa-box-open"></i>
        </div>
        <h3 class="text-sm font-bold text-slate-800">Tidak Ada Data Riwayat Harga</h3>
        <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
            Belum ada catatan harga untuk komoditi <b>{{ $selectedBahan->nama }}</b> di <b>{{ $selectedPasar->nama }}</b> pada rentang tanggal yang dipilih.
        </p>
    </div>
    @endif
    @endif
</div>
@endsection

@push('js')
<script>
    function salinFormatTeks() {
        const textElement = document.getElementById('rawTextContent');
        if (!textElement) return;

        navigator.clipboard.writeText(textElement.value).then(() => {
            const btn = document.getElementById('btnSalinText');
            if (btn) {
                const originalText = btn.innerText;
                btn.innerText = 'Tersalin!';
                setTimeout(() => {
                    btn.innerText = originalText;
                }, 2000);
            }
        }).catch(err => {
            alert('Gagal menyalin teks: ' + err);
        });
    }
</script>
@endpush
