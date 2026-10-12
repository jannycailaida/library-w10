<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                📖 Book Details
            </h2>
            <a href="{{ route('books.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                ← Back to Catalogue
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Flash message / Errors --}}
        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center space-x-2 shadow-sm">
                <span class="text-emerald-500 text-lg">✓</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-3">
                {{-- Left: Cover showcase --}}
                <div class="bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-900 p-8 flex flex-col justify-between text-white relative">
                    <div>
                        @if ($book->is_reference)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-400 text-amber-950 shadow-sm">
                                🔒 Reference Only
                            </span>
                        @elseif ($isBorrowed)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-500 text-white shadow-sm">
                                ⏳ Currently Borrowed
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-400 text-emerald-950 shadow-sm">
                                ✓ Available for Loan
                            </span>
                        @endif
                    </div>

                    <div class="my-12 text-center">
                        <div class="text-7xl mb-4">📖</div>
                        <h4 class="font-mono text-sm text-indigo-300">ISBN-13</h4>
                        <p class="font-mono font-bold text-white text-base tracking-widest mt-0.5">{{ $book->isbn }}</p>
                    </div>

                    <div class="text-xs text-indigo-300">
                        Published Year: <span class="text-white font-semibold">{{ $book->published_year }}</span>
                    </div>
                </div>

                {{-- Right: Book Information & Actions --}}
                <div class="p-8 md:col-span-2 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-md">
                            Library Item
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-3 leading-tight">
                            {{ $book->title }}
                        </h1>
                        <p class="text-base text-gray-600 mt-2">
                            Written by <span class="font-semibold text-gray-900">{{ $book->author->name }}</span>
                        </p>

                        <div class="mt-8 border-t border-gray-100 pt-6 space-y-4">
                            <div class="flex justify-between text-sm py-2 border-b border-gray-50">
                                <span class="text-gray-500">Circulation Policy</span>
                                <span class="font-medium text-gray-900">
                                    {{ $book->is_reference ? 'In-library reference only (cannot borrow)' : 'Standard loan allowed' }}
                                </span>
                            </div>
                            <div class="flex justify-between text-sm py-2 border-b border-gray-50">
                                <span class="text-gray-500">Current Status</span>
                                <span class="font-semibold {{ $isBorrowed ? 'text-rose-600' : ($book->is_reference ? 'text-amber-600' : 'text-emerald-600') }}">
                                    @if ($book->is_reference)
                                        Reference Library
                                    @elseif ($isBorrowed)
                                        Checked Out / On Loan
                                    @else
                                        On Shelf (Ready to Borrow)
                                    @endif
                                </span>
                            </div>
                        </div>

                        {{-- Borrow Action Box --}}
                        <div class="mt-8 p-6 rounded-2xl bg-gray-50 border border-gray-100">
                            @if ($book->is_reference)
                                <div class="flex items-start space-x-3 text-amber-800">
                                    <span class="text-xl">⚠️</span>
                                    <div>
                                        <h4 class="font-bold text-sm">Reference Material Only</h4>
                                        <p class="text-xs text-amber-700 mt-0.5">This book is marked as a reference item and cannot be checked out for home loans.</p>
                                    </div>
                                </div>
                            @elseif ($isBorrowed)
                                <div class="flex items-start space-x-3 text-rose-800">
                                    <span class="text-xl">⏳</span>
                                    <div>
                                        <h4 class="font-bold text-sm">Currently Unavailable</h4>
                                        <p class="text-xs text-rose-700 mt-0.5">This copy has already been checked out by another library member.</p>
                                    </div>
                                </div>
                            @else
                                @auth
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                        <div>
                                            <h4 class="font-bold text-sm text-gray-900">Ready to borrow?</h4>
                                            <p class="text-xs text-gray-500">This book will be assigned to your member record ({{ Auth::user()->name }}).</p>
                                        </div>
                                        <form method="POST" action="{{ route('books.borrow', $book) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow transition">
                                                Borrow Book Now
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="text-center py-2">
                                        <p class="text-sm text-gray-600 mb-3">Please sign in to borrow this book.</p>
                                        <div class="flex items-center justify-center space-x-3">
                                            <a href="{{ route('login') }}" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition">
                                                Log in
                                            </a>
                                            <a href="{{ route('register') }}" class="px-4 py-2 bg-gray-200 text-gray-800 text-xs font-bold rounded-lg hover:bg-gray-300 transition">
                                                Register
                                            </a>
                                        </div>
                                    </div>
                                @endauth
                            @endif
                        </div>
                    </div>

                    {{-- Staff Controls (Librarians & Admins) --}}
                    @can('update', $book)
                        <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Staff Tools:</span>
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('books.edit', $book) }}"
                                   class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                                    ✏️ Edit Book
                                </a>

                                @can('delete', $book)
                                    <form method="POST" action="{{ route('books.destroy', $book) }}" onsubmit="return confirm('Are you sure you want to delete this book?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold rounded-lg transition">
                                            🗑️ Delete
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
