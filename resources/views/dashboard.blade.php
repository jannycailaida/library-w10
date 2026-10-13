<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center space-x-2">
                    <span>📊</span>
                    <span>Dashboard</span>
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Welcome back to your library management portal.</p>
            </div>
            <div class="flex items-center space-x-2">
                @if ($user->isAdmin())
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 shadow-sm border border-purple-200">
                        👑 System Administrator
                    </span>
                @elseif ($user->isLibrarian())
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 shadow-sm border border-blue-200">
                        📚 Library Staff (Librarian)
                    </span>
                @else
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 shadow-sm border border-emerald-200">
                        👤 Library Member
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        {{-- Flash message --}}
        @if (session('status'))
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 flex items-center space-x-3 shadow-sm">
                <span class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm">✓</span>
                <span class="font-medium text-sm">{{ session('status') }}</span>
            </div>
        @endif

        {{-- 🏛️ Welcome Banner & Quick Identity Card --}}
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-lg border border-indigo-900/40 relative overflow-hidden">
            <div class="absolute -right-12 -top-12 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-white">
                        Hello, {{ $user->name }}! 👋
                    </h3>
                    <p class="text-indigo-200/80 text-sm mt-1">
                        @if ($user->hasRole('librarian', 'admin'))
                            You have staff circulation permissions. Monitor loans, manage inventory, and assist library patrons.
                        @else
                            Your library account is active. Explore our catalogue and manage your current borrowings below.
                        @endif
                    </p>
                    @if ($member)
                        <div class="mt-4 inline-flex items-center space-x-2 text-xs text-indigo-300 font-mono bg-white/10 px-3 py-1 rounded-lg">
                            <span>💳 Library Card No:</span>
                            <span class="text-white font-bold">#{{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    @endif
                </div>

                {{-- Quick Actions --}}
                <div class="flex items-center gap-3 flex-wrap">
                    <a href="{{ route('books.index') }}"
                       class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow transition">
                        📚 Browse Catalogue
                    </a>

                    @if ($user->hasRole('librarian', 'admin'))
                        <a href="{{ route('desk') }}"
                           class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow transition">
                            🏢 Circulation Desk
                        </a>
                        @can('create', App\Models\Book::class)
                            <a href="{{ route('books.create') }}"
                               class="px-4 py-2.5 bg-gray-800 hover:bg-gray-700 text-white font-bold text-xs rounded-xl shadow transition">
                                + Add Book
                            </a>
                        @endcan
                    @else
                        <a href="{{ route('my.loans') }}"
                           class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition">
                            📋 View My Loans
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- 📊 Key Statistics Widgets --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @if ($user->hasRole('librarian', 'admin'))
                {{-- Staff View Metrics --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl font-bold">
                        ⏳
                    </div>
                    <div>
                        <div class="text-2xl font-black text-rose-600">{{ $activeLoansCount }}</div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Library Loans</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold">
                        ✓
                    </div>
                    <div>
                        <div class="text-2xl font-black text-emerald-600">{{ $returnedLoansCount }}</div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Returned Records</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-bold">
                        📚
                    </div>
                    <div>
                        <div class="text-2xl font-black text-gray-900">{{ $totalBooks }}</div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Book Titles</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl font-bold">
                        👥
                    </div>
                    <div>
                        <div class="text-2xl font-black text-purple-600">{{ $totalMembers }}</div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Registered Members</div>
                    </div>
                </div>
            @else
                {{-- Member View Metrics --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl font-bold">
                        📖
                    </div>
                    <div>
                        <div class="text-2xl font-black text-rose-600">{{ $myActiveLoans->count() }}</div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">My Active Loans</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl font-bold">
                        📜
                    </div>
                    <div>
                        <div class="text-2xl font-black text-gray-900">{{ $myTotalLoansCount }}</div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Borrowed</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold">
                        ⚡
                    </div>
                    <div>
                        <div class="text-2xl font-black text-emerald-600">{{ $availableBooksCount }}</div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Available in Shelf</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-bold">
                        📚
                    </div>
                    <div>
                        <div class="text-2xl font-black text-gray-900">{{ $totalBooks }}</div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Library Collection</div>
                    </div>
                </div>
            @endif
        </div>

        {{-- 📑 Role Specific Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Left Column (7 cols) --}}
            <div class="lg:col-span-7 space-y-6">
                @if ($user->hasRole('librarian', 'admin'))
                    {{-- Staff: Recent Activity on Circulation Desk --}}
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">Circulation Desk Activity</h3>
                                <p class="text-xs text-gray-500">Recent checkouts across the library system.</p>
                            </div>
                            <a href="{{ route('desk') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                Open Desk →
                            </a>
                        </div>

                        @if ($recentLoans->isEmpty())
                            <p class="text-sm text-gray-500 py-6 text-center">No recent loan transactions found.</p>
                        @else
                            <div class="divide-y divide-gray-100">
                                @foreach ($recentLoans as $loan)
                                    <div class="py-3.5 flex items-center justify-between gap-4">
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('books.show', $loan->book_id) }}" class="font-bold text-sm text-gray-900 hover:text-indigo-600 truncate block">
                                                {{ $loan->book_title }}
                                            </a>
                                            <div class="text-xs text-gray-500 flex items-center space-x-1 mt-0.5">
                                                <span>Borrower:</span>
                                                <span class="font-medium text-gray-700">{{ $loan->member_name }}</span>
                                                <span>•</span>
                                                <span>{{ \Carbon\Carbon::parse($loan->borrowed_at)->diffForHumans() }}</span>
                                            </div>
                                        </div>

                                        <div class="shrink-0">
                                            @if ($loan->returned_at)
                                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                                    ✓ Returned
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-rose-100 text-rose-800">
                                                    ⏳ Active
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Member: Current Borrowed Books --}}
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">Currently Borrowed by You</h3>
                                <p class="text-xs text-gray-500">Books currently in your possession.</p>
                            </div>
                            <a href="{{ route('my.loans') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                View History →
                            </a>
                        </div>

                        @if ($myActiveLoans->isEmpty())
                            <div class="py-10 text-center">
                                <div class="text-4xl mb-3">📖</div>
                                <p class="text-sm font-bold text-gray-700">You don't have any active loans right now.</p>
                                <p class="text-xs text-gray-400 mt-1">Ready to start reading? Explore the catalogue and borrow books with one click!</p>
                                <a href="{{ route('books.index') }}" class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white font-bold text-xs rounded-xl shadow-sm hover:bg-indigo-700 transition">
                                    Find a Book to Read
                                </a>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($myActiveLoans as $book)
                                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-between gap-4">
                                        <div>
                                            <h4 class="font-bold text-sm text-gray-900">
                                                <a href="{{ route('books.show', $book) }}" class="hover:text-indigo-600">
                                                    {{ $book->title }}
                                                </a>
                                            </h4>
                                            <p class="text-xs text-gray-500 mt-0.5">by {{ $book->author->name }}</p>
                                            <div class="mt-2 text-[11px] text-gray-500">
                                                Borrowed on: <span class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($book->pivot->borrowed_at)->format('M d, Y') }}</span>
                                            </div>
                                        </div>

                                        <span class="shrink-0 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            ⏳ Active Loan
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Right Column (5 cols) --}}
            <div class="lg:col-span-5 space-y-6">
                {{-- Featured / Popular Books --}}
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-lg text-gray-900">Featured Collection</h3>
                        <a href="{{ route('books.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                            See all →
                        </a>
                    </div>

                    <div class="space-y-3">
                        @foreach ($featuredBooks as $fBook)
                            <a href="{{ route('books.show', $fBook) }}"
                               class="group p-3 rounded-2xl hover:bg-gray-50 transition border border-transparent hover:border-gray-200 flex items-center justify-between">
                                <div class="min-w-0 pr-3">
                                    <h4 class="font-bold text-sm text-gray-800 group-hover:text-indigo-600 truncate transition">
                                        {{ $fBook->title }}
                                    </h4>
                                    <p class="text-xs text-gray-500 truncate">by {{ $fBook->author->name }}</p>
                                </div>
                                <div class="shrink-0 text-xs font-bold text-indigo-600 group-hover:translate-x-1 transition">
                                    →
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Library Information Card --}}
                <div class="bg-indigo-50/60 rounded-3xl p-6 border border-indigo-100">
                    <h4 class="font-bold text-sm text-indigo-900 flex items-center space-x-2">
                        <span>🏛️</span>
                        <span>Library Guidelines</span>
                    </h4>
                    <ul class="mt-3 text-xs text-indigo-950/80 space-y-2 list-disc list-inside">
                        <li>Standard books can be borrowed online by logged-in members.</li>
                        <li><strong>Reference materials</strong> are reserved for in-library reading only.</li>
                        <li>All returns are processed at the <strong>Circulation Desk</strong> by library staff.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
