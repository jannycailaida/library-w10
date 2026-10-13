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

        {{-- 🖥️ Responsive Dashboard Layout with Left Sidebar --}}
        <div class="flex flex-col lg:flex-row items-start gap-8">

            {{-- 🗂️ Left Sidebar Menu (Enterprise Style inspired by CloudSchool) --}}
            <aside class="w-full lg:w-64 shrink-0 bg-slate-900 text-slate-300 rounded-3xl p-5 shadow-xl border border-slate-800">
                {{-- System Brand & Title --}}
                <div class="flex items-center space-x-3 pb-5 mb-5 border-b border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-xl shadow-md">
                        📚
                    </div>
                    <div>
                        <div class="font-bold text-white text-base leading-tight">LibraryHub</div>
                        <div class="text-[11px] text-slate-400 font-medium">Digital Management</div>
                    </div>
                </div>

                {{-- User Profile Card --}}
                <div class="p-3 bg-slate-800/80 rounded-2xl mb-6 border border-slate-700/60">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-indigo-500 text-white font-bold flex items-center justify-center text-sm shadow">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <div class="text-xs font-bold text-white truncate">{{ $user->name }}</div>
                            <div class="text-[10px] text-slate-400 truncate">{{ $user->email }}</div>
                        </div>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-700/50 flex items-center justify-between">
                        <span class="text-[10px] text-slate-400">Role:</span>
                        @if ($user->isAdmin())
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                👑 Admin
                            </span>
                        @elseif ($user->isLibrarian())
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                📚 Librarian
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                🎓 Student
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Navigation Links List --}}
                <nav class="space-y-1.5 text-xs font-semibold">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-bold transition">
                        <span class="text-base">📊</span>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('books.index') }}"
                       class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800 transition">
                        <span class="text-base">📚</span>
                        <span>Book Catalogue</span>
                    </a>

                    @if($member)
                        <a href="{{ route('my.loans') }}"
                           class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800 transition">
                            <span class="text-base">📖</span>
                            <span>My Loans</span>
                            @if($myActiveLoans->count() > 0)
                                <span class="ms-auto px-1.5 py-0.2 rounded-full text-[10px] bg-rose-500 text-white font-bold">
                                    {{ $myActiveLoans->count() }}
                                </span>
                            @endif
                        </a>
                    @endif

                    @if ($user->hasRole('librarian', 'admin'))
                        <div class="pt-4 pb-1">
                            <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider px-3">Staff Management</div>
                        </div>

                        <a href="{{ route('desk') }}"
                           class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800 transition">
                            <span class="text-base">🏢</span>
                            <span>Circulation Desk</span>
                        </a>

                        @can('create', App\Models\Book::class)
                            <a href="{{ route('books.create') }}"
                               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                <span class="text-base">➕</span>
                                <span>Add New Book</span>
                            </a>
                        @endcan
                    @endif

                    @can('manage-users')
                        <div class="pt-4 pb-1">
                            <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider px-3">Administration</div>
                        </div>

                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800 transition">
                            <span class="text-base">👥</span>
                            <span>User Management</span>
                        </a>
                    @endcan

                    <div class="pt-4 pb-1">
                        <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider px-3">Settings</div>
                    </div>

                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800 transition">
                        <span class="text-base">⚙️</span>
                        <span>Account Profile</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="pt-2">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-rose-400 hover:text-rose-300 hover:bg-rose-950/40 transition text-left">
                            <span class="text-base">🚪</span>
                            <span>Log Out</span>
                        </button>
                    </form>
                </nav>

                {{-- Status Indicator Footer --}}
                <div class="mt-8 pt-4 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400">
                    <span class="flex items-center space-x-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>System Online</span>
                    </span>
                    <span class="font-mono text-[10px] text-slate-400">v3.5 W10</span>
                </div>
            </aside>

            {{-- 📊 Main Content Area --}}
            <main class="flex-1 w-full space-y-7">

                {{-- Top Header Banner --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-indigo-600 mb-1">
                            Library Management Portal
                        </div>
                        <h1 class="text-2xl font-black text-gray-900 leading-tight">
                            Circulation & Inventory Analytics
                        </h1>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Today is {{ now()->format('l, F j, Y') }} — Showing real-time academic records.
                        </p>
                    </div>

                    <div class="flex items-center gap-2.5 flex-wrap">
                        <a href="{{ route('books.index') }}"
                           class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition flex items-center space-x-1.5">
                            <span>📚</span>
                            <span>Browse Books</span>
                        </a>

                        @if ($user->hasRole('librarian', 'admin'))
                            <a href="{{ route('desk') }}"
                               class="px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center space-x-1.5">
                                <span>🏢</span>
                                <span>Circulation Desk</span>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- 🎨 4 High-Contrast Vibrant Metric Boxes (Directly inspired by user reference: Orange, Red, Purple, Green!) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

                    {{-- 1. Orange Box: Total Titles --}}
                    <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-2xl shadow-lg shadow-orange-500/25 p-5 relative overflow-hidden flex flex-col justify-between transform hover:-translate-y-1 transition duration-200">
                        {{-- Large background icon watermark --}}
                        <div class="absolute -right-2 -bottom-3 text-7xl font-black text-white/20 pointer-events-none select-none">
                            📚
                        </div>

                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-amber-100">Total Book Titles</div>
                            <div class="text-4xl font-black mt-2 font-mono tracking-tight">{{ $totalBooks }}</div>
                            <div class="text-xs text-amber-100/90 mt-1">Across 7 Academic Disciplines</div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-white/20 flex items-center justify-between text-xs font-semibold">
                            <a href="{{ route('books.index') }}" class="hover:underline flex items-center space-x-1">
                                <span>View Catalogue</span>
                                <span>→</span>
                            </a>
                            <span class="text-white/70">100% Catalogued</span>
                        </div>
                    </div>

                    {{-- 2. Red Box: Active Loans (On Loan) --}}
                    <div class="bg-gradient-to-br from-rose-500 to-red-600 text-white rounded-2xl shadow-lg shadow-red-500/25 p-5 relative overflow-hidden flex flex-col justify-between transform hover:-translate-y-1 transition duration-200">
                        {{-- Large background icon watermark --}}
                        <div class="absolute -right-2 -bottom-3 text-7xl font-black text-white/20 pointer-events-none select-none">
                            ⏳
                        </div>

                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-rose-100">Active Borrowed Loans</div>
                            <div class="text-4xl font-black mt-2 font-mono tracking-tight">{{ $activeLoansCount }}</div>
                            <div class="text-xs text-rose-100/90 mt-1">Currently in Student Circulation</div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-white/20 flex items-center justify-between text-xs font-semibold">
                            @if ($user->hasRole('librarian', 'admin'))
                                <a href="{{ route('desk') }}?filter=active" class="hover:underline flex items-center space-x-1">
                                    <span>Circulation Desk</span>
                                    <span>→</span>
                                </a>
                            @else
                                <a href="{{ route('my.loans') }}" class="hover:underline flex items-center space-x-1">
                                    <span>My Loans</span>
                                    <span>→</span>
                                </a>
                            @endif
                            <span class="text-white/70">{{ $returnedLoansCount }} Returned</span>
                        </div>
                    </div>

                    {{-- 3. Purple Box: Library Members / Students --}}
                    <div class="bg-gradient-to-br from-purple-600 to-indigo-700 text-white rounded-2xl shadow-lg shadow-purple-500/25 p-5 relative overflow-hidden flex flex-col justify-between transform hover:-translate-y-1 transition duration-200">
                        {{-- Large background icon watermark --}}
                        <div class="absolute -right-2 -bottom-3 text-7xl font-black text-white/20 pointer-events-none select-none">
                            👥
                        </div>

                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-purple-100">Registered Members</div>
                            <div class="text-4xl font-black mt-2 font-mono tracking-tight">{{ $totalMembers }}</div>
                            <div class="text-xs text-purple-100/90 mt-1">Active Students & Faculty</div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-white/20 flex items-center justify-between text-xs font-semibold">
                            @can('manage-users')
                                <a href="{{ route('admin.users.index') }}" class="hover:underline flex items-center space-x-1">
                                    <span>Manage Users</span>
                                    <span>→</span>
                                </a>
                            @else
                                <span>Verified Borrowers</span>
                            @endcan
                            <span class="text-white/70">Cardholder Active</span>
                        </div>
                    </div>

                    {{-- 4. Green Box: Available to Borrow --}}
                    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 text-white rounded-2xl shadow-lg shadow-emerald-500/25 p-5 relative overflow-hidden flex flex-col justify-between transform hover:-translate-y-1 transition duration-200">
                        {{-- Large background icon watermark --}}
                        <div class="absolute -right-2 -bottom-3 text-7xl font-black text-white/20 pointer-events-none select-none">
                            ✓
                        </div>

                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-emerald-100">Available on Shelves</div>
                            <div class="text-4xl font-black mt-2 font-mono tracking-tight">{{ $availableBooksCount }}</div>
                            <div class="text-xs text-emerald-100/90 mt-1">Ready for 14-day checkout</div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-white/20 flex items-center justify-between text-xs font-semibold">
                            <a href="{{ route('books.index') }}?filter=available" class="hover:underline flex items-center space-x-1">
                                <span>Browse Available</span>
                                <span>→</span>
                            </a>
                            <span class="text-white/70">Instant Checkout</span>
                        </div>
                    </div>
                </div>

                {{-- 📈 Analytics Chart Box (Matching user's line graph) --}}
                <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 mb-5 border-b border-gray-100">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 flex items-center space-x-2">
                                <span>📈</span>
                                <span>Library Circulation & Borrowing Trends (Past 7 Days)</span>
                            </h2>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Real-time monitoring of textbook checkouts and returns.
                            </p>
                        </div>
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

                    {{-- Chart Canvas Container --}}
                    <div class="relative w-full h-64 sm:h-72">
                        <canvas id="circulationChart"></canvas>
                    </div>
                </div>

                {{-- 📑 Lower Two-Column Section: Circulation Table + Category Distribution --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-7">

                    {{-- Left Column: Recent Circulation Activity Stream (7 cols) --}}
                    <div class="lg:col-span-7 bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                                <div>
                                    <h3 class="font-bold text-gray-900 text-base flex items-center space-x-2">
                                        <span>📋</span>
                                        <span>Recent Circulation Activity</span>
                                    </h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Latest transactions across the library</p>
                                </div>
                                @if ($user->hasRole('librarian', 'admin'))
                                    <a href="{{ route('desk') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                                        Open Desk →
                                    </a>
                                @endif
                            </div>

                            @if ($user->hasRole('librarian', 'admin') && $recentLoans->isNotEmpty())
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs">
                                        <thead>
                                            <tr class="text-gray-400 border-b border-gray-100 font-semibold uppercase text-[10px]">
                                                <th class="py-2.5">Member</th>
                                                <th class="py-2.5">Book Title</th>
                                                <th class="py-2.5">Date</th>
                                                <th class="py-2.5 text-right">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            @foreach ($recentLoans as $loan)
                                                <tr class="hover:bg-gray-50/70 transition">
                                                    <td class="py-3 font-bold text-gray-900 truncate max-w-[120px]">
                                                        {{ $loan->member_name }}
                                                    </td>
                                                    <td class="py-3 text-gray-600 truncate max-w-[160px]">
                                                        <a href="{{ route('books.show', $loan->book_id) }}" class="hover:text-indigo-600">
                                                            {{ $loan->book_title }}
                                                        </a>
                                                    </td>
                                                    <td class="py-3 text-gray-400 font-mono text-[11px]">
                                                        {{ \Carbon\Carbon::parse($loan->borrowed_at)->format('M d') }}
                                                    </td>
                                                    <td class="py-3 text-right">
                                                        @if (is_null($loan->returned_at))
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                                                                On Loan
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                                                Returned
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @elseif($member && $myActiveLoans->isNotEmpty())
                                <div class="space-y-3">
                                    @foreach ($myActiveLoans as $myBook)
                                        <div class="p-3.5 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <span class="text-2xl">📖</span>
                                                <div>
                                                    <div class="font-bold text-gray-900 text-sm leading-tight">{{ $myBook->title }}</div>
                                                    <div class="text-xs text-gray-500 mt-0.5">by {{ $myBook->author->name }}</div>
                                                </div>
                                            </div>
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                                Borrowed
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="py-8 text-center text-gray-400">
                                    <span class="text-4xl block mb-2">📚</span>
                                    <p class="text-xs">No active borrowing transactions recorded yet.</p>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                            <span>Auto-refreshed with real-time MySQL loans</span>
                            <a href="{{ route('books.index') }}" class="font-semibold text-indigo-600 hover:text-indigo-800">Explore Catalogue →</a>
                        </div>
                    </div>

                    {{-- Right Column: Academic Category Breakdown (5 cols) --}}
                    <div class="lg:col-span-5 bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                                <div>
                                    <h3 class="font-bold text-gray-900 text-base flex items-center space-x-2">
                                        <span>🏷️</span>
                                        <span>Subject Distribution</span>
                                    </h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Books by academic discipline</p>
                                </div>
                                <span class="text-xs font-bold text-indigo-600">{{ $totalBooks }} total</span>
                            </div>

                            <div class="space-y-3.5">
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

                        <div class="mt-6 pt-3 border-t border-gray-100 text-center">
                            <a href="{{ route('books.index') }}"
                               class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                                Filter Books by Subject in Catalogue →
                            </a>
                        </div>
                    </div>
                </div>

            </main>
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
