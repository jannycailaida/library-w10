<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Browse the university library, borrow books, and keep track of your loans with LibraryHub.">
    <title>{{ $title ?? 'University library' }} · LibraryHub</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-stone-50">
    <a href="#main-content" class="skip-link">Skip to content</a>
    @include('layouts.navigation')
    @isset($header)
        <header class="border-b border-stone-200 bg-white"><div class="page-shell py-6">{{ $header }}</div></header>
    @endisset
    <main id="main-content" class="flex-1" tabindex="-1">{{ $slot }}</main>
    <footer class="mt-8 border-t border-stone-200">
        <div class="mx-auto flex max-w-7xl flex-col justify-between gap-2 px-4 py-6 text-sm text-stone-600 sm:flex-row sm:px-6 lg:px-8">
            <p>LibraryHub <span aria-hidden="true">·</span> University digital library</p>
            <p>Borrow for 14 days. Return at the circulation desk.</p>
        </div>
    </footer>
    <x-confirmation-dialog />
</body>
</html>
