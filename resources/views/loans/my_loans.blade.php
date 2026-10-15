<x-app-layout>
    <x-slot name="title">My loans</x-slot>
    <div class="page-shell">
        <x-page-heading title="My loans" description="Track your borrowed books and due dates. Bring returns to the library circulation desk."><a href="{{ route('books.index') }}" class="btn-primary">Browse books</a></x-page-heading>
        <x-feedback />
        <div class="mb-5 flex flex-wrap gap-x-5 gap-y-2 text-sm text-stone-600"><p><strong class="text-stone-900">{{ $activeCount }}</strong> on loan</p><p><strong class="{{ $overdueCount ? 'text-red-800' : 'text-stone-900' }}">{{ $overdueCount }}</strong> overdue</p><p><strong class="text-stone-900">{{ $totalCount - $activeCount }}</strong> returned</p></div>
        <nav class="mb-6 flex flex-wrap gap-2" aria-label="Filter your loans">
            @foreach (['all' => 'All history', 'active' => 'On loan', 'overdue' => 'Overdue', 'returned' => 'Returned'] as $value => $label)<a href="{{ route('my.loans', ['status' => $value]) }}" @class(['btn', 'btn-primary' => $filter === $value, 'btn-secondary' => $filter !== $value]) @if($filter === $value) aria-current="page" @endif>{{ $label }}</a>@endforeach
        </nav>
        <section class="panel" aria-label="Your loan records">
            @forelse ($loans as $loan)
                @php($dueDate = \Carbon\Carbon::parse($loan->borrowed_at)->addDays(14))
                <article class="record-grid">
                    <div class="min-w-0"><a href="{{ route('books.show', $loan->book_id) }}" class="font-semibold leading-6 hover:text-brand-700">{{ $loan->title }}</a><p class="mt-1 text-sm text-stone-600">{{ $loan->author_name }}</p><p class="mt-1 text-xs tabular-nums text-stone-600">ISBN {{ $loan->isbn }}</p></div>
                    <dl class="grid grid-cols-2 gap-4 text-sm lg:contents">
                        <div><dt class="text-stone-600">Borrowed</dt><dd class="mt-1 font-medium">{{ \Carbon\Carbon::parse($loan->borrowed_at)->format('M j, Y') }}</dd></div>
                        <div><dt class="text-stone-600">{{ $loan->returned_at ? 'Returned' : 'Due date' }}</dt><dd class="mt-1 font-medium {{ !$loan->returned_at && now()->gt($dueDate) ? 'text-red-800' : '' }}">{{ $loan->returned_at ? \Carbon\Carbon::parse($loan->returned_at)->format('M j, Y') : $dueDate->format('M j, Y') }}</dd></div>
                    </dl>
                    <div><x-loan-status :borrowed-at="$loan->borrowed_at" :returned-at="$loan->returned_at" /></div>
                </article>
            @empty
                <x-empty-state title="No loans in this view" :description="$totalCount ? 'Choose a different filter to see the rest of your borrowing history.' : 'Your borrowed books and return dates will appear here.'" :href="$totalCount ? route('my.loans') : route('books.index')" :action="$totalCount ? 'Show all history' : 'Browse the catalogue'" />
            @endforelse
        </section>
        <div class="mt-6">{{ $loans->links() }}</div>
    </div>
</x-app-layout>
