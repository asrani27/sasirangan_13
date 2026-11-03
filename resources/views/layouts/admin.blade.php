<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title_text', 'Panel Administrator') - Dedikasi Banjarmasin</title>
    <link rel="icon" type="image/png" href="/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('css')
</head>
<body class="flex min-h-full antialiased font-sans bg-slate-100 text-slate-800">
    @include('layouts.partials.admin-sidebar')

    <!-- Main Content Shell -->
    <div class="flex-1 flex flex-col min-w-0 lg:pl-64">
        @include('layouts.partials.admin-header')
        @include('layouts.partials.toast')

        <!-- Main Page Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>

        <!-- Admin Footer -->
        <footer class="bg-white border-t border-slate-200 py-4 px-6 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                &copy; {{ date('Y') }} Dedikasi &bull; Pemerintah Kota Banjarmasin
            </div>
            <div class="text-slate-400">
                Modernized with Laravel 12 &amp; Tailwind CSS
            </div>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('admin-sidebar-backdrop');
            const openBtn = document.getElementById('admin-sidebar-open');
            const closeBtn = document.getElementById('admin-sidebar-close');

            function toggle(show) {
                if (show) {
                    sidebar.classList.remove('-translate-x-full');
                    backdrop.classList.remove('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                }
            }

            if (openBtn) openBtn.addEventListener('click', () => toggle(true));
            if (closeBtn) closeBtn.addEventListener('click', () => toggle(false));
            if (backdrop) backdrop.addEventListener('click', () => toggle(false));
        });
    </script>
    @stack('js')
</body>
</html>
