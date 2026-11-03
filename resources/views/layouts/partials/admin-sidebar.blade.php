<!-- Mobile Sidebar Backdrop -->
<div id="admin-sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs hidden lg:hidden"></div>

<!-- Sidebar Navigation -->
<aside id="admin-sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 flex flex-col transition-transform duration-300 transform -translate-x-full lg:translate-x-0 border-r border-slate-800 shadow-xl lg:shadow-none">
    <!-- Brand Header -->
    <div class="h-16 flex items-center justify-between px-5 bg-slate-950 border-b border-slate-800">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div
                class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-black text-lg shadow-md shadow-emerald-500/20">
                D
            </div>
            <div>
                <div class="text-sm font-extrabold text-white tracking-tight uppercase">DEDIKASI</div>
                <div class="text-[10px] text-emerald-400 font-semibold">Admin Panel v2.0</div>
            </div>
        </a>
        <button type="button" id="admin-sidebar-close" class="lg:hidden text-slate-400 hover:text-white p-1">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <!-- Sidebar Navigation Menu Items -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-sm">
        <div class="px-3 pt-1 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500">Menu Utama</div>

        <a href="{{ route('home') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->routeIs('home') ? 'bg-emerald-600 text-white shadow-xs' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i
                class="fas fa-gauge-high w-5 text-center {{ request()->routeIs('home') ? 'text-white' : 'text-emerald-400' }}"></i>
            <span>Dashboard</span>
        </a>

        <div class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500">Pencatatan</div>

        <a href="{{ route('harga') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('input/harga*') ? 'bg-emerald-600 text-white shadow-xs' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i
                class="fas fa-tags w-5 text-center {{ request()->is('input/harga*') ? 'text-white' : 'text-emerald-400' }}"></i>
            <span>Harga Harian</span>
        </a>

        <a href="{{ route('stok') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('input/stok*') ? 'bg-emerald-600 text-white shadow-xs' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i
                class="fas fa-boxes-stacked w-5 text-center {{ request()->is('input/stok*') ? 'text-white' : 'text-teal-400' }}"></i>
            <span>Stok Mingguan</span>
        </a>

        <div class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500">Master Data</div>

        <a href="{{ route('pasar') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('data/pasar*') ? 'bg-emerald-600 text-white shadow-xs' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i
                class="fas fa-shop w-5 text-center {{ request()->is('data/pasar*') ? 'text-white' : 'text-amber-400' }}"></i>
            <span>Data Pasar</span>
        </a>

        <a href="{{ route('bahan') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('data/bahan*') ? 'bg-emerald-600 text-white shadow-xs' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i
                class="fas fa-carrot w-5 text-center {{ request()->is('data/bahan*') ? 'text-white' : 'text-orange-400' }}"></i>
            <span>Komoditi Bapok</span>
        </a>

        <a href="{{ route('kelompok') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('data/kelompok*') ? 'bg-emerald-600 text-white shadow-xs' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i
                class="fas fa-layer-group w-5 text-center {{ request()->is('data/kelompok*') ? 'text-white' : 'text-indigo-400' }}"></i>
            <span>Kelompok Bapok</span>
        </a>

        <a href="{{ route('satuan') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('data/satuan*') ? 'bg-emerald-600 text-white shadow-xs' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i
                class="fas fa-weight-scale w-5 text-center {{ request()->is('data/satuan*') ? 'text-white' : 'text-cyan-400' }}"></i>
            <span>Satuan Ukur</span>
        </a>

        <div class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500">Laporan &amp; Analisis
        </div>

        <a href="{{ route('report.harian') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('report/harga/rata-rata/harian*') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i class="fas fa-file-invoice w-5 text-center text-sky-400"></i>
            <span>Laporan Harian</span>
        </a>

        <a href="{{ route('report.bulanan') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('report/harga/rata-rata/bulanan*') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i class="fas fa-calendar-days w-5 text-center text-blue-400"></i>
            <span>Laporan Bulanan</span>
        </a>

        <a href="{{ route('report.riwayat') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('report/harga/riwayat*') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i class="fas fa-clock-rotate-left w-5 text-center text-amber-400"></i>
            <span>Riwayat Perubahan Harga</span>
        </a>

        <a href="{{ route('report.stokbulanan') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('report/stok/bulanan*') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i class="fas fa-boxes-packing w-5 text-center text-purple-400"></i>
            <span>Stok Bulanan</span>
        </a>

        <a href="{{ route('report.grafik.stok') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('report/stok/grafik*') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i class="fas fa-chart-area w-5 text-center text-teal-400"></i>
            <span>Grafik Stok</span>
        </a>

        <div class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500">Konten &amp; Publikasi
        </div>

        <a href="{{ route('berita') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('informasi/berita*') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i class="fas fa-newspaper w-5 text-center text-yellow-400"></i>
            <span>Artikel Berita</span>
        </a>

        <a href="{{ route('kategori') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('informasi/kategori*') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i class="fas fa-folder-tree w-5 text-center text-amber-300"></i>
            <span>Kategori Berita</span>
        </a>

        <a href="{{ route('slider') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('informasi/slider*') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i class="fas fa-images w-5 text-center text-pink-400"></i>
            <span>Banner Beranda</span>
        </a>

        <div class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500">Konfigurasi</div>

        <a href="{{ route('notifikasi') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('data/notifikasi*') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i class="fab fa-whatsapp w-5 text-center text-emerald-400"></i>
            <span>Nomor WA Alert</span>
        </a>

        <a href="{{ route('nomoraduan') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('data/nomoraduan*') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i class="fas fa-headset w-5 text-center text-sky-400"></i>
            <span>Nomor Aduan Publik</span>
        </a>

        <a href="{{ route('user') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('data/user*') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i class="fas fa-users-gear w-5 text-center text-violet-400"></i>
            <span>Manajemen User</span>
        </a>

        <a href="{{ route('gantipass') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition {{ request()->is('data/gantipassword*') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
            <i class="fas fa-key w-5 text-center text-slate-400"></i>
            <span>Ganti Password</span>
        </a>
    </nav>

    <!-- Sidebar Footer -->
    <div class="p-3 bg-slate-950 border-t border-slate-800 text-xs text-slate-400 flex items-center justify-between">
        <div class="truncate">

            <span class="font-semibold text-slate-200 block truncate">{{ Auth::user()->name ?? 'Administrator' }}</span>
            <span class="block text-[10px] text-emerald-400 font-mono">{{ Auth::user()->roles['name'] ?? 'Admin'
                }}</span>
        </div>
        <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
            @csrf
            <button type="submit" title="Keluar"
                class="p-2 text-rose-400 hover:text-rose-300 hover:bg-slate-800 rounded-lg transition">
                <i class="fas fa-power-off"></i>
            </button>
        </form>
    </div>
</aside>