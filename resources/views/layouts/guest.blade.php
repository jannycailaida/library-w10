<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Access your university library account with LibraryHub.">
    <title>{{ $title ?? 'Your account' }} · LibraryHub</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-stone-50">
    <a href="#main-content" class="skip-link">Skip to content</a>
    <header class="mx-auto flex w-full max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-5 sm:px-8">
        <a href="{{ route('books.index') }}" class="flex items-center gap-2.5 text-lg font-bold tracking-tight text-stone-900"><x-application-logo class="h-8 w-8 text-brand-700" />LibraryHub</a>
        <a href="{{ route('books.index') }}" class="btn text-stone-600 hover:text-brand-800"><span aria-hidden="true">←</span> Back to catalogue</a>
    </header>
    <main id="main-content" class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center px-4 py-5" tabindex="-1">
        <div class="panel px-6 py-7 sm:px-8 sm:py-8">{{ $slot }}</div>
    </main>
    <footer class="px-4 py-5 text-center text-sm text-stone-600">© {{ date('Y') }} LibraryHub <span aria-hidden="true">·</span> University digital library</footer>
</body>
</html>
