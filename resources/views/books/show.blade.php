<x-app-layout>
    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb Navigation --}}
        <div class="mb-6 flex items-center space-x-2 text-xs font-semibold text-gray-500">
            <a href="{{ route('books.index') }}" class="hover:text-indigo-600 transition flex items-center space-x-1">
                <span>📚</span>
                <span>Catalogue</span>
            </a>
            <span>/</span>
            <span class="text-gray-900 truncate max-w-xs sm:max-w-md">{{ $book->title }}</span>
        </div>

        {{-- Flash message / Errors --}}
        @if (session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 flex items-center space-x-3 shadow-sm">
                <span class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm">✓</span>
                <span class="font-medium text-sm">{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $themes = [
                ['bg' => 'from-slate-950 via-blue-950 to-indigo-950', 'spine' => 'border-blue-900', 'ribbon' => 'bg-amber-400', 'text_accent' => 'text-blue-300'],
                ['bg' => 'from-slate-950 via-emerald-950 to-teal-950', 'spine' => 'border-emerald-900', 'ribbon' => 'bg-amber-400', 'text_accent' => 'text-emerald-300'],
                ['bg' => 'from-slate-950 via-rose-950 to-red-950', 'spine' => 'border-rose-900', 'ribbon' => 'bg-amber-400', 'text_accent' => 'text-rose-300'],
                ['bg' => 'from-slate-950 via-purple-950 to-indigo-950', 'spine' => 'border-purple-900', 'ribbon' => 'bg-amber-400', 'text_accent' => 'text-purple-300'],
                ['bg' => 'from-stone-950 via-amber-950 to-yellow-950', 'spine' => 'border-amber-900', 'ribbon' => 'bg-amber-400', 'text_accent' => 'text-amber-300'],
                ['bg' => 'from-neutral-950 via-slate-900 to-stone-950', 'spine' => 'border-slate-900', 'ribbon' => 'bg-amber-400', 'text_accent' => 'text-slate-300'],
            ];
            $theme = $themes[$book->id % count($themes)];
        @endphp

        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-0">
                {{-- 📖 Left: 3D Hardcover Book Showcase (5 cols) --}}
                <div class="md:col-span-5 bg-gradient-to-br {{ $theme['bg'] }} p-8 sm:p-10 flex flex-col justify-between text-white relative border-l-8 {{ $theme['spine'] }} shadow-inner">
                    {{-- Left spine crease shadow --}}
                    <div class="absolute left-0 top-0 bottom-0 w-4 bg-gradient-to-r from-black/60 via-black/25 to-transparent pointer-events-none"></div>

                    {{-- Golden bookmark ribbon --}}
                    @if (!$book->is_reference && !$isBorrowed)
                        <div class="absolute top-0 right-8 w-5 h-10 bg-amber-400 shadow-lg [clip-path:polygon(0_0,100%_0,100%_100%,50%_75%,0_100%)] z-20"></div>
                    @endif

                    <div class="flex items-center justify-between z-10 relative">
                        @if ($book->is_reference)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-400 text-amber-950 shadow-md">
                                🔒 Reference Material
                            </span>
                        @elseif ($isBorrowed)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-500 text-white shadow-md">
                                ⏳ Currently on Loan
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-400 text-emerald-950 shadow-md">
                                ✓ Available for Borrowing
                            </span>
                        @endif

                        <span class="text-xs text-white/70 font-mono bg-black/40 px-2.5 py-1 rounded-lg">
                            {{ $book->published_year }}
                        </span>
                    </div>

                    {{-- Stylized Hardcover Center --}}
                    <div class="my-12 z-10 relative py-6 border-y border-white/15">
                        <div class="text-5xl mb-4 opacity-20">📖</div>
                        <h2 class="font-serif font-black text-2xl sm:text-3xl text-white leading-tight drop-shadow-md">
                            {{ $book->title }}
                        </h2>
                        <p class="text-sm {{ $theme['text_accent'] }} font-semibold mt-2 tracking-wide">
                            by {{ $book->author->name }}
                        </p>
                    </div>

                    <div class="z-10 relative flex items-center justify-between text-xs text-white/60 font-mono pt-4 border-t border-white/10">
                        <span>ISBN-13</span>
                        <span class="text-white font-bold tracking-wider">{{ $book->isbn }}</span>
                    </div>
                </div>

                {{-- 📄 Right: Detailed Metadata & Circulation Actions (7 cols) --}}
                <div class="md:col-span-7 p-8 sm:p-10 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-md">
                                Book Details
                            </span>
                            <span class="text-xs font-medium text-gray-400">Library ID: #{{ $book->id }}</span>
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-3 leading-snug">
                            {{ $book->title }}
                        </h1>

                        <div class="mt-3 flex items-center space-x-2 text-sm text-gray-600">
                            <span class="text-gray-400">Written by</span>
                            <span class="font-bold text-gray-800 text-base">{{ $book->author->name }}</span>
                        </div>

                        {{-- Metadata Specification Grid --}}
                        <div class="mt-8 grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                                <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold block">Publication Year</span>
                                <span class="text-base font-bold text-gray-800 mt-0.5 block">{{ $book->published_year }}</span>
                            </div>

                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                                <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold block">Circulation Policy</span>
                                <span class="text-base font-bold {{ $book->is_reference ? 'text-amber-700' : 'text-emerald-700' }} mt-0.5 block">
                                    {{ $book->is_reference ? 'Reference (In-Library)' : 'Borrowable Loan' }}
                                </span>
                            </div>
                        </div>

                        {{-- Circulation Checkout Card --}}
                        <div class="mt-8 p-6 rounded-2xl {{ $book->is_reference ? 'bg-amber-50 border border-amber-200' : ($isBorrowed ? 'bg-rose-50 border border-rose-200' : 'bg-indigo-50/60 border border-indigo-100') }}">
                            @if ($book->is_reference)
                                <div class="flex items-start space-x-3 text-amber-900">
                                    <span class="text-2xl">🔒</span>
                                    <div>
                                        <h4 class="font-bold text-base">In-Library Reference Only</h4>
                                        <p class="text-xs text-amber-700 mt-1 leading-relaxed">
                                            This book is part of the non-circulating reference collection. You may consult this text inside the library reading rooms, but it cannot be checked out for home loans.
                                        </p>
                                    </div>
                                </div>
                            @elseif ($isBorrowed)
                                <div class="flex items-start space-x-3 text-rose-900">
                                    <span class="text-2xl">⏳</span>
                                    <div>
                                        <h4 class="font-bold text-base">Currently Checked Out</h4>
                                        <p class="text-xs text-rose-700 mt-1 leading-relaxed">
                                            This copy is currently in possession of another member. Please check back once the book is returned to the circulation desk.
                                        </p>
                                    </div>
                                </div>
                            @else
                                @auth
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div>
                                            <h4 class="font-bold text-base text-gray-900">Ready to borrow this copy?</h4>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                Will be registered under card: <span class="font-semibold text-gray-700">{{ Auth::user()->name }}</span>
                                            </p>
                                        </div>

                                        <form method="POST" action="{{ route('books.borrow', $book) }}" class="shrink-0">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-600/20 transition transform active:scale-95">
                                                <span>📖</span>
                                                <span>Borrow Book</span>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="text-center py-2">
                                        <h4 class="font-bold text-base text-gray-900">Want to borrow this book?</h4>
                                        <p class="text-xs text-gray-500 mt-1 mb-4">Please log in to your library account to start borrowing.</p>
                                        <div class="flex items-center justify-center space-x-3">
                                            <a href="{{ route('login') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow transition">
                                                Sign In
                                            </a>
                                            <a href="{{ route('register') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-xl hover:bg-gray-50 transition">
                                                Register Account
                                            </a>
                                        </div>
                                    </div>
                                @endauth
                            @endif
                        </div>
                    </div>

                    {{-- Staff Controls Toolbar --}}
                    @can('update', $book)
                        <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Librarian Tools</span>
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('books.edit', $book) }}"
                                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition flex items-center space-x-1">
                                    <span>✏️</span>
                                    <span>Edit</span>
                                </a>

                                @can('delete', $book)
                                    <form method="POST" action="{{ route('books.destroy', $book) }}" onsubmit="return confirm('Delete this book permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-xl transition flex items-center space-x-1">
                                            <span>🗑️</span>
                                            <span>Delete</span>
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
