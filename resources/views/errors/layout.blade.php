<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · LibraryHub</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="flex min-h-screen items-center justify-center bg-stone-50 px-5 py-10 text-stone-900">
    <main class="w-full max-w-lg">
        <a href="{{ route('books.index') }}" class="mb-12 flex items-center gap-2.5 text-lg font-bold"><x-application-logo class="h-8 w-8 text-brand-700" />LibraryHub</a>
        <p class="mb-3 text-sm font-semibold text-stone-600">Error @yield('code')</p>
        <h1 class="font-serif text-3xl tracking-tight sm:text-4xl">@yield('title')</h1>
        <p class="mt-4 max-w-md text-base leading-7 text-stone-600">@yield('message')</p>
        <div class="mt-7 flex flex-wrap gap-3"><a href="{{ route('books.index') }}" class="btn-primary">Back to catalogue</a>@auth<a href="{{ route('dashboard') }}" class="btn-secondary">My overview</a>@else<a href="{{ route('login') }}" class="btn-secondary">Sign in</a>@endauth</div>
    </main>
</body>
</html>
