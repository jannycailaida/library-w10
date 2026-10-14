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
    <body class="font-sans antialiased text-slate-900 bg-gradient-to-br from-slate-50 via-indigo-50/35 to-slate-100 min-h-screen relative overflow-x-hidden selection:bg-indigo-600 selection:text-white">

        {{-- Background decorative ambient lights --}}
        <div class="fixed -right-20 -top-20 w-96 h-96 rounded-full bg-indigo-200/40 blur-3xl pointer-events-none"></div>
        <div class="fixed left-1/4 -bottom-24 w-96 h-96 rounded-full bg-purple-200/35 blur-3xl pointer-events-none"></div>
        <div class="fixed -left-20 top-1/3 w-80 h-80 rounded-full bg-sky-200/30 blur-3xl pointer-events-none"></div>

        <div class="min-h-screen flex flex-col justify-between items-center py-8 px-4 sm:px-6 relative z-10">

            {{-- Top Link back to public Catalogue --}}
            <div class="w-full max-w-md flex items-center justify-between">
                <a href="{{ route('books.index') }}"
                   class="inline-flex items-center space-x-2 text-xs font-bold text-slate-600 hover:text-indigo-600 transition group">
                    <span class="transform group-hover:-translate-x-1 transition-transform">←</span>
                    <span>Back to Catalogue</span>
                </a>

                <div class="text-xs text-slate-400 font-mono">
                    Library Auth v3.5
                </div>
            </div>

            {{-- Center Form Card --}}
            <div class="w-full sm:max-w-md my-auto py-6">
                {{-- Logo Header --}}
                <div class="flex justify-center mb-6">
                    <a href="{{ route('books.index') }}" class="p-3 bg-white/80 rounded-3xl backdrop-blur-md border border-slate-200/80 hover:border-slate-300 transition shadow-sm hover:shadow">
                        <x-application-logo />
                    </a>
                </div>

                {{-- The Form Card --}}
                <div class="w-full bg-white rounded-3xl shadow-xl shadow-slate-200/70 p-7 sm:p-9 border border-slate-200/80">
                    {{ $slot }}
                </div>
            </div>

            {{-- Footer Note --}}
            <div class="text-center text-xs text-slate-500 pb-2">
                &copy; {{ date('Y') }} LibraryHub Digital System &bull; University Coursework
            </div>
        </div>
    </body>
</html>
