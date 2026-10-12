<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Library Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                <div class="bg-white shadow-sm rounded-xl p-6">
                    <p class="text-sm font-medium text-gray-500">Total Books</p>
                    <p class="mt-4 text-3xl font-bold text-gray-900">{{ $totalBooks }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-xl p-6">
                    <p class="text-sm font-medium text-gray-500">Available Copies</p>
                    <p class="mt-4 text-3xl font-bold text-green-600">{{ $availableCopies }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-xl p-6">
                    <p class="text-sm font-medium text-gray-500">Active Loans</p>
                    <p class="mt-4 text-3xl font-bold text-blue-600">{{ $activeLoans }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-xl p-6">
                    <p class="text-sm font-medium text-gray-500">Overdue</p>
                    <p class="mt-4 text-3xl font-bold text-red-600">{{ $overdueLoans }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Loans</h3>
                        <a href="{{ route('books.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">View all books</a>
                    </div>

                    @if ($recentLoans->isEmpty())
                        <p class="text-sm text-gray-500">No loans yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($recentLoans as $loan)
                                <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                    <div class="flex justify-between gap-4">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $loan->book?->title ?? 'Unknown book' }}</p>
                                            <p class="text-sm text-gray-500">Borrowed by {{ $loan->user?->name ?? 'Unknown user' }}</p>
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $loan->borrowed_at?->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white shadow-sm rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Popular Titles</h3>
                    @if ($popularBooks->isEmpty())
                        <p class="text-sm text-gray-500">No titles available.</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($popularBooks as $book)
                                <div class="flex items-center justify-between gap-4 border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $book->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $book->author->name ?? 'Unknown author' }} · {{ $book->category->name ?? 'Uncategorized' }}</p>
                                    </div>
                                    <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-medium text-indigo-700">
                                        {{ $book->active_copies ?? 0 }} active
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
