<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    🏢 Circulation Desk
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Staff dashboard: manage active checkouts and process book returns.</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 bg-blue-100 text-blue-700 font-bold text-xs rounded-full">
                    Active Loans: {{ $activeCount }}
                </span>
                <span class="px-3 py-1 bg-gray-100 text-gray-700 font-semibold text-xs rounded-full">
                    Total Records: {{ $totalCount }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Flash message / Errors --}}
        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center space-x-2 shadow-sm">
                <span class="text-emerald-500 text-lg">✓</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- Filter Tabs --}}
        <div class="flex items-center space-x-2 mb-6">
            <a href="{{ route('desk', ['status' => 'active']) }}"
               class="px-4 py-2 text-xs font-bold rounded-xl transition {{ $filter === 'active' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                ⏳ Active Loans ({{ $activeCount }})
            </a>
            <a href="{{ route('desk', ['status' => 'returned']) }}"
               class="px-4 py-2 text-xs font-bold rounded-xl transition {{ $filter === 'returned' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                ✓ Returned Records
            </a>
            <a href="{{ route('desk', ['status' => 'all']) }}"
               class="px-4 py-2 text-xs font-bold rounded-xl transition {{ $filter === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                All Records ({{ $totalCount }})
            </a>
        </div>

        {{-- Circulation Desk Table --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            @if ($loans->isEmpty())
                <div class="p-12 text-center">
                    <div class="text-4xl mb-3">✨</div>
                    <h3 class="text-lg font-bold text-gray-800">No records found</h3>
                    <p class="text-sm text-gray-500 mt-1">There are no loans matching the selected filter.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                <th class="px-6 py-4 font-semibold">Borrower (Member)</th>
                                <th class="px-6 py-4 font-semibold">Book Details</th>
                                <th class="px-6 py-4 font-semibold">Borrowed Date</th>
                                <th class="px-6 py-4 font-semibold">Status / Return Date</th>
                                <th class="px-6 py-4 font-semibold text-right">Desk Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach ($loans as $loan)
                                <tr class="hover:bg-gray-50 transition">
                                    {{-- Member --}}
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $loan->member_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $loan->member_email }}</div>
                                    </td>

                                    {{-- Book --}}
                                    <td class="px-6 py-4">
                                        <a href="{{ route('books.show', $loan->book_id) }}" class="font-bold text-indigo-700 hover:underline">
                                            {{ $loan->book_title }}
                                        </a>
                                        <div class="text-xs text-gray-500 font-normal">by {{ $loan->author_name }} · <span class="font-mono">ISBN: {{ $loan->isbn }}</span></div>
                                    </td>

                                    {{-- Borrowed At --}}
                                    <td class="px-6 py-4 text-gray-700">
                                        {{ \Carbon\Carbon::parse($loan->borrowed_at)->format('M d, Y') }}
                                        <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($loan->borrowed_at)->diffForHumans() }}</div>
                                    </td>

                                    {{-- Status / Return Date --}}
                                    <td class="px-6 py-4">
                                        @if ($loan->returned_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                Returned: {{ \Carbon\Carbon::parse($loan->returned_at)->format('M d, Y') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                                Unreturned
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Desk Action --}}
                                    <td class="px-6 py-4 text-right">
                                        @if (! $loan->returned_at)
                                            <form method="POST" action="{{ route('desk.return', $loan->loan_id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                                    ✓ Mark Returned
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs font-medium text-gray-400">Archived</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-6 border-t border-gray-100">
                    {{ $loans->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

