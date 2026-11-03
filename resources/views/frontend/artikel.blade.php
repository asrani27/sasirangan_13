@extends('layouts.public')

@section('title', 'Artikel & Berita - DEDIKASI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <div class="space-y-1">
        <div class="flex items-center gap-2 text-xs font-semibold text-emerald-700 uppercase tracking-wider">
            <i class="far fa-newspaper"></i>
            <span>Publikasi &amp; Edukasi</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Kabar &amp; Informasi Pangan</h1>
        <p class="text-sm text-slate-500">Berita operasi pasar, pemantauan stabilitas pasokan, dan himbauan pemerintah Kota Banjarmasin.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($berita as $item)
            <article class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col hover:shadow-md transition group">
                <div class="relative h-48 bg-slate-100 overflow-hidden">
                    <img src="{{ (!empty($item->file) && (Storage::disk('public')->exists($item->file) || file_exists(public_path('storage/' . $item->file)))) ? '/storage/' . $item->file : 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80' }}"
                         onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80';"
                         alt="{{ $item->judul }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @if($item->kategori)
                        <span class="absolute top-3 left-3 bg-emerald-600/95 backdrop-blur-xs text-white text-xs font-bold px-3 py-1 rounded-lg shadow-xs">
                            {{ $item->kategori->nama }}
                        </span>
                    @endif
                </div>

                <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-xs text-slate-400">
                            <i class="far fa-calendar text-[11px]"></i>
                            <span>{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</span>
                        </div>
                        <h2 class="text-base font-bold text-slate-900 group-hover:text-emerald-700 line-clamp-2 leading-snug">
                            <a href="/artikel/{{ $item->id }}">{{ $item->judul }}</a>
                        </h2>
                        <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                            {{ Str::limit(strip_tags($item->isi), 140) }}
                        </p>
                    </div>

                    <a href="/artikel/{{ $item->id }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:text-emerald-800 self-start">
                        <span>Baca Selengkapnya</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </article>
        @empty
            <div class="col-span-full bg-white rounded-3xl p-16 text-center border border-dashed border-slate-200 space-y-2">
                <i class="far fa-newspaper text-4xl text-slate-300"></i>
                <h3 class="text-base font-bold text-slate-700">Belum Ada Artikel</h3>
                <p class="text-xs text-slate-400">Belum ada publikasi artikel atau berita saat ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4">
        {{ $berita->links() }}
    </div>
</div>
@endsection
