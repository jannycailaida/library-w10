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
    <body class="font-sans antialiased text-slate-900 bg-gradient-to-br from-indigo-50/60 via-purple-50/40 to-slate-50 min-h-screen relative overflow-x-hidden selection:bg-indigo-600 selection:text-white flex flex-col justify-between items-center py-4 sm:py-6 px-4">

        {{-- Background decorative ambient lights (matching Catalogue Hero) --}}
        <div class="fixed -right-20 -top-20 w-96 h-96 rounded-full bg-indigo-200/35 blur-3xl pointer-events-none"></div>
        <div class="fixed left-1/4 -bottom-24 w-96 h-96 rounded-full bg-purple-200/30 blur-3xl pointer-events-none"></div>
        <div class="fixed -left-20 top-1/3 w-80 h-80 rounded-full bg-pink-200/20 blur-3xl pointer-events-none"></div>

        {{-- Top Navigation Bar --}}
        <header class="w-full max-w-4xl flex items-center justify-between z-10 shrink-0 mb-3 px-2">
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

        {{-- Main Unified Split Card Container (Compact & Fits on 1 Screen without scrolling) --}}
        <main class="w-full max-w-4xl my-auto z-10">
            <div class="w-full bg-white rounded-3xl shadow-xl shadow-indigo-950/5 border border-slate-200/80 overflow-hidden grid grid-cols-1 md:grid-cols-12">

                {{-- 📚 Left Showcase Column (Desktop Only / md:col-span-5) --}}
                <div class="hidden md:flex md:col-span-5 flex-col justify-between p-7 bg-gradient-to-br from-indigo-50/80 via-purple-50/50 to-pink-50/30 border-r border-slate-100 relative overflow-hidden select-none">
                    {{-- Ambient circular soft glow --}}
                    <div class="absolute -right-8 -bottom-8 w-56 h-56 rounded-full bg-indigo-200/40 blur-2xl pointer-events-none"></div>
                    <div class="absolute -left-8 -top-8 w-48 h-48 rounded-full bg-purple-200/35 blur-2xl pointer-events-none"></div>

                    {{-- Top Header of Showcase --}}
                    <div class="relative z-10">
                        <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-white/90 backdrop-blur-sm border border-indigo-200 text-indigo-800 text-[11px] font-bold shadow-sm">
                            <span class="text-indigo-600">✦</span>
                            <span>The Digital Library</span>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight mt-3 leading-snug">
                            Your next book is waiting.
                        </h3>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed font-normal">
                            Borrow university textbooks, algorithms, literature, and reference volumes.
                        </p>
                    </div>

                    {{-- Center: 3D Floating Book Stack --}}
                    <div class="relative z-10 my-4 flex items-center justify-center [perspective:1000px] py-4">
                        {{-- Book 1 (Left - Algorithms, tilted -12deg) --}}
                        <div class="relative transform -rotate-12 translate-x-4 hover:-translate-y-1 hover:-rotate-6 transition-all duration-300 z-10">
                            <div class="w-20 h-32 rounded-xl overflow-hidden shadow-lg shadow-slate-400/25 border-2 border-white bg-slate-100">
                                <img src="{{ asset('images/books/isbn_9780262046305.jpg') }}"
                                     alt="Algorithms"
                                     class="w-full h-full object-cover">
                            </div>
                        </div>

                        {{-- Book 2 (Center - Clean Code, elevated) --}}
                        <div class="relative transform scale-105 -translate-y-1 hover:scale-110 transition-all duration-300 z-20">
                            <div class="w-24 h-36 rounded-2xl overflow-hidden shadow-2xl shadow-indigo-950/20 border-2 border-amber-400 bg-white relative">
                                <img src="{{ asset('images/books/isbn_9780132350884.jpg') }}"
                                     alt="Clean Code"
                                     class="w-full h-full object-cover">
                                {{-- Bookmark ribbon --}}
                                <div class="absolute top-0 right-2.5 w-2.5 h-5 bg-amber-400 [clip-path:polygon(0_0,100%_0,100%_100%,50%_75%,0_100%)] shadow-sm"></div>
                            </div>
                            <div class="absolute -bottom-2.5 left-1/2 -translate-x-1/2 bg-amber-400 text-amber-950 text-[9px] font-black px-2.5 py-0.5 rounded-full shadow-sm whitespace-nowrap">
                                ★ Most Borrowed
                            </div>
                        </div>

                        {{-- Book 3 (Right - Calculus, tilted +12deg) --}}
                        <div class="relative transform rotate-12 -translate-x-4 hover:-translate-y-1 hover:rotate-6 transition-all duration-300 z-10">
                            <div class="w-20 h-32 rounded-xl overflow-hidden shadow-lg shadow-slate-400/25 border-2 border-white bg-slate-100">
                                <img src="{{ asset('images/books/isbn_9781337613927.jpg') }}"
                                     alt="Calculus"
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>

                    {{-- Bottom Live Availability Badge --}}
                    <div class="relative z-10 pt-3 border-t border-indigo-100/80 flex items-center justify-between text-[11px] text-slate-600 font-medium">
                        <span class="inline-flex items-center space-x-1.5 text-emerald-700 font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>26 books available now</span>
                        </span>
                        <span class="text-slate-500 font-semibold text-[10px]">14-Day Free Loan</span>
                    </div>
                </div>

                {{-- 📝 Right Form Column (md:col-span-7) --}}
                <div class="md:col-span-7 p-6 sm:p-8 flex flex-col justify-center bg-white">
                    {{ $slot }}
                </div>
            </div>
        </main>

        {{-- Footer Note --}}
        <footer class="text-center text-xs text-slate-500 z-10 shrink-0 mt-3 pb-1">
            &copy; {{ date('Y') }} LibraryHub Digital System &bull; University Coursework
        </footer>
    </body>
</html>
