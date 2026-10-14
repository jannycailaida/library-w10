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
    <body class="font-sans antialiased text-slate-900 bg-gradient-to-br from-slate-50 via-indigo-50/45 to-purple-50/40 min-h-screen relative overflow-x-hidden selection:bg-indigo-600 selection:text-white">

        {{-- Background decorative ambient lights (matching Catalogue Hero) --}}
        <div class="fixed -right-20 -top-20 w-96 h-96 rounded-full bg-indigo-200/40 blur-3xl pointer-events-none"></div>
        <div class="fixed left-1/4 -bottom-24 w-96 h-96 rounded-full bg-purple-200/35 blur-3xl pointer-events-none"></div>
        <div class="fixed -left-20 top-1/3 w-80 h-80 rounded-full bg-sky-200/30 blur-3xl pointer-events-none"></div>

        <div class="min-h-screen flex flex-col justify-between items-center py-6 sm:py-8 px-4 sm:px-6 relative z-10">

            {{-- Top Bar Link back to public Catalogue --}}
            <div class="w-full max-w-5xl flex items-center justify-between mb-2">
                <a href="{{ route('books.index') }}"
                   class="inline-flex items-center space-x-2 text-xs font-bold text-slate-600 hover:text-indigo-600 transition group">
                    <span class="transform group-hover:-translate-x-1 transition-transform">←</span>
                    <span>Back to Catalogue</span>
                </a>

                <div class="text-xs text-slate-400 font-mono">
                    LibraryHub Auth
                </div>
            </div>

            {{-- Main Content Area: Centered Form with Side Floating Books on Desktop --}}
            <div class="w-full max-w-5xl my-auto py-4 flex flex-col lg:flex-row items-center justify-center gap-10 lg:gap-14">

                {{-- Left Side on Desktop: Floating 3D Book Showcase (Extension of Catalogue) --}}
                <div class="hidden lg:flex flex-col items-center justify-center shrink-0 relative select-none">
                    {{-- Soft ambient glow behind books --}}
                    <div class="absolute w-64 h-64 rounded-full bg-gradient-to-tr from-indigo-200/60 via-purple-200/50 to-pink-200/40 blur-3xl pointer-events-none"></div>

                    {{-- Mini 3D Overlapping Book Stack --}}
                    <div class="relative flex items-center justify-center py-6 px-4 [perspective:1000px] group scale-95">

                        {{-- Floating Top Pill --}}
                        <div class="absolute -top-1 right-2 z-30 inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-white/95 backdrop-blur-md border border-slate-200 text-slate-700 text-[10px] font-bold shadow-md">
                            <span class="text-amber-500">✦</span>
                            <span>Featured Student Titles</span>
                        </div>

                        {{-- Book 1 (Left - Algorithms, tilted -12deg) --}}
                        <div class="relative transform -rotate-12 translate-x-4 hover:-translate-y-1 hover:-rotate-6 transition-all duration-300 z-10 cursor-pointer">
                            <div class="w-20 h-32 rounded-xl overflow-hidden shadow-lg shadow-slate-400/20 border-2 border-white bg-slate-100">
                                <img src="{{ asset('images/books/isbn_9780262046305.jpg') }}"
                                     alt="Introduction to Algorithms"
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="absolute -bottom-2 -left-2 bg-white/95 backdrop-blur-sm text-[8px] font-bold text-indigo-800 px-1.5 py-0.5 rounded-md border border-indigo-200 shadow-xs">
                                Algorithms
                            </div>
                        </div>

                        {{-- Book 2 (Center - Clean Code, elevated) --}}
                        <div class="relative transform scale-110 -translate-y-2 hover:scale-115 transition-all duration-300 z-20 cursor-pointer">
                            <div class="w-24 h-36 rounded-2xl overflow-hidden shadow-xl shadow-indigo-950/20 border-2 border-amber-400 bg-white relative">
                                <img src="{{ asset('images/books/isbn_9780132350884.jpg') }}"
                                     alt="Clean Code"
                                     class="w-full h-full object-cover">
                                {{-- Bookmark ribbon --}}
                                <div class="absolute top-0 right-2.5 w-2.5 h-5 bg-amber-400 [clip-path:polygon(0_0,100%_0,100%_100%,50%_75%,0_100%)] shadow-xs"></div>
                            </div>
                            <div class="absolute -bottom-2.5 left-1/2 -translate-x-1/2 bg-amber-400 text-amber-950 text-[9px] font-black px-2 py-0.5 rounded-full shadow-sm whitespace-nowrap">
                                ★ Most Borrowed
                            </div>
                        </div>

                        {{-- Book 3 (Right - Calculus, tilted +12deg) --}}
                        <div class="relative transform rotate-12 -translate-x-4 hover:-translate-y-1 hover:rotate-6 transition-all duration-300 z-10 cursor-pointer">
                            <div class="w-20 h-32 rounded-xl overflow-hidden shadow-lg shadow-slate-400/20 border-2 border-white bg-slate-100">
                                <img src="{{ asset('images/books/isbn_9781337613927.jpg') }}"
                                     alt="Calculus"
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="absolute -bottom-2 -right-2 bg-white/95 backdrop-blur-sm text-[8px] font-bold text-purple-800 px-1.5 py-0.5 rounded-md border border-purple-200 shadow-xs">
                                Calculus
                            </div>
                        </div>

                        {{-- Floating Bottom Pill --}}
                        <div class="absolute -bottom-2 left-2 z-30 inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full bg-white/95 backdrop-blur-md border border-emerald-300 text-emerald-800 text-[10px] font-bold shadow-md">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>14-Day Free Loan</span>
                        </div>
                    </div>

                    <div class="mt-4 text-center max-w-[220px]">
                        <p class="text-xs font-semibold text-slate-700 leading-snug">
                            Over 40 academic titles ready for checkout.
                        </p>
                        <p class="text-[11px] text-slate-500 mt-1">
                            Reserved for students & university faculty.
                        </p>
                    </div>
                </div>

                {{-- Center Form Card --}}
                <div class="w-full sm:max-w-md">
                    {{-- Logo Header --}}
                    <div class="flex justify-center mb-5">
                        <a href="{{ route('books.index') }}" class="p-3 bg-white/90 rounded-3xl backdrop-blur-md border border-slate-200/80 hover:border-slate-300 transition shadow-sm hover:shadow">
                            <x-application-logo />
                        </a>
                    </div>

                    {{-- The Form Card --}}
                    <div class="w-full bg-white rounded-3xl shadow-xl shadow-slate-200/70 p-7 sm:p-9 border border-slate-200/80">
                        {{ $slot }}
                    </div>
                </div>
            </div>

            {{-- Footer Note --}}
            <div class="text-center text-xs text-slate-500 pb-2">
                &copy; {{ date('Y') }} LibraryHub Digital System &bull; University Coursework
            </div>
        </div>
    </body>
</html>
