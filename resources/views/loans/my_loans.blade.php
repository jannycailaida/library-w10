<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    📋 My Loan History
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Track your currently borrowed books and reading history.</p>
            </div>
            <a href="{{ route('books.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                📚 Browse More Books
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Flash message --}}
        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center space-x-2 shadow-sm">
                <span class="text-emerald-500 text-lg">✓</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- Statistics Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                    📖
                </div>
                <div>
                    <div class="text-2xl font-black text-gray-900">{{ $loans->count() }}</div>
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Loans</div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold">
                    ⏳
                </div>
                <div>
                    <div class="text-2xl font-black text-rose-600">{{ $loans->whereNull('returned_at')->count() }}</div>
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Loans</div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                    ✓
                </div>
                <div>
                    <div class="text-2xl font-black text-emerald-600">{{ $loans->whereNotNull('returned_at')->count() }}</div>
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Returned Books</div>
                </div>
            </div>
        </div>

        {{-- Loans Table --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            @if ($loans->isEmpty())
                <div class="p-12 text-center">
                    <div class="text-4xl mb-3">📭</div>
                    <h3 class="text-lg font-bold text-gray-800">No loan records yet</h3>
                    <p class="text-sm text-gray-500 mt-1">You haven't borrowed any books from the library yet.</p>
                    <a href="{{ route('books.index') }}" class="mt-4 inline-block px-5 py-2.5 bg-indigo-600 text-white font-bold text-xs rounded-xl shadow-sm hover:bg-indigo-700 transition">
                        Explore Book Catalogue
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                <th class="px-6 py-4 font-semibold">Book Title</th>
                                <th class="px-6 py-4 font-semibold">Author</th>
                                <th class="px-6 py-4 font-semibold">Borrowed Date</th>
                                <th class="px-6 py-4 font-semibold">Return Date</th>
                                <th class="px-6 py-4 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach ($loans as $loan)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900">
                                        <a href="{{ route('books.show', $loan->book_id) }}" class="hover:text-indigo-600 transition">
                                            {{ $loan->title }}
                                        </a>
                                        <div class="text-xs text-gray-400 font-mono font-normal">ISBN: {{ $loan->isbn }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $loan->author_name }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">
                                        {{ \Carbon\Carbon::parse($loan->borrowed_at)->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">
                                        @if ($loan->returned_at)
                                            {{ \Carbon\Carbon::parse($loan->returned_at)->format('M d, Y h:i A') }}
                                        @else
                                            <span class="text-gray-400 italic">Not yet returned</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($loan->returned_at)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                ✓ Returned
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 animate-pulse">
                                                ⏳ Active Loan
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

