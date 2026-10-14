<x-app-layout>
    <div x-data="{
        quickBook: null,
        isModalOpen: false,
        openQuickView(bookData) {
            this.quickBook = bookData;
            this.isModalOpen = true;
        },
        closeQuickView() {
            this.isModalOpen = false;
            this.quickBook = null;
        }
    }" @keydown.escape.window="closeQuickView()" class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Flash message --}}
        @if (session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 flex items-center justify-between shadow-sm animate-fade-in">
                <div class="flex items-center space-x-3">
                    <span class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm">✓</span>
                    <span class="font-medium text-sm">{{ session('status') }}</span>
                </div>
            </div>
        @endif

        {{-- 👤 Personalized Student Welcome Banner (Authenticated Members Only) --}}
        @auth
            @if(auth()->user()->isMember())
                <div class="mb-6 bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 border border-indigo-100 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                    <div class="flex items-center space-x-3">
                        <span class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-lg font-bold shadow-sm">
                            🎓
                        </span>
                        <div>
                            <div class="text-sm font-bold text-gray-900">
                                Welcome back, {{ auth()->user()->name }}!
                            </div>
                            <div class="text-xs text-gray-600">
                                @if($userActiveLoansCount > 0)
                                    You currently have <span class="font-bold text-indigo-700">{{ $userActiveLoansCount }}</span> active borrowed book(s).
                                @else
                                    You have no books currently checked out. Ready to borrow a new title?
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="shrink-0">
                        <a href="{{ route('my.loans') }}"
                           class="inline-flex items-center space-x-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                            <span>📖</span>
                            <span>My Active Loans</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            @endif
        @endauth

        {{-- 🏛️ Modern Hero Banner --}}
        <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 text-white p-8 sm:p-12 mb-8 shadow-xl border border-indigo-900/40">
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

                {{-- 📚 Right Side: 3D Floating Book Showcase with Glow --}}
                <div class="relative shrink-0 flex flex-col items-center lg:items-end justify-center pt-8 lg:pt-0">
                    {{-- Ambient circular glow behind the book stack --}}
                    <div class="absolute w-72 h-72 rounded-full bg-gradient-to-tr from-indigo-500/25 via-purple-500/25 to-pink-500/15 blur-3xl pointer-events-none"></div>

                    {{-- 3D Overlapping Floating Book Stack --}}
                    <div class="relative flex items-center justify-center py-6 px-4 [perspective:1000px] select-none group">

                        {{-- Floating Top Pill --}}
                        <div class="absolute -top-1 right-2 z-30 inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md border border-white/25 text-white text-[11px] font-bold shadow-lg shadow-black/30">
                            <span class="text-amber-300">✦</span>
                            <span>Featured Student Titles</span>
                        </div>

                        {{-- Book 1 (Left - Algorithms, tilted -12deg) --}}
                        <div class="relative transform -rotate-12 translate-x-5 hover:-translate-y-2 hover:-rotate-6 transition-all duration-300 z-10 cursor-pointer">
                            <div class="w-24 sm:w-28 h-36 sm:h-40 rounded-xl overflow-hidden shadow-2xl border-2 border-white/20 bg-slate-900">
                                <img src="{{ asset('images/books/isbn_9780262046305.jpg') }}"
                                     alt="Introduction to Algorithms"
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="absolute -bottom-2 -left-2 bg-indigo-900/90 backdrop-blur-sm text-[9px] font-bold text-indigo-200 px-2 py-0.5 rounded-md border border-indigo-400/30 shadow">
                                Algorithms
                            </div>
                        </div>

                        {{-- Book 2 (Center - Clean Code, elevated, prominent) --}}
                        <div class="relative transform scale-110 hover:scale-115 -translate-y-2 transition-all duration-300 z-20 cursor-pointer">
                            <div class="w-28 sm:w-32 h-40 sm:h-48 rounded-2xl overflow-hidden shadow-2xl shadow-indigo-950 border-2 border-amber-400/50 bg-slate-900 relative">
                                <img src="{{ asset('images/books/isbn_9780132350884.jpg') }}"
                                     alt="Clean Code"
                                     class="w-full h-full object-cover">
                                {{-- Bookmark ribbon --}}
                                <div class="absolute top-0 right-3 w-3 h-6 bg-amber-400 [clip-path:polygon(0_0,100%_0,100%_100%,50%_75%,0_100%)] shadow"></div>
                            </div>
                            <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-amber-400 text-amber-950 text-[10px] font-black px-2.5 py-0.5 rounded-full shadow-md whitespace-nowrap">
                                ★ Most Borrowed
                            </div>
                        </div>

                        {{-- Book 3 (Right - Calculus, tilted +12deg) --}}
                        <div class="relative transform rotate-12 -translate-x-5 hover:-translate-y-2 hover:rotate-6 transition-all duration-300 z-10 cursor-pointer">
                            <div class="w-24 sm:w-28 h-36 sm:h-40 rounded-xl overflow-hidden shadow-2xl border-2 border-white/20 bg-slate-900">
                                <img src="{{ asset('images/books/isbn_9781337613927.jpg') }}"
                                     alt="Calculus"
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="absolute -bottom-2 -right-2 bg-purple-900/90 backdrop-blur-sm text-[9px] font-bold text-purple-200 px-2 py-0.5 rounded-md border border-purple-400/30 shadow">
                                Calculus
                            </div>
                        </div>

                        {{-- Floating Bottom Pill --}}
                        <div class="absolute -bottom-2 left-2 z-30 inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-slate-900/90 backdrop-blur-md border border-emerald-400/40 text-emerald-300 text-[11px] font-bold shadow-xl">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>14-Day Free Loan</span>
                        </div>
                    </div>

                    {{-- Staff Action Button --}}
                    @can('create', App\Models\Book::class)
                        <div class="mt-4 z-20">
                            <a href="{{ route('books.create') }}"
                               class="inline-flex items-center space-x-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5 active:translate-y-0 border border-indigo-400/30">
                                <span class="text-base">+</span>
                                <span>Add New Book to Shelf</span>
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>

        {{-- 🏷️ Academic Subject Categories Filter Bar --}}
        <div class="mb-6 overflow-x-auto pb-2 scrollbar-thin">
            <div class="flex items-center space-x-2 min-w-max">
                @php
                    $categories = [
                        ['slug' => 'all', 'name' => 'All Subjects', 'icon' => '🌐'],
                        ['slug' => 'cs', 'name' => 'Computer Science', 'icon' => '💻'],
                        ['slug' => 'math', 'name' => 'Mathematics', 'icon' => '📐'],
                        ['slug' => 'physics', 'name' => 'Physics & Engineering', 'icon' => '🔬'],
                        ['slug' => 'biology', 'name' => 'Biology & Life', 'icon' => '🧬'],
                        ['slug' => 'economics', 'name' => 'Economics & Business', 'icon' => '📈'],
                        ['slug' => 'literature', 'name' => 'Literature & Classics', 'icon' => '📚'],
                        ['slug' => 'reference', 'name' => 'Reference Collection', 'icon' => '🔒'],
                    ];
                @endphp

                @foreach($categories as $cat)
                    @php
                        $isActive = ($category ?? 'all') === $cat['slug'];
                        $count = $categoryCounts[$cat['slug']] ?? 0;
                    @endphp
                    <a href="{{ route('books.index', array_merge(request()->query(), ['category' => $cat['slug'], 'page' => 1])) }}"
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-2 {{ $isActive ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                        <span>{{ $cat['icon'] }}</span>
                        <span>{{ $cat['name'] }}</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $isActive ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }}">
                            {{ $count }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- 🔍 Search & Filter Toolbar --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-5 mb-8">
            <form method="GET" action="{{ route('books.index') }}" class="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center justify-between">
                {{-- Preserve Category --}}
                <input type="hidden" name="category" value="{{ $category ?? 'all' }}">

                {{-- Search Input with clear icon --}}
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Search by title, author, or 13-digit ISBN..."
                           class="w-full ps-11 pe-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition placeholder:text-gray-400">
                </div>

                {{-- Availability & Sorting controls --}}
                <div class="flex items-center gap-2 flex-wrap justify-between lg:justify-end">
                    {{-- Availability Pills --}}
                    <div class="inline-flex rounded-xl p-1 bg-gray-100 border border-gray-200 text-xs font-semibold">
                        <a href="{{ route('books.index', array_merge(request()->query(), ['filter' => null, 'page' => 1])) }}"
                           class="px-3 py-1.5 rounded-lg transition {{ empty($filter) ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900' }}">
                            All
                        </a>
                        <a href="{{ route('books.index', array_merge(request()->query(), ['filter' => 'available', 'page' => 1])) }}"
                           class="px-3 py-1.5 rounded-lg transition flex items-center space-x-1 {{ $filter === 'available' ? 'bg-emerald-600 text-white shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $filter === 'available' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                            <span>Available ({{ $availableCount }})</span>
                        </a>
                        <a href="{{ route('books.index', array_merge(request()->query(), ['filter' => 'reference', 'page' => 1])) }}"
                           class="px-3 py-1.5 rounded-lg transition flex items-center space-x-1 {{ $filter === 'reference' ? 'bg-amber-600 text-white shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900' }}">
                            <span>🔒 Reference ({{ $referenceCount }})</span>
                        </a>
                    </div>

                    {{-- Sort Dropdown --}}
                    <div class="relative">
                        <select name="sort" onchange="this.form.submit()"
                                class="pe-8 ps-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 cursor-pointer">
                            <option value="title_asc" {{ ($sort ?? '') === 'title_asc' ? 'selected' : '' }}>🔤 Title (A - Z)</option>
                            <option value="title_desc" {{ ($sort ?? '') === 'title_desc' ? 'selected' : '' }}>🔤 Title (Z - A)</option>
                            <option value="newest" {{ ($sort ?? '') === 'newest' ? 'selected' : '' }}>📅 Newest Edition</option>
                            <option value="oldest" {{ ($sort ?? '') === 'oldest' ? 'selected' : '' }}>⏳ Oldest Edition</option>
                        </select>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-xl transition shadow-sm">
                        Filter
                    </button>

                    @if($search || $filter || ($category && $category !== 'all'))
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
                <p class="text-sm text-gray-500 mt-1">We couldn't find any books matching your search query or selected subject.</p>
                <a href="{{ route('books.index') }}" class="mt-6 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition">
                    View All Books
                </a>
            </div>
        @else
            @php
                $themes = [
                    ['bg' => 'from-slate-950 via-blue-950 to-indigo-950', 'spine' => 'border-blue-900', 'ribbon' => 'bg-blue-400', 'text_accent' => 'text-blue-300'],
                    ['bg' => 'from-slate-950 via-emerald-950 to-teal-950', 'spine' => 'border-emerald-900', 'ribbon' => 'bg-emerald-400', 'text_accent' => 'text-emerald-300'],
                    ['bg' => 'from-slate-950 via-rose-950 to-red-950', 'spine' => 'border-rose-900', 'ribbon' => 'bg-rose-400', 'text_accent' => 'text-rose-300'],
                    ['bg' => 'from-slate-950 via-purple-950 to-indigo-950', 'spine' => 'border-purple-900', 'ribbon' => 'bg-purple-400', 'text_accent' => 'text-purple-300'],
                    ['bg' => 'from-stone-950 via-amber-950 to-yellow-950', 'spine' => 'border-amber-900', 'ribbon' => 'bg-amber-400', 'text_accent' => 'text-amber-300'],
                    ['bg' => 'from-neutral-950 via-slate-900 to-stone-950', 'spine' => 'border-slate-900', 'ribbon' => 'bg-slate-400', 'text_accent' => 'text-slate-300'],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-7">
                @foreach ($books as $book)
                    @php
                        $isBorrowed = $book->active_loans_count > 0;
                        $theme = $themes[$book->id % count($themes)];
                        $bookJson = json_encode([
                            'id' => $book->id,
                            'title' => $book->title,
                            'author' => $book->author->name,
                            'isbn' => $book->isbn,
                            'year' => $book->published_year,
                            'cover_path' => $book->cover_path ? asset($book->cover_path) : null,
                            'is_reference' => $book->is_reference,
                            'is_borrowed' => $isBorrowed,
                            'category_name' => $book->category_name,
                            'category_icon' => $book->category_icon,
                            'show_url' => route('books.show', $book),
                            'borrow_url' => route('books.borrow', $book),
                        ]);
                    @endphp

                    <div class="group bg-white rounded-2xl shadow-sm hover:shadow-2xl hover:-translate-y-2 border border-gray-100 overflow-hidden flex flex-col transition-all duration-300">
                        {{-- 📖 Realistic Hardcover Book Spine & Jacket Mockup --}}
                        <div class="h-64 bg-slate-950 relative overflow-hidden select-none border-l-4 {{ $theme['spine'] }} shadow-inner flex flex-col justify-between">
                            @if ($book->cover_path)
                                {{-- Real Book Cover Photo --}}
                                <img src="{{ asset($book->cover_path) }}"
                                     alt="{{ $book->title }}"
                                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                                {{-- Soft top gradient for badge readability --}}
                                <div class="absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-black/75 to-transparent pointer-events-none"></div>

                                {{-- Hardcover spine shadow crease --}}
                                <div class="absolute left-0 top-0 bottom-0 w-3 bg-gradient-to-r from-black/70 via-black/30 to-transparent pointer-events-none z-10"></div>

                                {{-- Quick View Hover Button --}}
                                <button type="button"
                                        @click='openQuickView({!! $bookJson !!})'
                                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-200 z-30 cursor-pointer">
                                    <span class="px-4 py-2 bg-white/95 backdrop-blur-sm rounded-full text-xs font-bold text-gray-900 shadow-xl flex items-center space-x-1.5 transform translate-y-2 group-hover:translate-y-0 transition-transform">
                                        <span>👁️</span>
                                        <span>Quick View</span>
                                    </span>
                                </button>

                                {{-- Bookmark ribbon for Available Books --}}
                                @if (!$book->is_reference && !$isBorrowed)
                                    <div class="absolute top-0 right-4 w-4 h-8 bg-amber-400 shadow-md [clip-path:polygon(0_0,100%_0,100%_100%,50%_75%,0_100%)] z-20"></div>
                                @endif

                                {{-- Top Header on Book Cover: Status badge + Year --}}
                                <div class="flex items-start justify-between z-10 relative p-3.5">
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
                                {{-- Subject Pill --}}
                                <div class="mb-2">
                                    <span class="inline-flex items-center space-x-1 text-[11px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md">
                                        <span>{{ $book->category_icon }}</span>
                                        <span>{{ $book->category_name }}</span>
                                    </span>
                                </div>

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
                                <button type="button"
                                        @click='openQuickView({!! $bookJson !!})'
                                        class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition flex items-center space-x-1">
                                    <span>Quick View</span>
                                    <span>→</span>
                                </button>

                                @auth
                                    @if (!$book->is_reference && !$isBorrowed)
                                        <form method="POST" action="{{ route('books.borrow', $book) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center space-x-1.5 px-4 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition transform active:scale-95">
                                                <span>📖</span>
                                                <span>Borrow</span>
                                            </button>
                                        </form>
                                    @elseif ($book->is_reference)
                                        <span class="inline-flex items-center space-x-1 px-3 py-1.5 text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300 rounded-xl">
                                            <span>🔒</span>
                                            <span>Reference</span>
                                        </span>
                                    @elseif ($isBorrowed)
                                        <span class="inline-flex items-center space-x-1 px-3 py-1.5 text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300 rounded-xl">
                                            <span>⏳</span>
                                            <span>Borrowed</span>
                                        </span>
                                    @endif
                                @else
                                    @if ($book->is_reference)
                                        <span class="inline-flex items-center space-x-1 px-3 py-1.5 text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300 rounded-xl">
                                            <span>🔒</span>
                                            <span>Reference</span>
                                        </span>
                                    @elseif ($isBorrowed)
                                        <span class="inline-flex items-center space-x-1 px-3 py-1.5 text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300 rounded-xl">
                                            <span>⏳</span>
                                            <span>Borrowed</span>
                                        </span>
                                    @else
                                        <a href="{{ route('login') }}" class="inline-flex items-center space-x-1 px-3.5 py-1.5 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-xl transition">
                                            <span>Sign In to Borrow</span>
                                        </a>
                                    @endif
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

        {{-- 🔍 Alpine.js Quick Book Preview Modal --}}
        <div x-show="isModalOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">

            <div @click.away="closeQuickView()"
                 x-show="isModalOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                 class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full overflow-hidden border border-gray-100 relative">

                {{-- Close Button --}}
                <button type="button"
                        @click="closeQuickView()"
                        class="absolute top-4 right-4 z-20 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center text-sm font-bold transition">
                    ✕
                </button>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-0" x-if="quickBook">
                    {{-- Cover Left Column --}}
                    <div class="md:col-span-5 bg-slate-950 p-6 flex items-center justify-center relative overflow-hidden">
                        <template x-if="quickBook && quickBook.cover_path">
                            <img :src="quickBook.cover_path"
                                 :alt="quickBook.title"
                                 class="w-full max-h-[320px] object-cover rounded-xl shadow-2xl border border-white/20">
                        </template>
                        <template x-if="quickBook && !quickBook.cover_path">
                            <div class="p-8 text-center text-white">
                                <span class="text-6xl">📖</span>
                                <div class="mt-4 font-bold text-sm" x-text="quickBook ? quickBook.title : ''"></div>
                            </div>
                        </template>
                    </div>

                    {{-- Info Right Column --}}
                    <div class="md:col-span-7 p-6 sm:p-8 flex flex-col justify-between">
                        <div>
                            {{-- Category and Status Badges --}}
                            <div class="flex items-center gap-2 flex-wrap mb-3">
                                <span class="inline-flex items-center space-x-1 text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg">
                                    <span x-text="quickBook ? quickBook.category_icon : ''"></span>
                                    <span x-text="quickBook ? quickBook.category_name : ''"></span>
                                </span>

                                <template x-if="quickBook && quickBook.is_reference">
                                    <span class="text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300 px-2.5 py-1 rounded-lg">
                                        🔒 Reference
                                    </span>
                                </template>
                                <template x-if="quickBook && !quickBook.is_reference && quickBook.is_borrowed">
                                    <span class="text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300 px-2.5 py-1 rounded-lg">
                                        ⏳ Borrowed
                                    </span>
                                </template>
                                <template x-if="quickBook && !quickBook.is_reference && !quickBook.is_borrowed">
                                    <span class="text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 px-2.5 py-1 rounded-lg">
                                        ✓ Available
                                    </span>
                                </template>
                            </div>

                            <h3 class="text-xl font-extrabold text-gray-900 leading-snug" x-text="quickBook ? quickBook.title : ''"></h3>
                            <p class="text-sm font-medium text-gray-600 mt-1">
                                Author: <span class="text-gray-900 font-bold" x-text="quickBook ? quickBook.author : ''"></span>
                            </p>

                            {{-- Metadata stats --}}
                            <div class="mt-4 grid grid-cols-2 gap-3 p-3.5 bg-gray-50 rounded-2xl text-xs">
                                <div>
                                    <span class="text-gray-400 block font-medium">Published Year</span>
                                    <span class="font-bold text-gray-900 text-sm font-mono" x-text="quickBook ? quickBook.year : ''"></span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block font-medium">ISBN-13</span>
                                    <span class="font-bold text-gray-900 text-sm font-mono truncate block" x-text="quickBook ? quickBook.isbn : ''"></span>
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-indigo-50/60 rounded-xl border border-indigo-100/60 text-xs text-indigo-900 flex items-start space-x-2">
                                <span class="text-sm">ℹ️</span>
                                <span class="leading-relaxed">Standard university 14-day checkout period for students. Reference volumes are reserved for in-library reading.</span>
                            </div>
                        </div>

                        {{-- Modal Bottom Actions --}}
                        <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-between gap-3">
                            <a :href="quickBook ? quickBook.show_url : '#'"
                               class="text-xs font-bold text-gray-600 hover:text-indigo-600 transition flex items-center space-x-1">
                                <span>Full Details Page</span>
                                <span>→</span>
                            </a>

                            @auth
                                <template x-if="quickBook && !quickBook.is_reference && !quickBook.is_borrowed">
                                    <form method="POST" :action="quickBook ? quickBook.borrow_url : '#'">
                                        @csrf
                                        <button type="submit"
                                                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition transform active:scale-95">
                                            📖 Borrow Now
                                        </button>
                                    </form>
                                </template>
                                <template x-if="quickBook && quickBook.is_reference">
                                    <span class="inline-flex items-center space-x-1 px-3 py-1.5 text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300 rounded-xl">
                                        <span>🔒</span>
                                        <span>Reference</span>
                                    </span>
                                </template>
                                <template x-if="quickBook && !quickBook.is_reference && quickBook.is_borrowed">
                                    <span class="inline-flex items-center space-x-1 px-3 py-1.5 text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300 rounded-xl">
                                        <span>⏳</span>
                                        <span>Borrowed</span>
                                    </span>
                                </template>
                            @else
                                <template x-if="quickBook && !quickBook.is_reference && !quickBook.is_borrowed">
                                    <a href="{{ route('login') }}"
                                       class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition">
                                        Sign In to Borrow
                                    </a>
                                </template>
                                <template x-if="quickBook && quickBook.is_reference">
                                    <span class="inline-flex items-center space-x-1 px-3 py-1.5 text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300 rounded-xl">
                                        <span>🔒</span>
                                        <span>Reference</span>
                                    </span>
                                </template>
                                <template x-if="quickBook && !quickBook.is_reference && quickBook.is_borrowed">
                                    <span class="inline-flex items-center space-x-1 px-3 py-1.5 text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300 rounded-xl">
                                        <span>⏳</span>
                                        <span>Borrowed</span>
                                    </span>
                                </template>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
