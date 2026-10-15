<x-app-layout>
    <x-slot name="title">Circulation desk</x-slot>
    <div class="page-shell">
        <x-page-heading title="Circulation desk" description="Find a reader’s loan, verify the physical book, then record its return." />
        <x-feedback />
        @if ($overlappingBookIds)
            <div class="alert-warning" role="status">{{ count($overlappingBookIds) }} {{ count($overlappingBookIds) === 1 ? 'title has' : 'titles have' }} more than one active loan. Verify each loan against the physical copy before recording a return. Records that need review are marked below.</div>
        @endif
        <div class="mb-5 flex flex-wrap gap-x-5 gap-y-2 text-sm text-stone-600"><p><strong class="text-stone-900">{{ $activeCount }}</strong> on loan</p><p><strong class="{{ $overdueCount ? 'text-red-800' : 'text-stone-900' }}">{{ $overdueCount }}</strong> overdue</p><p><strong class="text-stone-900">{{ $totalCount }}</strong> total loan records</p></div>
        <form method="GET" action="{{ route('desk') }}" class="panel mb-6 grid items-end gap-4 p-4 sm:grid-cols-[minmax(0,1fr)_12rem_auto]">
            <div><label class="field-label" for="loan-search">Find a loan</label><input type="search" id="loan-search" name="search" value="{{ $search }}" placeholder="Book, ISBN, reader name, or email" maxlength="255"></div>
            <div><label class="field-label" for="loan-status">Loan status</label><select id="loan-status" name="status">@foreach (['active' => 'On loan', 'overdue' => 'Overdue', 'returned' => 'Returned', 'all' => 'All records'] as $value => $label)<option value="{{ $value }}" @selected($filter === $value)>{{ $label }}</option>@endforeach</select></div>
            <button class="btn-primary" type="submit">Find loans</button>
        </form>
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3"><h2 class="section-title">{{ $loans->total() }} matching {{ $loans->total() === 1 ? 'loan' : 'loans' }}</h2>@if ($search !== '' || $filter !== 'active')<a href="{{ route('desk') }}" class="btn text-brand-700">Reset view</a>@endif</div>
        <section class="panel" aria-label="Circulation records">
            @forelse ($loans as $loan)
                @php($dueDate = \Carbon\Carbon::parse($loan->borrowed_at)->addDays(14))
                <article class="record-grid">
                    @if (!$loan->returned_at && in_array((int) $loan->book_id, $overlappingBookIds, true))<p class="text-sm font-semibold text-amber-900 lg:col-span-4">Review this record: another open loan exists for this title.</p>@endif
                    <div class="min-w-0"><a href="{{ route('books.show', $loan->book_id) }}" class="font-semibold leading-6 hover:text-brand-700">{{ $loan->book_title }}</a><p class="mt-1 text-sm text-stone-600">{{ $loan->author_name }}</p><p class="mt-1 text-xs tabular-nums text-stone-600">ISBN {{ $loan->isbn }}</p></div>
                    <div class="min-w-0 text-sm"><p class="font-medium">{{ $loan->member_name }}</p><p class="mt-1 break-all text-stone-600">{{ $loan->member_email }}</p><p class="mt-1 text-xs text-stone-600">Card #{{ $loan->member_id }}</p></div>
                    <div class="text-sm"><x-loan-status :borrowed-at="$loan->borrowed_at" :returned-at="$loan->returned_at" /><p class="mt-2 font-medium">{{ $loan->returned_at ? 'Returned '.\Carbon\Carbon::parse($loan->returned_at)->format('M j, Y') : 'Due '.$dueDate->format('M j, Y') }}</p><p class="mt-1 text-stone-600">Borrowed {{ \Carbon\Carbon::parse($loan->borrowed_at)->format('M j, Y') }}</p></div>
                    <div>
                        @if (!$loan->returned_at)
                            <form method="POST" action="{{ route('desk.return', $loan->loan_id) }}">@csrf @method('PATCH')<button type="submit" class="btn-primary w-full lg:w-auto" aria-label="Record return of {{ $loan->book_title }} borrowed by {{ $loan->member_name }}">Record return</button></form>
                        @else
                            <span class="text-sm text-stone-600">Return recorded</span>
                        @endif
                    </div>
                </article>
            @empty
                <x-empty-state title="No loans found" description="Try a different search or status. New loans will appear here when books are borrowed." :href="route('desk', ['status' => 'all'])" action="View all records" />
            @endforelse
        </section>
        <div class="mt-6">{{ $loans->links() }}</div>
    </div>
</x-app-layout>
