<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Estate Platform') }} - Estate Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">

    <div class="flex min-h-screen">
        <!-- SIDEBAR -->
        <aside class="w-64 bg-gray-800 shadow-lg">
            @include('layouts.partials.estate-sidebar')
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col">
            <!-- HEADER -->
            <header class="h-20 bg-white shadow flex items-center px-6">
                @include('layouts.partials.header')
            </header>

            <!-- PAGE CONTENT -->
            <main class="flex-1 p-6 bg-gray-100 overflow-auto">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
