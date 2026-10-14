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
    <body class="font-sans antialiased text-slate-900 bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 min-h-screen relative overflow-x-hidden selection:bg-indigo-500 selection:text-white flex flex-col justify-between items-center py-4 px-4 sm:px-6">

        {{-- Background decorative ambient lights --}}
        <div class="fixed -right-20 -top-20 w-96 h-96 rounded-full bg-indigo-500/20 blur-3xl pointer-events-none"></div>
        <div class="fixed left-1/4 -bottom-24 w-96 h-96 rounded-full bg-purple-500/15 blur-3xl pointer-events-none"></div>
        <div class="fixed -left-20 top-1/3 w-80 h-80 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>

        {{-- Top Link back to public Catalogue --}}
        <div class="w-full max-w-md flex items-center justify-between z-10 shrink-0 mb-1">
            <a href="{{ route('books.index') }}"
               class="inline-flex items-center space-x-1.5 text-xs font-bold text-indigo-300 hover:text-white transition group">
                <span class="transform group-hover:-translate-x-1 transition-transform">←</span>
                <span>Back to Catalogue</span>
            </a>

            <div class="text-[11px] text-indigo-200/50 font-mono">
                LibraryHub Access
            </div>
        </div>

        {{-- Center Form Card (Compact & Fits on 1 screen) --}}
        <div class="w-full sm:max-w-md my-auto py-2 z-10">
            <div class="w-full bg-white rounded-3xl shadow-2xl p-6 sm:p-7 border border-white/20">
                {{ $slot }}
            </div>
        </div>

        {{-- Footer Note --}}
        <div class="text-center text-[11px] text-indigo-200/40 z-10 shrink-0 mt-1">
            &copy; {{ date('Y') }} LibraryHub Digital System &bull; University Coursework
        </div>
    </body>
</html>
