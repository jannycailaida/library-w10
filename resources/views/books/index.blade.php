<x-app-layout>
    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Flash message --}}
        @if (session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 flex items-center justify-between shadow-sm animate-fade-in">
                <div class="flex items-center space-x-3">
                    <span class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm">✓</span>
                    <span class="font-medium text-sm">{{ session('status') }}</span>
                </div>
            </div>
        @endif

        {{-- 🏛️ Modern Hero Banner --}}
        <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 text-white p-8 sm:p-12 mb-10 shadow-xl border border-indigo-900/40">
            {{-- Background decorative ambient lights --}}
            <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-indigo-500/20 blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/4 -bottom-20 w-80 h-80 rounded-full bg-purple-500/15 blur-3xl pointer-events-none"></div>
            <div class="absolute left-1/3 top-0 w-60 h-60 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold uppercase tracking-wider mb-4">
                        <span>✦</span>
                        <span>The Digital Library System</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
                        Discover, Borrow & <br class="hidden sm:inline">Expand Your Mind.
                    </h1>

                    <p class="mt-4 text-base sm:text-lg text-indigo-200/80 leading-relaxed">
                        Access our curated collection of literature, computer science, and reference volumes. Borrow books instantly with your student card.
                    </p>

                    {{-- Live Stats Bar --}}
                    <div class="mt-8 flex flex-wrap items-center gap-4 sm:gap-8 pt-6 border-t border-white/10 text-xs sm:text-sm">
                        <div class="flex items-center space-x-2">
                            <span class="text-xl">📚</span>
                            <div>
                                <span class="font-black text-white text-base">{{ $totalBooks }}</span>
                                <span class="text-indigo-300 block text-xs">Total Titles</span>
                            </div>
                        </div>

                        <div class="h-8 w-px bg-white/10 hidden sm:block"></div>

                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <div>
                                <span class="font-black text-emerald-300 text-base">{{ $availableCount }}</span>
                                <span class="text-indigo-300 block text-xs">Available Now</span>
                            </div>
                        </div>

                        <div class="h-8 w-px bg-white/10 hidden sm:block"></div>

                        <div class="flex items-center space-x-2">
                            <span class="text-xl">🔒</span>
                            <div>
                                <span class="font-black text-amber-300 text-base">{{ $referenceCount }}</span>
                                <span class="text-indigo-300 block text-xs">Reference Books</span>
                            </div>
                        </div>

                        <div class="h-8 w-px bg-white/10 hidden sm:block"></div>

                        <div class="flex items-center space-x-2">
                            <span class="text-xl">👥</span>
                            <div>
                                <span class="font-black text-purple-300 text-base">{{ $totalMembers }}</span>
                                <span class="text-indigo-300 block text-xs">Library Members</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action on Hero --}}
                @can('create', App\Models\Book::class)
                    <div class="shrink-0">
                        <a href="{{ route('books.create') }}"
                           class="inline-flex items-center space-x-2 px-6 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm rounded-2xl shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5 active:translate-y-0">
                            <span class="text-lg">+</span>
                            <span>Add New Book</span>
                        </a>
                    </div>
                @endcan
            </div>
        </div>

        {{-- 🔍 Search & Filter Toolbar --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-5 mb-8">
            <form method="GET" action="{{ route('books.index') }}" class="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center justify-between">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Search by title, author, or 13-digit ISBN..."
                           class="w-full ps-11 pe-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition placeholder:text-gray-400">
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('books.index', ['search' => $search]) }}"
                       class="px-4 py-2.5 text-xs font-bold rounded-xl transition flex items-center space-x-1.5 {{ empty($filter) ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        <span>📚 All Books</span>
                        <span class="opacity-75 font-normal">({{ $totalBooks }})</span>
                    </a>

                    <a href="{{ route('books.index', ['search' => $search, 'filter' => 'available']) }}"
                       class="px-4 py-2.5 text-xs font-bold rounded-xl transition flex items-center space-x-1.5 {{ $filter === 'available' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>Available to Borrow</span>
                        <span class="opacity-75 font-normal">({{ $availableCount }})</span>
                    </a>

                    <a href="{{ route('books.index', ['search' => $search, 'filter' => 'reference']) }}"
                       class="px-4 py-2.5 text-xs font-bold rounded-xl transition flex items-center space-x-1.5 {{ $filter === 'reference' ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        <span>🔒 Reference Only</span>
                        <span class="opacity-75 font-normal">({{ $referenceCount }})</span>
                    </a>

                    <button type="submit" class="px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-xl transition shadow-sm">
                        Filter
                    </button>

                    @if($search || $filter)
                        <a href="{{ route('books.index') }}" class="px-3 py-2 text-xs font-semibold text-gray-500 hover:text-rose-600 transition">
                            ✕ Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- 📚 Books Grid --}}
        @if ($books->isEmpty())
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-16 text-center max-w-md mx-auto">
                <div class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-600 mx-auto flex items-center justify-center text-3xl mb-4">
                    🔍
                </div>
                <h3 class="text-xl font-bold text-gray-900">No books found</h3>
                <p class="text-sm text-gray-500 mt-1">We couldn't find any books matching your search query or filter.</p>
                <a href="{{ route('books.index') }}" class="mt-6 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition">
                    View All Books
                </a>
            </div>
        @else
            @php
                // Distinct thematic palettes for realistic book jacket variety
                $themes = [
                    ['bg' => 'from-slate-950 via-blue-950 to-indigo-950', 'accent' => 'border-blue-400/30', 'spine' => 'border-blue-900', 'ribbon' => 'bg-blue-400', 'text_accent' => 'text-blue-300'],
                    ['bg' => 'from-slate-950 via-emerald-950 to-teal-950', 'accent' => 'border-emerald-400/30', 'spine' => 'border-emerald-900', 'ribbon' => 'bg-emerald-400', 'text_accent' => 'text-emerald-300'],
                    ['bg' => 'from-slate-950 via-rose-950 to-red-950', 'accent' => 'border-rose-400/30', 'spine' => 'border-rose-900', 'ribbon' => 'bg-rose-400', 'text_accent' => 'text-rose-300'],
                    ['bg' => 'from-slate-950 via-purple-950 to-indigo-950', 'accent' => 'border-purple-400/30', 'spine' => 'border-purple-900', 'ribbon' => 'bg-purple-400', 'text_accent' => 'text-purple-300'],
                    ['bg' => 'from-stone-950 via-amber-950 to-yellow-950', 'accent' => 'border-amber-400/30', 'spine' => 'border-amber-900', 'ribbon' => 'bg-amber-400', 'text_accent' => 'text-amber-300'],
                    ['bg' => 'from-neutral-950 via-slate-900 to-stone-950', 'accent' => 'border-slate-400/30', 'spine' => 'border-slate-900', 'ribbon' => 'bg-slate-400', 'text_accent' => 'text-slate-300'],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-7">
                @foreach ($books as $book)
                    @php
                        $isBorrowed = $book->active_loans_count > 0;
                        $theme = $themes[$book->id % count($themes)];
                    @endphp

                    <div class="group bg-white rounded-2xl shadow-sm hover:shadow-2xl hover:-translate-y-2 border border-gray-100 overflow-hidden flex flex-col transition-all duration-300">
                        {{-- 📖 Realistic Hardcover Book Spine & Jacket Mockup --}}
                        <div class="h-64 bg-slate-950 relative overflow-hidden select-none border-l-4 {{ $theme['spine'] }} shadow-inner flex flex-col justify-between">
                            @if ($book->cover_path)
                                {{-- Real Book Cover Photo --}}
                                <img src="{{ asset($book->cover_path) }}"
                                     alt="{{ $book->title }}"
                                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                                {{-- Dark gradient overlays for readability and realism --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/15 to-black/60 pointer-events-none"></div>

                                {{-- Hardcover spine shadow crease --}}
                                <div class="absolute left-0 top-0 bottom-0 w-3 bg-gradient-to-r from-black/70 via-black/30 to-transparent pointer-events-none z-10"></div>

                                {{-- Bookmark ribbon for Available Books --}}
                                @if (!$book->is_reference && !$isBorrowed)
                                    <div class="absolute top-0 right-4 w-4 h-8 bg-amber-400 shadow-md [clip-path:polygon(0_0,100%_0,100%_100%,50%_75%,0_100%)] z-20"></div>
                                @endif

                                {{-- Top Header on Book Cover: Status badge + Year --}}
                                <div class="flex items-start justify-between z-10 relative p-4">
                                    @if ($book->is_reference)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-400 text-amber-950 shadow-md">
                                            🔒 Reference
                                        </span>
                                    @elseif ($isBorrowed)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-600 text-white shadow-md">
                                            ⏳ Borrowed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500 text-white shadow-md">
                                            ✓ Available
                                        </span>
                                    @endif

                                    <span class="text-xs text-white font-mono bg-black/60 backdrop-blur-sm px-2 py-0.5 rounded-md shadow-sm">
                                        {{ $book->published_year }}
                                    </span>
                                </div>

                                {{-- Bottom overlay info on picture --}}
                                <div class="p-4 z-10 relative mt-auto">
                                    <h4 class="font-serif font-bold text-white text-sm leading-snug line-clamp-2 drop-shadow-md group-hover:text-amber-200 transition">
                                        {{ $book->title }}
                                    </h4>
                                    <div class="flex items-center justify-between text-[11px] text-white/70 font-mono mt-1">
                                        <span>ISBN-13</span>
                                        <span>{{ $book->isbn }}</span>
                                    </div>
                                </div>
                            @else
                                {{-- Hardcover spine shadow crease --}}
                                <div class="absolute left-0 top-0 bottom-0 w-3 bg-gradient-to-r from-black/50 via-black/20 to-transparent pointer-events-none"></div>

                                {{-- Bookmark ribbon for Available Books --}}
                                @if (!$book->is_reference && !$isBorrowed)
                                    <div class="absolute top-0 right-4 w-4 h-7 bg-amber-400 shadow-md [clip-path:polygon(0_0,100%_0,100%_100%,50%_75%,0_100%)] z-20"></div>
                                @endif

                                {{-- Background geometric watermark --}}
                                <div class="absolute -right-6 -bottom-6 text-white/5 font-black text-9xl pointer-events-none select-none">
                                    📖
                                </div>

                                {{-- Top Header on Book Cover: Status badge + Year --}}
                                <div class="flex items-start justify-between z-10 relative p-6">
                                    @if ($book->is_reference)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-400 text-amber-950 shadow-sm">
                                            🔒 Reference
                                        </span>
                                    @elseif ($isBorrowed)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-500 text-white shadow-sm">
                                            ⏳ Borrowed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-400 text-emerald-950 shadow-sm">
                                            ✓ Available
                                        </span>
                                    @endif

                                    <span class="text-xs text-white/60 font-mono bg-black/30 px-2 py-0.5 rounded-md">
                                        {{ $book->published_year }}
                                    </span>
                                </div>

                                {{-- Center: Book title stylized on jacket --}}
                                <div class="my-auto z-10 relative px-6 py-2 border-y border-white/10">
                                    <h4 class="font-serif font-bold text-white text-base leading-snug line-clamp-2 drop-shadow-sm group-hover:text-amber-200 transition">
                                        {{ $book->title }}
                                    </h4>
                                    <p class="text-xs {{ $theme['text_accent'] }} mt-1 truncate font-medium">
                                        by {{ $book->author->name }}
                                    </p>
                                </div>

                                {{-- Bottom ISBN Bar --}}
                                <div class="z-10 relative flex items-center justify-between text-[11px] text-white/50 font-mono px-6 pb-4">
                                    <span>ISBN-13</span>
                                    <span>{{ $book->isbn }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- 📄 Book Details & Bottom Action Bar --}}
                        <div class="p-5 flex-1 flex flex-col justify-between bg-white">
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm leading-snug line-clamp-2 hover:text-indigo-600 transition">
                                    <a href="{{ route('books.show', $book) }}">
                                        {{ $book->title }}
                                    </a>
                                </h3>

                                <div class="mt-2 flex items-center space-x-2 text-xs text-gray-500">
                                    {{-- Author initial avatar --}}
                                    <span class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-[10px]">
                                        {{ substr($book->author->name, 0, 1) }}
                                    </span>
                                    <span class="truncate font-medium text-gray-700">{{ $book->author->name }}</span>
                                </div>
                            </div>

                            <div class="pt-4 mt-4 border-t border-gray-100 flex items-center justify-between">
                                <a href="{{ route('books.show', $book) }}"
                                   class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition flex items-center space-x-1">
                                    <span>Details</span>
                                    <span>→</span>
                                </a>

                                @auth
                                    @if (!$book->is_reference && !$isBorrowed)
                                        <form method="POST" action="{{ route('books.borrow', $book) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center space-x-1 px-3.5 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold rounded-xl shadow-sm transition transform active:scale-95">
                                                <span>📖</span>
                                                <span>Borrow</span>
                                            </button>
                                        </form>
                                    @elseif ($book->is_reference)
                                        <span class="text-[11px] text-amber-600 font-semibold bg-amber-50 px-2 py-0.5 rounded-md">
                                            Library use only
                                        </span>
                                    @elseif ($isBorrowed)
                                        <span class="text-[11px] text-rose-500 font-semibold bg-rose-50 px-2 py-0.5 rounded-md">
                                            On Loan
                                        </span>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="text-[11px] font-semibold text-gray-400 hover:text-indigo-600">
                                        Sign in to borrow
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-10">
                {{ $books->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
