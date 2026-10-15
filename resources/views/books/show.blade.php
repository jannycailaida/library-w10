<x-app-layout>
    <x-slot name="title">{{ $book->title }}</x-slot>
    <div class="page-shell max-w-5xl">
        <nav aria-label="Breadcrumb" class="mb-5 flex items-center gap-2 text-sm text-stone-600"><a href="{{ route('books.index') }}" class="text-link">Catalogue</a><span aria-hidden="true">/</span><span>Book details</span></nav>
        <x-page-heading :title="$book->title" :description="$book->author->name">
            @can('update', $book)<a class="btn-secondary" href="{{ route('books.edit', $book) }}">Edit book</a>@endcan
        </x-page-heading>
        <x-feedback />
        <div class="panel grid gap-7 p-5 sm:grid-cols-[12rem_minmax(0,1fr)] sm:p-7">
            <x-book-cover :book="$book" class="h-48 w-36 sm:h-72 sm:w-full" />
            <div class="min-w-0">
                <x-book-status :book="$book" />
                <dl class="mt-6 grid grid-cols-2 gap-x-4 gap-y-5 text-sm">
                    <div class="col-span-2"><dt class="text-stone-600">Subject</dt><dd class="mt-1 font-medium">{{ $book->category_name }}</dd></div>
                    <div><dt class="text-stone-600">ISBN</dt><dd class="mt-1 font-medium tabular-nums">{{ $book->isbn }}</dd></div>
                    <div><dt class="text-stone-600">Published</dt><dd class="mt-1 font-medium">{{ $book->published_year }}</dd></div>
                </dl>
                <section class="mt-7 border-t border-stone-200 pt-6" aria-label="Borrowing information">
                    @if ($book->is_reference)
                        <h2 class="section-title">Library use only</h2>
                        <p class="mt-2 text-sm leading-6 text-stone-600">This reference book stays in the library. You can read it in the reading room, but it cannot be checked out.</p>
                    @elseif ($isBorrowed)
                        <h2 class="section-title">This copy is on loan</h2>
                        <p class="mt-2 text-sm leading-6 text-stone-600">It will become available when library staff record its return.</p>
                        <a href="{{ route('books.index', ['filter' => 'available']) }}" class="btn-secondary mt-4">Browse available books</a>
                    @else
                        <h2 class="section-title">Borrow for 14 days</h2>
                        <p class="mt-2 text-sm leading-6 text-stone-600">Borrow today and return this copy at the circulation desk by <strong class="text-stone-900">{{ now()->addDays(14)->format('M j, Y') }}</strong>.</p>
                        @auth
                            <form method="POST" action="{{ route('books.borrow', $book) }}" class="mt-4">@csrf<button type="submit" class="btn-primary">Borrow this book</button></form>
                        @else
                            <div class="mt-4 flex flex-wrap gap-3"><a href="{{ route('login') }}" class="btn-primary">Sign in to borrow</a><a href="{{ route('register') }}" class="btn-secondary">Join the library</a></div>
                        @endauth
                    @endif
                </section>
            </div>
        </div>
        @can('delete', $book)
            <section class="mt-7 flex flex-col justify-between gap-4 border-t border-stone-200 pt-6 sm:flex-row sm:items-center" aria-labelledby="remove-book-title">
                <div><h2 id="remove-book-title" class="text-sm font-semibold">Remove from catalogue</h2><p class="mt-1 text-sm text-stone-600">{{ $book->loans_count ? 'This book has loan history and must be retained for circulation records.' : 'Only remove a book that is no longer part of the collection.' }}</p></div>
                @if (!$book->loans_count)
                    <form method="POST" action="{{ route('books.destroy', $book) }}" data-confirm="Permanently remove this book from the catalogue? This cannot be undone.">@csrf @method('DELETE')<button class="btn-danger shrink-0" type="submit">Delete book</button></form>
                @endif
            </section>
        @endcan
    </div>
</x-app-layout>
