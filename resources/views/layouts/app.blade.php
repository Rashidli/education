<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <div class="app">
        @include('partials.sidebar')

        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <div class="app__main">
            @include('partials.header')

            <main class="app__content">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        (function () {
            const sidebar = document.querySelector('.sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const menuBtn = document.getElementById('menuBtn');
            if (!sidebar || !backdrop || !menuBtn) return;

            const open = () => { sidebar.classList.add('is-open'); backdrop.classList.add('is-open'); };
            const close = () => { sidebar.classList.remove('is-open'); backdrop.classList.remove('is-open'); };

            menuBtn.addEventListener('click', open);
            backdrop.addEventListener('click', close);
        })();
    </script>
    @stack('scripts')
</body>
</html>
