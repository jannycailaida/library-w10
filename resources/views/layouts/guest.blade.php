<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LibraryHub') }} — Authentication</title>
        <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📚</text></svg>">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-900 bg-gradient-to-br from-indigo-50/70 via-purple-50/50 to-pink-50/30 min-h-screen relative overflow-x-hidden selection:bg-indigo-600 selection:text-white flex flex-col justify-between items-center py-6 px-4">

        {{-- Background decorative ambient lights (matching Catalogue Hero) --}}
        <div class="fixed -right-20 -top-20 w-96 h-96 rounded-full bg-indigo-200/35 blur-3xl pointer-events-none"></div>
        <div class="fixed left-1/4 -bottom-24 w-96 h-96 rounded-full bg-purple-200/30 blur-3xl pointer-events-none"></div>
        <div class="fixed -left-20 top-1/3 w-80 h-80 rounded-full bg-pink-200/20 blur-3xl pointer-events-none"></div>

        {{-- Top Navigation Bar --}}
        <header class="w-full max-w-md flex items-center justify-between z-10 shrink-0 mb-2 px-1">
            <a href="{{ route('books.index') }}"
               class="inline-flex items-center space-x-1.5 text-xs font-bold text-slate-600 hover:text-indigo-600 transition group">
                <span class="transform group-hover:-translate-x-1 transition-transform">←</span>
                <span>Back to Catalogue</span>
            </a>

            <a href="{{ route('books.index') }}" class="flex items-center space-x-2 text-xs font-extrabold text-indigo-700 hover:opacity-80 transition">
                <span class="text-base">📚</span>
                <span>LibraryHub</span>
            </a>
        </header>

        {{-- Center Form Card (Clean, Simple & Centered) --}}
        <main class="w-full max-w-md my-auto py-2 z-10">
            <div class="w-full bg-white rounded-3xl shadow-xl shadow-indigo-950/5 border border-indigo-100/80 p-6 sm:p-8">
                {{ $slot }}
            </div>
        </main>

        {{-- Footer Note --}}
        <footer class="text-center text-xs text-slate-500 z-10 shrink-0 mt-3 pb-1">
            &copy; {{ date('Y') }} LibraryHub Digital System &bull; University Coursework
        </footer>
    </body>
</html>
