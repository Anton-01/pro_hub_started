<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Panel Admin') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/sass/admin.scss', 'resources/js/admin.js'])

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    @stack('styles')
    @notifyCss
</head>
<body>
    <div class="admin-wrapper">
        {{-- Sidebar --}}
        @include('admin.partials.sidebar')

        {{-- Sidebar Backdrop (Mobile) --}}
        <div class="sidebar-backdrop"></div>

        {{-- Main Content --}}
        <div class="main-content">
            {{-- Header --}}
            @include('admin.partials.header')

            {{-- Page Content --}}
            <div class="page-content">
                {{-- Alerts --}}
                @include('admin.partials.alerts')

                {{-- Page Header --}}
                @hasSection('page-header')
                    <div class="page-header">
                        @yield('page-header')
                    </div>
                @endif

                {{-- Content --}}
                @yield('content')
            </div>
        </div>
    </div>

    {{-- Toast Container --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Laravel Notify -->
    <x-notify::notify />
    @notifyJs

    @stack('modals')
    @stack('scripts')
</body>
</html>
