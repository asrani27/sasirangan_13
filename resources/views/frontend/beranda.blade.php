@extends('layouts.public')

@section('title', 'Beranda - DEDIKASI Pemantauan Pangan Banjarmasin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">

    <!-- Hero / Carousel Section -->
    <div class="relative rounded-3xl overflow-hidden bg-slate-900 shadow-xl border border-slate-800">
        @if(isset($slider) && count($slider) > 0)
            <div id="carousel-container" class="relative h-64 sm:h-96 md:h-[420px] overflow-hidden">
                @foreach($slider as $key => $item)
                    <div class="carousel-slide absolute inset-0 transition-opacity duration-700 ease-in-out {{ $key === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}" data-index="{{ $key }}">
                        <img src="{{ (!empty($item->file) && (Storage::disk('public')->exists($item->file) || file_exists(public_path('storage/' . $item->file)))) ? '/storage/' . $item->file : 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1600&q=80' }}"
                             onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1600&q=80';"
                             alt="{{ $item->judul }}"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-10 text-white">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500 text-white mb-2">Informasi Publik</span>
                            <h2 class="text-xl sm:text-3xl font-extrabold max-w-3xl leading-tight">{{ $item->judul }}</h2>
                        </div>
                    </div>
                @endforeach

                @if(count($slider) > 1)
                    <!-- Carousel Controls -->
                    <button type="button" id="prev-slide" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-black/40 hover:bg-black/70 text-white flex items-center justify-center backdrop-blur-xs transition">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" id="next-slide" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-black/40 hover:bg-black/70 text-white flex items-center justify-center backdrop-blur-xs transition">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <div class="absolute bottom-3 right-6 z-20 flex gap-1.5" id="carousel-dots">
                        @foreach($slider as $key => $item)
                            <button type="button" class="w-2.5 h-2.5 rounded-full {{ $key === 0 ? 'bg-emerald-400 w-6' : 'bg-white/50' }} transition-all" data-dot="{{ $key }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <!-- Hero Fallback Banner -->
            <div class="p-8 sm:p-14 bg-gradient-to-br from-emerald-800 via-teal-800 to-slate-900 text-white">
                <div class="max-w-3xl space-y-4">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-emerald-400 text-emerald-950">SISTEM RESMI PEMKOT BANJARMASIN</span>
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight">
                        Pantau Stabilitas Harga &amp; Ketersediaan Pangan
                    </h1>
                    <p class="text-emerald-100 text-base sm:text-lg">
                        Layanan transparansi harga harian bahan pokok di pasar-pasar tradisional Kota Banjarmasin. Terkoneksi langsung dengan sistem Early Warning System (DEDIKASI).
                    </p>
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Access Feature Tiles -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('info-harga') }}" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-emerald-400 hover:shadow-md transition group">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-4 group-hover:bg-emerald-600 group-hover:text-white transition">
                <i class="fas fa-tags"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1 group-hover:text-emerald-700 transition">Informasi Harga Harian</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Telusuri harga komoditi pangan harian per pasar tradisional secara transparan dan terukur di Banjarmasin.
            </p>
        </a>

        <a href="{{ route('grafik.harga') }}" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-sky-400 hover:shadow-md transition group">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl mb-4 group-hover:bg-sky-600 group-hover:text-white transition">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1 group-hover:text-sky-700 transition">Grafik Tren &amp; Riwayat</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Visualisasi interaktif pergerakan harga komoditi dari waktu ke waktu untuk analisis tren inflasi daerah.
            </p>
        </a>
    </div>

    <!-- Latest News / Articles Section -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Berita &amp; Publikasi Terbaru</h2>
                <p class="text-xs text-slate-500">Informasi stabilitas pasokan, operasi pasar, dan edukasi konsumen</p>
            </div>
            <a href="{{ route('artikel') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                <span>Semua Berita</span>
                <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($berita as $b)
                <article class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col hover:shadow-md transition group">
                    <div class="relative h-44 overflow-hidden bg-slate-100">
                        <img src="{{ (!empty($b->file) && (Storage::disk('public')->exists($b->file) || file_exists(public_path('storage/' . $b->file)))) ? '/storage/' . $b->file : 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=600&q=80' }}"
                             onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=600&q=80';"
                             alt="{{ $b->judul }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @if($b->kategori)
                            <span class="absolute top-3 left-3 bg-emerald-600/90 backdrop-blur-xs text-white text-[10px] font-bold px-2 py-0.5 rounded-md">
                                {{ $b->kategori->nama }}
                            </span>
                        @endif
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                        <div class="space-y-2">
                            <span class="text-[11px] font-semibold text-slate-400 block">
                                <i class="far fa-calendar text-[10px] mr-1"></i> {{ $b->created_at ? $b->created_at->format('d M Y') : '-' }}
                            </span>
                            <h3 class="font-bold text-slate-900 group-hover:text-emerald-700 line-clamp-2 leading-snug">
                                <a href="/artikel/{{ $b->id }}">{{ $b->judul }}</a>
                            </h3>
                            <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                {{ Str::limit(strip_tags($b->isi), 120) }}
                            </p>
                        </div>
                        <a href="/artikel/{{ $b->id }}" class="inline-flex items-center text-xs font-bold text-emerald-700 group-hover:text-emerald-800">
                            <span>Baca Selengkapnya</span>
                            <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400 bg-white rounded-2xl border border-dashed border-slate-200">
                    <i class="far fa-newspaper text-3xl mb-2 text-slate-300"></i>
                    <p class="text-sm">Belum ada artikel berita yang dipublikasikan.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('#carousel-dots button');
        const prevBtn = document.getElementById('prev-slide');
        const nextBtn = document.getElementById('next-slide');

        if (slides.length <= 1) return;

        let currentIndex = 0;
        let timer = null;

        function showSlide(index) {
            slides.forEach((s, i) => {
                if (i === index) {
                    s.classList.remove('opacity-0', 'z-0');
                    s.classList.add('opacity-100', 'z-10');
                } else {
                    s.classList.remove('opacity-100', 'z-10');
                    s.classList.add('opacity-0', 'z-0');
                }
            });

            dots.forEach((d, i) => {
                if (i === index) {
                    d.classList.add('bg-emerald-400', 'w-6');
                    d.classList.remove('bg-white/50');
                } else {
                    d.classList.remove('bg-emerald-400', 'w-6');
                    d.classList.add('bg-white/50');
                }
            });

            currentIndex = index;
        }

        function nextSlide() {
            showSlide((currentIndex + 1) % slides.length);
        }

        function prevSlide() {
            showSlide((currentIndex - 1 + slides.length) % slides.length);
        }

        if (nextBtn) nextBtn.addEventListener('click', () => { nextSlide(); resetTimer(); });
        if (prevBtn) prevBtn.addEventListener('click', () => { prevSlide(); resetTimer(); });

        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                const idx = parseInt(dot.getAttribute('data-dot'));
                showSlide(idx);
                resetTimer();
            });
        });

        function startTimer() {
            timer = setInterval(nextSlide, 5000);
        }

        function resetTimer() {
            clearInterval(timer);
            startTimer();
        }

        startTimer();
    });
</script>
@endpush
@endsection
