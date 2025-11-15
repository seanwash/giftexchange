<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $theme ?? 'theme-default' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset(app()->environment('production') ? 'favicon.svg' : 'favicon-dev.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <title>@if(isset($eventName)){{ $eventName }} - @endif{{ $title ?? 'Gift Exchange - Admin' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 antialiased">
    <div class="min-h-screen">
        <nav class="border-b border-gray-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <div class="flex shrink-0 items-center">
                            <h1 class="text-xl font-bold text-gray-900">Gift Exchange Admin</h1>
                            @if(isset($eventName))
                                <span class="ml-4 text-sm text-gray-600">({{ $eventName }})</span>
                            @endif
                        </div>
                    </div>
                    @if(isset($nav))
                        <div class="flex items-center">
                            {{ $nav }}
                        </div>
                    @endif
                </div>
            </div>
        </nav>

        <main class="py-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
