<x-app-layout>
    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Flash message --}}
        @if (session('status'))
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 flex items-center justify-between shadow-sm animate-fade-in">
                <div class="flex items-center space-x-3">
                    <span class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm">✓</span>
                    <span class="font-medium text-sm">{{ session('status') }}</span>
                </div>
            </div>
        @endif

        {{-- 🏛️ Hero Welcome Header (Sleek, Full-Width, Consistent with Catalogue) --}}
        <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 text-white p-8 sm:p-10 shadow-xl border border-indigo-900/40">
            <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-indigo-500/20 blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -bottom-20 w-80 h-80 rounded-full bg-purple-500/15 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-indigo-500/20 text-indigo-300 border border-indigo-400/30">
                            <span>✦</span>
                            <span>Library Management Portal</span>
                        </span>

                        @if ($user->isAdmin())
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-500/30 text-purple-200 border border-purple-400/40">
                                👑 System Administrator
                            </span>
                        @elseif ($user->isLibrarian())
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-500/30 text-blue-200 border border-blue-400/40">
                                📚 Librarian Staff
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/30 text-emerald-200 border border-emerald-400/40">
                                🎓 Student Cardholder
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight leading-tight">
                        Welcome back, {{ $user->name }}! 👋
                    </h1>

                    <p class="text-indigo-200/80 text-sm mt-2 max-w-2xl leading-relaxed">
                        @if ($user->hasRole('librarian', 'admin'))
                            You have staff permissions. Manage book circulation, track member loans, and keep university inventory updated.
                        @else
                            Access university textbooks, track your borrowed loans, and discover resources for your courses.
                        @endif
                    </p>

                    @if ($member)
                        <div class="mt-4 flex items-center space-x-4 text-xs font-mono text-indigo-300">
                            <span class="bg-white/10 px-3 py-1 rounded-lg border border-white/10">
                                💳 Card ID: <strong class="text-white font-bold">#{{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}</strong>
                            </span>
                            <span class="text-indigo-200/60 font-sans">
                                Today is {{ now()->format('l, F j, Y') }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Quick Header Action Buttons --}}
                <div class="flex items-center gap-3 flex-wrap shrink-0">
                    <a href="{{ route('books.index') }}"
                       class="px-5 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-2xl shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5 flex items-center space-x-2">
                        <span>📚</span>
                        <span>Browse Catalogue</span>
                    </a>

                    @if ($user->hasRole('librarian', 'admin'))
                        <a href="{{ route('desk') }}"
                           class="px-5 py-3 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-2xl border border-white/20 transition flex items-center space-x-2">
                            <span>🏢</span>
                            <span>Circulation Desk</span>
                        </a>

                        @can('create', App\Models\Book::class)
                            <a href="{{ route('books.create') }}"
                               class="px-4 py-3 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-2xl border border-white/20 transition">
                                <span>+ Add Book</span>
                            </a>
                        @endcan
                    @else
                        <a href="{{ route('my.loans') }}"
                           class="px-5 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-2xl shadow-lg shadow-emerald-600/30 transition flex items-center space-x-2">
                            <span>📖</span>
                            <span>My Active Loans ({{ $myActiveLoans->count() }})</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- 📊 Refined & Cohesive 4 Metric Cards (Clean, Professional, Not Clunky) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- 1. Total Titles --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Book Titles</div>
                        <div class="text-3xl font-black text-gray-900 mt-1 font-mono">{{ $totalBooks }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        📚
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                    <span class="text-gray-500">Across 7 subjects</span>
                    <a href="{{ route('books.index') }}" class="font-bold text-indigo-600 hover:text-indigo-800 transition">
                        Catalogue →
                    </a>
                </div>
            </div>

            {{-- 2. Active Loans --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Active Borrowed Loans</div>
                        <div class="text-3xl font-black text-rose-600 mt-1 font-mono">{{ $activeLoansCount }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        ⏳
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                    <span class="text-gray-500">{{ $returnedLoansCount }} returned records</span>
                    @if ($user->hasRole('librarian', 'admin'))
                        <a href="{{ route('desk') }}?filter=active" class="font-bold text-rose-600 hover:text-rose-800 transition">
                            Desk →
                        </a>
                    @else
                        <a href="{{ route('my.loans') }}" class="font-bold text-rose-600 hover:text-rose-800 transition">
                            My Loans →
                        </a>
                    @endif
                </div>
            </div>

            {{-- 3. Library Members --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Library Members</div>
                        <div class="text-3xl font-black text-purple-600 mt-1 font-mono">{{ $totalMembers }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        👥
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                    <span class="text-gray-500">Active student cards</span>
                    @can('manage-users')
                        <a href="{{ route('admin.users.index') }}" class="font-bold text-purple-600 hover:text-purple-800 transition">
                            Manage →
                        </a>
                    @else
                        <span class="text-gray-400">Verified Patrons</span>
                    @endcan
                </div>
            </div>

            {{-- 4. Available to Borrow --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Available on Shelves</div>
                        <div class="text-3xl font-black text-emerald-600 mt-1 font-mono">{{ $availableBooksCount }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        ✓
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                    <span class="text-gray-500">14-day checkout</span>
                    <a href="{{ route('books.index') }}?filter=available" class="font-bold text-emerald-600 hover:text-emerald-800 transition">
                        Borrow →
                    </a>
                </div>
            </div>
        </div>

        {{-- 📈 Main Content Grid: Analytics Graph (8 cols) + Subject Distribution (4 cols) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- Analytics Line Chart (8 cols) --}}
            <div class="lg:col-span-8 bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 mb-5 border-b border-gray-100">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 flex items-center space-x-2">
                                <span>📈</span>
                                <span>Library Circulation & Borrowing Trends</span>
                            </h2>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Daily checkouts and book returns over the past 7 days.
                            </p>
                        </div>

                        {{-- Legend Pills --}}
                        <div class="flex items-center space-x-4 text-xs font-semibold">
                            <span class="flex items-center space-x-1.5">
                                <span class="w-3 h-3 rounded-full bg-indigo-600"></span>
                                <span class="text-gray-700">Books Borrowed</span>
                            </span>
                            <span class="flex items-center space-x-1.5">
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                <span class="text-gray-700">Books Returned</span>
                            </span>
                        </div>
                    </div>

                    {{-- Chart Canvas --}}
                    <div class="relative w-full h-72">
                        <canvas id="circulationChart"></canvas>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                    <span>⚡ Live data calculated from circulation timestamps</span>
                    <span class="text-gray-500 font-mono">Real-time MySQL logs</span>
                </div>
            </div>

            {{-- Subject Distribution Breakdown (4 cols) --}}
            <div class="lg:col-span-4 bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                        <div>
                            <h3 class="font-bold text-gray-900 text-base flex items-center space-x-2">
                                <span>🏷️</span>
                                <span>Subject Distribution</span>
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">Inventory by discipline</p>
                        </div>
                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-lg">
                            {{ $totalBooks }} total
                        </span>
                    </div>

                    <div class="space-y-4">
                        @foreach ($categoryBreakdown as $cat)
                            @php
                                $percent = $totalBooks > 0 ? round(($cat['count'] / $totalBooks) * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-semibold text-gray-700 flex items-center space-x-1.5">
                                        <span>{{ $cat['icon'] }}</span>
                                        <span>{{ $cat['name'] }}</span>
                                    </span>
                                    <span class="font-mono text-gray-500 font-bold">
                                        {{ $cat['count'] }} <span class="text-[10px] text-gray-400 font-normal">({{ $percent }}%)</span>
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500"
                                         style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                    <a href="{{ route('books.index') }}"
                       class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                        View Filtered Catalogue →
                    </a>
                </div>
            </div>
        </div>

        {{-- 📑 Lower Grid: Recent Circulation Table (or Member's Personal Loans) + Popular Books --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- Left: Recent Transactions (8 cols) --}}
            <div class="lg:col-span-8 bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base flex items-center space-x-2">
                            <span>📋</span>
                            <span>{{ $user->hasRole('librarian', 'admin') ? 'Recent Circulation Stream' : 'My Current Book Loans' }}</span>
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $user->hasRole('librarian', 'admin') ? 'Live checkout and return records from students' : 'Books currently in your possession' }}
                        </p>
                    </div>

                    @if ($user->hasRole('librarian', 'admin'))
                        <a href="{{ route('desk') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                            Open Desk →
                        </a>
                    @else
                        <a href="{{ route('my.loans') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 transition">
                            View History →
                        </a>
                    @endif
                </div>

                @if ($user->hasRole('librarian', 'admin'))
                    @if ($recentLoans->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="text-gray-400 border-b border-gray-100 font-semibold uppercase text-[10px]">
                                        <th class="py-2.5">Member</th>
                                        <th class="py-2.5">Book Title</th>
                                        <th class="py-2.5">Borrowed</th>
                                        <th class="py-2.5 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach ($recentLoans as $loan)
                                        <tr class="hover:bg-gray-50/70 transition">
                                            <td class="py-3.5 font-bold text-gray-900">
                                                {{ $loan->member_name }}
                                            </td>
                                            <td class="py-3.5 text-gray-600 max-w-xs truncate">
                                                <a href="{{ route('books.show', $loan->book_id) }}" class="hover:text-indigo-600 font-medium">
                                                    {{ $loan->book_title }}
                                                </a>
                                            </td>
                                            <td class="py-3.5 text-gray-400 font-mono text-[11px]">
                                                {{ \Carbon\Carbon::parse($loan->borrowed_at)->format('M d, Y') }}
                                            </td>
                                            <td class="py-3.5 text-right">
                                                @if (is_null($loan->returned_at))
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                                                        ⏳ On Loan
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                                        ✓ Returned
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="py-12 text-center text-gray-400">
                            <span class="text-4xl block mb-2">📚</span>
                            <p class="text-xs">No circulation records logged yet.</p>
                        </div>
                    @endif
                @else
                    {{-- Student / Member View --}}
                    @if ($myActiveLoans->isNotEmpty())
                        <div class="space-y-3">
                            @foreach ($myActiveLoans as $myBook)
                                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-between hover:bg-gray-100/70 transition">
                                    <div class="flex items-center space-x-3.5">
                                        @if ($myBook->cover_path)
                                            <img src="{{ asset($myBook->cover_path) }}" alt="{{ $myBook->title }}"
                                                 class="w-10 h-14 object-cover rounded-lg shadow-sm border border-gray-200">
                                        @else
                                            <div class="w-10 h-14 bg-slate-900 rounded-lg flex items-center justify-center text-lg text-white">📖</div>
                                        @endif
                                        <div>
                                            <a href="{{ route('books.show', $myBook) }}" class="font-bold text-gray-900 text-sm hover:text-indigo-600">
                                                {{ $myBook->title }}
                                            </a>
                                            <div class="text-xs text-gray-500 mt-0.5">by {{ $myBook->author->name }}</div>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                        ⏳ Borrowed
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 text-center text-gray-400">
                            <span class="text-4xl block mb-2">📖</span>
                            <p class="text-sm font-semibold text-gray-700">You currently have no borrowed books.</p>
                            <p class="text-xs text-gray-400 mt-1">Explore our university catalogue to find textbooks for your studies.</p>
                            <a href="{{ route('books.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-bold text-xs rounded-xl shadow transition">
                                Browse Catalogue
                            </a>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Right: Most Borrowed / Popular Titles (4 cols) --}}
            <div class="lg:col-span-4 bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                        <div>
                            <h3 class="font-bold text-gray-900 text-base flex items-center space-x-2">
                                <span>🔥</span>
                                <span>Most Borrowed</span>
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">Trending student textbooks</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach ($featuredBooks as $featured)
                            <div class="flex items-center space-x-3 group">
                                @if ($featured->cover_path)
                                    <img src="{{ asset($featured->cover_path) }}" alt="{{ $featured->title }}"
                                         class="w-10 h-14 object-cover rounded-lg shadow-sm border border-gray-200 group-hover:scale-105 transition-transform">
                                @else
                                    <div class="w-10 h-14 bg-slate-900 rounded-lg flex items-center justify-center text-lg text-white">📖</div>
                                @endif

                                <div class="overflow-hidden flex-1">
                                    <a href="{{ route('books.show', $featured) }}" class="font-bold text-xs text-gray-900 truncate block hover:text-indigo-600 transition">
                                        {{ $featured->title }}
                                    </a>
                                    <div class="text-[11px] text-gray-500 mt-0.5 truncate">{{ $featured->author->name }}</div>
                                    <div class="text-[10px] text-indigo-600 font-semibold mt-0.5">
                                        Borrowed {{ $featured->borrow_count }} times
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                    <a href="{{ route('books.index') }}?filter=available" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 transition">
                        View All Available Books →
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- 📊 Chart.js Script for Circulation Line Chart --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('circulationChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Books Borrowed',
                            data: @json($chartBorrowed),
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.08)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#4f46e5',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                        },
                        {
                            label: 'Books Returned',
                            data: @json($chartReturned),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.05)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { family: 'Figtree, sans-serif', size: 12, weight: 'bold' },
                            bodyFont: { family: 'Figtree, sans-serif', size: 12 },
                            padding: 10,
                            cornerRadius: 12,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: { family: 'Figtree, sans-serif', size: 11 },
                                color: '#64748b',
                            },
                            grid: {
                                color: '#f1f5f9',
                            },
                            border: {
                                display: false,
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                            },
                            ticks: {
                                font: { family: 'Figtree, sans-serif', size: 11 },
                                color: '#64748b',
                            },
                            border: {
                                display: false,
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
