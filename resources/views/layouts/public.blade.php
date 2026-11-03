<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DEDIKASI - Pemantauan Harga Bapok Banjarmasin')</title>
    <link rel="icon" type="image/png" href="/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('css')
</head>
<body class="flex min-h-full flex-col text-slate-800 antialiased font-sans bg-slate-50 selection:bg-emerald-500 selection:text-white">
    @include('layouts.partials.header')
    @include('layouts.partials.toast')

    <!-- Main Content Area -->
    <main class="flex-1">
        @yield('content')
    </main>

    @include('layouts.partials.whatsapp-widget')
    @include('layouts.partials.footer')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const iconOpen = document.getElementById('menu-icon-open');
            const iconClose = document.getElementById('menu-icon-close');

            if (btn && menu) {
                btn.addEventListener('click', () => {
                    const isHidden = menu.classList.contains('hidden');
                    menu.classList.toggle('hidden', !isHidden);
                    if (iconOpen) iconOpen.classList.toggle('hidden', !isHidden);
                    if (iconClose) iconClose.classList.toggle('hidden', isHidden);
                });
            }
        });
    </script>
    @stack('js')
</body>
</html>
