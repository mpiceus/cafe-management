<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ann Coffee')</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/5.3.3/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ \App\Http\Controllers\ResourceAssetController::url('css', 'layout.css') }}">
    @stack('styles')
</head>
<body class="@auth auth-shell @endauth">
@auth
    <div class="app-shell d-flex">
        @include('partials.sidebar')
        <div class="content flex-grow-1">
            @include('partials.navbar')
            <main class="content-main">
            <div class="container-fluid py-4">
                @include('partials.flash')
                @yield('content')
            </div>
            </main>
            <footer class="app-footer px-4 py-2 text-center">
                Ann Coffee · 89 Lò Đúc, Hai Bà Trưng, Hà Nội · &copy; 2026
            </footer>
        </div>
    </div>
@else
    @yield('content')
@endauth
<script src="{{ asset('vendor/bootstrap/5.3.3/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ \App\Http\Controllers\ResourceAssetController::url('js', 'layout.js') }}"></script>
@stack('scripts')
</body>
</html>
