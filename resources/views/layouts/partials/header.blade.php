<!-- Top Bar -->
<div class="bg-gradient-to-r from-emerald-800 via-teal-700 to-emerald-900 text-white text-xs font-medium py-1.5 px-4">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-1 sm:gap-4 text-center sm:text-left">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/30 text-emerald-200 border border-emerald-400/20">RESMI</span>
            <span>Pemerintah Kota Banjarmasin &bull; Dinas Perdagangan dan Perindustrian</span>
        </div>
        <div class="flex items-center gap-4 text-emerald-100">
            <span><i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
            @php
                $aduanNum = isset($aduan) && !empty($aduan->nomor) ? $aduan->nomor : (\App\Models\NomorAduan::first()->nomor ?? '');
                $isAdmin = auth()->check() && auth()->user()->isAdmin();
                $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin();
            @endphp
            @if(!empty($aduanNum))
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $aduanNum) }}" target="_blank" class="hover:text-white transition flex items-center gap-1">
                    <i class="fab fa-whatsapp text-emerald-300"></i> Aduan: {{ $aduanNum }}
                </a>
            @endif
        </div>
    </div>
</div>

<!-- Main Navigation Header -->
<header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <a href="{{ route('beranda') }}" class="flex items-center gap-3 group">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-800 text-white flex items-center justify-center font-bold text-2xl shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                    D
                </div>
                <div>
                    <div class="text-xl font-extrabold tracking-tight text-slate-900 flex items-center gap-1.5">
                        <span>DEDIKASI</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Banjarmasin</span>
                    </div>
                    <div class="text-xs text-slate-500 font-medium">Sistem Pantau Harga &amp; Stok Bahan Pokok</div>
                </div>
            </a>

            <!-- Desktop Menu -->
            <nav class="hidden lg:flex items-center gap-1 xl:gap-2 text-sm font-medium">
                <a href="{{ route('beranda') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('beranda') ? 'text-emerald-700 bg-emerald-50 font-semibold' : 'text-slate-700 hover:text-emerald-600 hover:bg-slate-100' }}">Beranda</a>
                <a href="{{ route('info-harga') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('info-harga*') ? 'text-emerald-700 bg-emerald-50 font-semibold' : 'text-slate-700 hover:text-emerald-600 hover:bg-slate-100' }}">Info Harga</a>
                @if($isAdmin)
                    <a href="{{ route('info-stok') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('info-stok*') ? 'text-emerald-700 bg-emerald-50 font-semibold' : 'text-slate-700 hover:text-emerald-600 hover:bg-slate-100' }}">Info Stok</a>
                @endif
                
                <div class="relative group">
                    <button type="button" class="px-3 py-2 rounded-lg transition flex items-center gap-1 {{ request()->routeIs('grafik*') ? 'text-emerald-700 bg-emerald-50 font-semibold' : 'text-slate-700 hover:text-emerald-600 hover:bg-slate-100' }}">
                        <span>Grafik</span>
                        <i class="fas fa-chevron-down text-xs text-slate-400 group-hover:text-emerald-600 transition-transform group-hover:rotate-180"></i>
                    </button>
                    <div class="absolute left-0 mt-1 w-48 rounded-xl bg-white shadow-xl border border-slate-100 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="{{ route('grafik.harga') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 font-medium"><i class="fas fa-chart-line text-emerald-600 mr-2 w-4"></i> Grafik Harga</a>
                        @if($isAdmin)
                            <a href="{{ route('grafik.stok') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 font-medium"><i class="fas fa-chart-bar text-teal-600 mr-2 w-4"></i> Grafik Stok</a>
                        @endif
                    </div>
                </div>

                @if($isSuperAdmin)
                    <a href="{{ route('kenaikan') }}" class="px-3 py-2 rounded-lg transition flex items-center gap-1.5 {{ request()->routeIs('kenaikan*') ? 'text-rose-700 bg-rose-50 font-semibold' : 'text-slate-700 hover:text-rose-600 hover:bg-slate-100' }}">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        <span>EWS / Kenaikan</span>
                    </a>
                @endif
                <a href="{{ route('artikel') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('artikel*') ? 'text-emerald-700 bg-emerald-50 font-semibold' : 'text-slate-700 hover:text-emerald-600 hover:bg-slate-100' }}">Berita</a>
            </nav>

            <div class="hidden lg:flex items-center gap-3">
                @auth
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs transition hover:shadow-md">
                        <i class="fas fa-gauge"></i><span>Dashboard Admin</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl transition">
                        <i class="fas fa-arrow-right-to-bracket text-emerald-600"></i><span>Masuk Petugas</span>
                    </a>
                @endauth
            </div>

            <div class="lg:hidden flex items-center">
                <button type="button" id="mobile-menu-btn" class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100" aria-label="Toggle menu">
                    <i class="fas fa-bars text-xl" id="menu-icon-open"></i>
                    <i class="fas fa-times text-xl hidden" id="menu-icon-close"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer -->
    <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-1">
        <a href="{{ route('beranda') }}" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('beranda') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}"><i class="fas fa-house mr-2 w-5 text-emerald-600"></i> Beranda</a>
        <a href="{{ route('info-harga') }}" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('info-harga*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}"><i class="fas fa-tag mr-2 w-5 text-emerald-600"></i> Info Harga</a>
        @if($isAdmin)
            <a href="{{ route('info-stok') }}" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('info-stok*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}"><i class="fas fa-boxes-stacked mr-2 w-5 text-emerald-600"></i> Info Stok</a>
        @endif
        <a href="{{ route('grafik.harga') }}" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('grafik.harga*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}"><i class="fas fa-chart-line mr-2 w-5 text-emerald-600"></i> Grafik Harga</a>
        @if($isAdmin)
            <a href="{{ route('grafik.stok') }}" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('grafik.stok*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}"><i class="fas fa-chart-bar mr-2 w-5 text-teal-600"></i> Grafik Stok</a>
        @endif
        @if($isSuperAdmin)
            <a href="{{ route('kenaikan') }}" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('kenaikan*') ? 'bg-rose-50 text-rose-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}"><i class="fas fa-triangle-exclamation mr-2 w-5 text-rose-600"></i> Peringatan EWS</a>
        @endif
        <a href="{{ route('artikel') }}" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('artikel*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}"><i class="fas fa-newspaper mr-2 w-5 text-emerald-600"></i> Berita &amp; Artikel</a>
        <div class="pt-3 border-t border-slate-100">
            @auth
                <a href="{{ route('home') }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white font-semibold"><i class="fas fa-gauge"></i> Masuk Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 text-slate-800 font-semibold hover:bg-slate-200"><i class="fas fa-arrow-right-to-bracket text-emerald-600"></i> Masuk Petugas</a>
            @endauth
        </div>
    </div>
</header>
