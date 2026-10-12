<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    📚 Library Catalogue
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Explore our collection of books, monographs, and references.</p>
            </div>
            @can('create', App\Models\Book::class)
                <a href="{{ route('books.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wider hover:bg-indigo-700 active:bg-indigo-900 transition ease-in-out duration-150 shadow-sm">
                    + Add New Book
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Flash message --}}
        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-2">
                    <span class="text-emerald-500 text-lg">✓</span>
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif

        {{-- Search & Filter Bar --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-8">
            <form method="GET" action="{{ route('books.index') }}" class="flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Search by title, author, or ISBN..."
                           class="w-full ps-10 pe-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('books.index', ['search' => $search]) }}"
                       class="px-3.5 py-2 text-xs font-semibold rounded-lg transition {{ empty($filter) ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        All Books
                    </a>
                    <a href="{{ route('books.index', ['search' => $search, 'filter' => 'available']) }}"
                       class="px-3.5 py-2 text-xs font-semibold rounded-lg transition {{ $filter === 'available' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Available to Borrow
                    </a>
                    <a href="{{ route('books.index', ['search' => $search, 'filter' => 'reference']) }}"
                       class="px-3.5 py-2 text-xs font-semibold rounded-lg transition {{ $filter === 'reference' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Reference Only
                    </a>

                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold rounded-lg hover:bg-gray-700 transition">
                        Apply
                    </button>
                    @if($search || $filter)
                        <a href="{{ route('books.index') }}" class="px-3 py-2 text-xs text-gray-500 hover:text-gray-700">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Books Grid --}}
        @if ($books->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center max-w-lg mx-auto">
                <div class="text-4xl mb-3">🔍</div>
                <h3 class="text-lg font-bold text-gray-800">No books found</h3>
                <p class="text-sm text-gray-500 mt-1">Try adjusting your search query or filter options.</p>
                <a href="{{ route('books.index') }}" class="mt-4 inline-block text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    Reset catalogue
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($books as $book)
                    @php
                        $isBorrowed = $book->active_loans_count > 0;
                    @endphp
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 overflow-hidden flex flex-col transition duration-200">
                        {{-- Book Card Header / Cover Mockup --}}
                        <div class="h-44 bg-gradient-to-br from-indigo-900 via-indigo-800 to-slate-900 p-5 flex flex-col justify-between relative overflow-hidden text-white">
                            <div class="absolute -right-6 -bottom-6 text-white/5 font-black text-8xl select-none pointer-events-none">
                                📖
                            </div>
                            <div class="flex items-start justify-between z-10">
                                @if ($book->is_reference)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-400 text-amber-950 shadow-sm">
                                        Reference Only
                                    </span>
                                @elseif ($isBorrowed)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500 text-white shadow-sm">
                                        Currently Borrowed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-400 text-emerald-950 shadow-sm">
                                        Available
                                    </span>
                                @endif

                                <span class="text-xs text-indigo-200 font-mono">{{ $book->published_year }}</span>
                            </div>

                            <div class="z-10 mt-auto">
                                <span class="text-xs text-indigo-300 font-medium block truncate">ISBN: {{ $book->isbn }}</span>
                            </div>
                        </div>

                        {{-- Book Details --}}
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-gray-900 text-base leading-snug line-clamp-2 hover:text-indigo-600 transition">
                                    <a href="{{ route('books.show', $book) }}">
                                        {{ $book->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 mt-1 flex items-center space-x-1">
                                    <span class="text-gray-400">by</span>
                                    <span class="font-medium text-gray-700 truncate">{{ $book->author->name }}</span>
                                </p>
                            </div>

                            <div class="pt-4 mt-4 border-t border-gray-100 flex items-center justify-between">
                                <a href="{{ route('books.show', $book) }}"
                                   class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                    View Details →
                                </a>

                                @auth
                                    @if (!$book->is_reference && !$isBorrowed)
                                        <form method="POST" action="{{ route('books.borrow', $book) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                                                Borrow
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $books->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
