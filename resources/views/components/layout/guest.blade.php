<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $theme ?? 'theme-default' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset(app()->environment('production') ? 'favicon.svg' : 'favicon-dev.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <title>@if(isset($userName)){{ $userName }} - @endif{{ $title ?? 'Gift Exchange' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 antialiased">
    <div class="flex min-h-screen flex-col justify-center py-12 sm:px-6 lg:px-8">
        {{ $slot }}
    </div>
</body>
</html>
