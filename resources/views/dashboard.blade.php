<x-app-layout>
    <x-slot name="title">Overview</x-slot>
    <div class="page-shell">
        <x-page-heading :title="$user->hasRole('librarian', 'admin') ? 'Library overview' : 'Your library overview'" :description="'Welcome back, '.$user->name.'. Here is what needs your attention.'">
            @if ($user->hasRole('librarian', 'admin'))
                <a href="{{ route('desk') }}" class="btn-primary">Open circulation desk</a>
                <a href="{{ route('books.create') }}" class="btn-secondary">Add a book</a>
            @else
                <a href="{{ route('books.index') }}" class="btn-primary">Browse books <span aria-hidden="true">→</span></a>
            @endif
        </x-page-heading>
        <x-feedback />
        @if ($user->hasRole('librarian', 'admin'))
            <section aria-label="Library statistics" class="panel mb-8 grid sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('desk', ['status' => 'active']) }}" class="stat hover:bg-stone-50"><span class="stat-label">Active loans</span><strong class="stat-value">{{ $activeLoansCount }}</strong></a>
                <a href="{{ route('desk', ['status' => 'overdue']) }}" class="stat hover:bg-stone-50"><span class="stat-label">Overdue returns</span><strong class="stat-value {{ $overdueLoansCount ? 'text-red-800' : '' }}">{{ $overdueLoansCount }}</strong></a>
                <a href="{{ route('books.index', ['filter' => 'available']) }}" class="stat hover:bg-stone-50"><span class="stat-label">Available to borrow</span><strong class="stat-value">{{ $availableBooksCount }}</strong></a>
                <div class="stat"><span class="stat-label">Library cardholders</span><strong class="stat-value">{{ $totalMembers }}</strong></div>
            </section>
            <div class="grid items-start gap-7 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
                <section class="panel" aria-labelledby="recent-activity">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-stone-200 px-5 py-4"><h2 id="recent-activity" class="section-title">Recent circulation</h2><a href="{{ route('desk', ['status' => 'all']) }}" class="btn px-0 text-brand-700">All loan records <span aria-hidden="true">→</span></a></div>
                    @forelse ($recentLoans as $loan)
                        <article class="flex flex-col justify-between gap-3 border-b border-stone-100 p-5 last:border-0 sm:flex-row">
                            <div class="min-w-0"><a class="font-semibold leading-6 hover:text-brand-700" href="{{ route('books.show', $loan->book_id) }}">{{ $loan->book_title }}</a><p class="mt-1 text-sm text-stone-600">{{ $loan->member_name }}</p></div>
                            <div class="shrink-0 sm:text-right"><x-loan-status :borrowed-at="$loan->borrowed_at" :returned-at="$loan->returned_at" /><p class="mt-1 text-sm text-stone-600">{{ $loan->returned_at ? 'Returned '.\Carbon\Carbon::parse($loan->returned_at)->format('M j, Y') : 'Due '.\Carbon\Carbon::parse($loan->borrowed_at)->addDays(14)->format('M j, Y') }}</p></div>
                        </article>
                    @empty
                        <x-empty-state title="No circulation activity yet" description="Loans and returns will appear here when readers start borrowing." :href="route('books.index')" action="View catalogue" />
                    @endforelse
                </section>
                <aside class="panel p-5">
                    <h2 class="section-title">Daily library tasks</h2>
                    <div class="mt-4 grid gap-1">
                        <a href="{{ route('desk', ['status' => 'overdue']) }}" class="nav-item justify-between px-0">Review overdue returns <span aria-hidden="true">→</span></a>
                        <a href="{{ route('books.index') }}" class="nav-item justify-between px-0">Manage {{ $totalBooks }} catalogue titles <span aria-hidden="true">→</span></a>
                        @can('manage-users')<a href="{{ route('admin.users.index') }}" class="nav-item justify-between px-0">Manage users and roles <span aria-hidden="true">→</span></a>@endcan
                    </div>
                    <p class="mt-5 border-t border-stone-200 pt-4 text-sm leading-6 text-stone-600">Verify physical returns before recording them at the circulation desk. Reference books stay inside the library.</p>
                </aside>
            </div>
        @else
            <section aria-label="Your borrowing statistics" class="panel mb-8 grid sm:grid-cols-3">
                <a href="{{ route('my.loans', ['status' => 'active']) }}" class="stat hover:bg-stone-50"><span class="stat-label">Your books on loan</span><strong class="stat-value">{{ $myActiveLoans->count() }}</strong></a>
                <a href="{{ route('my.loans', ['status' => 'overdue']) }}" class="stat hover:bg-stone-50"><span class="stat-label">Your overdue returns</span><strong class="stat-value {{ $myOverdueCount ? 'text-red-800' : '' }}">{{ $myOverdueCount }}</strong></a>
                <a href="{{ route('my.loans') }}" class="stat hover:bg-stone-50"><span class="stat-label">Your borrowing history</span><strong class="stat-value">{{ $myTotalLoansCount }}</strong></a>
            </section>
            <div class="grid items-start gap-7 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
                <section class="panel" aria-labelledby="current-reading">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-stone-200 px-5 py-4"><h2 id="current-reading" class="section-title">Currently borrowed</h2><a href="{{ route('my.loans') }}" class="btn px-0 text-brand-700">Loan history <span aria-hidden="true">→</span></a></div>
                    @forelse ($myActiveLoans as $book)
                        <article class="flex flex-col justify-between gap-4 border-b border-stone-100 p-5 last:border-0 sm:flex-row">
                            <div class="min-w-0"><a href="{{ route('books.show', $book) }}" class="font-semibold leading-6 hover:text-brand-700">{{ $book->title }}</a><p class="mt-1 text-sm text-stone-600">{{ $book->author->name }}</p></div>
                            <div class="shrink-0 sm:text-right"><x-loan-status :borrowed-at="$book->pivot->borrowed_at" /><p class="mt-2 text-sm font-semibold">Due {{ \Carbon\Carbon::parse($book->pivot->borrowed_at)->addDays(14)->format('M j, Y') }}</p></div>
                        </article>
                    @empty
                        <x-empty-state title="Your next read is waiting" description="You have no books on loan. Browse the catalogue to find an available title." :href="route('books.index', ['filter' => 'available'])" action="Find an available book" />
                    @endforelse
                </section>
                <aside class="panel p-5">
                    <h2 class="section-title">Your library card</h2>
                    @if ($member)<p class="mt-3 text-sm text-stone-600">Card number <strong class="ml-1 font-medium tabular-nums text-stone-900">#{{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}</strong></p>@endif
                    <p class="mt-3 text-sm leading-6 text-stone-600">Each loan lasts 14 days. Bring your books to the circulation desk; library staff will record the return.</p>
                    <a href="{{ route('profile.edit') }}" class="btn mt-4 px-0 text-brand-700">Manage your account <span aria-hidden="true">→</span></a>
                </aside>
            </div>
        @endif
    </div>
</x-app-layout>
