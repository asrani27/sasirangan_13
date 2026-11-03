@extends('layouts.public')

@section('title', $berita->judul . ' - DEDIKASI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6 font-medium">
        <a href="{{ route('beranda') }}" class="hover:text-emerald-600">Beranda</a>
        <span>/</span>
        <a href="{{ route('artikel') }}" class="hover:text-emerald-600">Artikel</a>
        <span>/</span>
        <span class="text-slate-800 truncate max-w-xs sm:max-w-md">{{ $berita->judul }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Article Content -->
        <main class="lg:col-span-8 bg-white rounded-3xl p-6 sm:p-10 border border-slate-200/90 shadow-xs space-y-6">
            <header class="space-y-4">
                @if($berita->kategori)
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        {{ $berita->kategori->nama }}
                    </span>
                @endif
                <h1 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight">
                    {{ $berita->judul }}
                </h1>
                <div class="flex items-center gap-4 text-xs text-slate-500 pb-4 border-b border-slate-100">
                    <span class="inline-flex items-center gap-1.5">
                        <i class="far fa-calendar"></i>
                        <span>{{ $berita->created_at ? $berita->created_at->format('l, d F Y') : '-' }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <i class="far fa-clock"></i>
                        <span>{{ $berita->created_at ? $berita->created_at->format('H:i') : '' }} WITA</span>
                    </span>
                </div>
            </header>

            <div class="rounded-2xl overflow-hidden bg-slate-100 max-h-[440px]">
                <img src="{{ (!empty($berita->file) && (Storage::disk('public')->exists($berita->file) || file_exists(public_path('storage/' . $berita->file)))) ? '/storage/' . $berita->file : 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1200&q=80' }}"
                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1200&q=80';"
                     alt="{{ $berita->judul }}"
                     class="w-full h-full object-cover">
            </div>

            <div class="text-slate-700 text-sm sm:text-base leading-relaxed space-y-4 max-w-none">
                {!! $berita->isi !!}
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('artikel') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-emerald-700 bg-slate-50 hover:bg-slate-100 px-4 py-2 rounded-xl transition">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Berita</span>
                </a>
            </div>
        </main>

        <!-- Sidebar / Recent Articles -->
        <aside class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-4">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i class="far fa-newspaper text-emerald-600"></i>
                    <span>Berita Terkait Lainnya</span>
                </h3>

                <div class="space-y-4">
                    @forelse($recentBerita as $r)
                        <a href="/artikel/{{ $r->id }}" class="flex gap-3 group">
                            <div class="w-16 h-16 rounded-xl overflow-hidden bg-slate-100 shrink-0">
                                <img src="{{ (!empty($r->file) && (Storage::disk('public')->exists($r->file) || file_exists(public_path('storage/' . $r->file)))) ? '/storage/' . $r->file : 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=200&q=80' }}"
                                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=200&q=80';"
                                     alt="{{ $r->judul }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition">
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] text-slate-400 font-medium block">
                                    {{ $r->created_at ? $r->created_at->format('d M Y') : '' }}
                                </span>
                                <h4 class="text-xs font-bold text-slate-800 group-hover:text-emerald-700 line-clamp-2 leading-snug">
                                    {{ $r->judul }}
                                </h4>
                            </div>
                        </a>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada berita lainnya.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
