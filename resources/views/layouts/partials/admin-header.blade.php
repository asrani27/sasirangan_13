<!-- Top Admin Header -->
<header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 shadow-xs">
    <div class="flex items-center gap-3">
        <button type="button" id="admin-sidebar-open" class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-lg hover:bg-slate-100">
            <i class="fas fa-bars text-lg"></i>
        </button>
        <div class="flex flex-col">
            @hasSection('title')
                @yield('title')
            @else
                <h1 class="text-base sm:text-lg font-bold text-slate-900">@yield('page_heading', 'Dashboard Admin')</h1>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-3 sm:gap-4">
        <a href="{{ route('beranda') }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg text-slate-600 hover:text-emerald-700 bg-slate-100 hover:bg-emerald-50 transition border border-slate-200">
            <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
            <span class="hidden sm:inline">Web Publik</span>
        </a>

        <div class="h-6 w-px bg-slate-200"></div>

        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-xs shadow-xs">
                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
            </div>
            <span class="hidden md:inline text-sm font-semibold text-slate-700">{{ Auth::user()->name ?? 'Admin' }}</span>
        </div>
    </div>
</header>
