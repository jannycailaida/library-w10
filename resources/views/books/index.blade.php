<x-app-layout>
    <x-slot name="title">Catalogue</x-slot>
    <div class="page-shell" x-data="catalogue">
        <x-page-heading title="The library catalogue" description="Find your next read, check availability, and borrow for 14 days.">
            @can('create', App\Models\Book::class)<a href="{{ route('books.create') }}" class="btn-primary">Add a book <span aria-hidden="true">+</span></a>@endcan
        </x-page-heading>
        <x-feedback />
        <div class="mb-5 flex flex-wrap items-center justify-between gap-2 text-sm text-stone-600">
            <p><strong class="font-semibold text-stone-900">{{ $totalBooks }}</strong> titles in the collection <span aria-hidden="true">·</span> <strong class="font-semibold text-brand-800">{{ $availableCount }}</strong> available to borrow</p>
            <p>Reference books are for library use only.</p>
        </div>
        <form method="GET" action="{{ route('books.index') }}" class="panel mb-7 p-4 sm:p-5" data-catalogue-search role="search" aria-label="Search the library catalogue">
            <label for="search" class="field-label">Search the collection</label>
            <div class="flex items-center gap-3">
                <input id="search" name="search" type="search" value="{{ $search }}" maxlength="255" placeholder="Title, author, or 13-digit ISBN" class="min-w-0 flex-1" autocomplete="off">
                <button class="btn-primary shrink-0" type="submit">Search</button>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="col-span-2 sm:col-span-1">
                    <label for="category" class="field-label">Subject</label>
                    <select id="category" name="category">
                        <option value="all" @selected($category === 'all')>All subjects</option>
                        @foreach ($categories as $slug => $subject)<option value="{{ $slug }}" @selected($category === $slug)>{{ $subject['name'] }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label for="filter" class="field-label">Availability</label>
                    <select id="filter" name="filter">
                        <option value="all" @selected($filter === 'all')>All books</option>
                        <option value="available" @selected($filter === 'available')>Available</option>
                        <option value="borrowed" @selected($filter === 'borrowed')>On loan</option>
                        <option value="reference" @selected($filter === 'reference')>Reference only</option>
                    </select>
                </div>
                <div>
                    <label for="sort" class="field-label">Sort by</label>
                    <select id="sort" name="sort">
                        <option value="title_asc" @selected($sort === 'title_asc')>Title: A–Z</option>
                        <option value="title_desc" @selected($sort === 'title_desc')>Title: Z–A</option>
                        <option value="newest" @selected($sort === 'newest')>Newest edition</option>
                        <option value="oldest" @selected($sort === 'oldest')>Oldest edition</option>
                    </select>
                </div>
            </div>
            <p data-search-status class="mt-3 text-sm text-stone-600" role="status" aria-live="polite">Results update as you type. You can also press Search.</p>
        </form>

        <section id="catalogue-results" aria-label="Catalogue results" aria-busy="false">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h2 class="section-title">{{ $books->total() }} {{ $books->total() === 1 ? 'book' : 'books' }}{{ $search !== '' ? ' matching “'.$search.'”' : '' }}</h2>
                @if ($search !== '' || $filter !== 'all' || $category !== 'all' || $sort !== 'title_asc')<a href="{{ route('books.index') }}" class="btn text-brand-700">Clear filters</a>@endif
            </div>
            @if ($books->isEmpty())
                <div class="panel"><x-empty-state title="No books found" description="Try a different title, author, or ISBN, or clear your filters to browse the collection." :href="route('books.index')" action="Browse all books" /></div>
            @else
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($books as $book)
                        @php
                            $preview = [
                                'title' => $book->title, 'author' => $book->author->name,
                                'isbn' => $book->isbn, 'year' => $book->published_year,
                                'category' => $book->category_name,
                                'cover' => $book->cover_path ? asset($book->cover_path) : null,
                                'reference' => $book->is_reference, 'borrowed' => $book->isBorrowed(),
                                'url' => route('books.show', $book), 'borrowUrl' => route('books.borrow', $book),
                            ];
                        @endphp
                        <article class="panel grid grid-cols-[6.5rem_minmax(0,1fr)] gap-4 p-4 sm:flex sm:flex-col">
                            <a href="{{ route('books.show', $book) }}" class="block rounded-lg" aria-label="View {{ $book->title }}"><x-book-cover :book="$book" class="h-36 sm:h-52" /></a>
                            <div class="flex min-w-0 flex-1 flex-col items-start">
                                <p class="mb-2 text-xs font-medium text-stone-600">{{ $book->category_name }}</p>
                                <h3 class="text-base font-semibold leading-6 text-stone-900"><a href="{{ route('books.show', $book) }}" class="hover:text-brand-700">{{ $book->title }}</a></h3>
                                <p class="mt-1 text-sm leading-5 text-stone-600">{{ $book->author->name }} <span aria-hidden="true">·</span> {{ $book->published_year }}</p>
                                <div class="mt-3"><x-book-status :book="$book" /></div>
                                <div class="mt-auto flex w-full flex-wrap items-center justify-between gap-2 pt-4">
                                    <button type="button" class="btn-secondary px-3" @click="openBook({{ Illuminate\Support\Js::from($preview) }}, $el)">Quick view</button>
                                    @if ($book->isBorrowable())
                                        @auth
                                            <form method="POST" action="{{ route('books.borrow', $book) }}">@csrf<button type="submit" class="btn-primary px-3">Borrow</button></form>
                                        @else
                                            <a href="{{ route('books.show', $book) }}" class="btn px-2 text-brand-700">View details <span aria-hidden="true">→</span></a>
                                        @endauth
                                    @else
                                        <a href="{{ route('books.show', $book) }}" class="btn px-2 text-stone-600">Details <span aria-hidden="true">→</span></a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-7">{{ $books->links() }}</div>
            @endif
        </section>

        <dialog x-ref="preview" aria-labelledby="preview-title" class="w-[calc(100%-2rem)] max-w-xl" @cancel.prevent="closeBook()" @click="if ($event.target === $el) closeBook()">
            <div class="p-5 sm:p-7">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <p class="text-sm font-semibold text-stone-600">Book preview</p>
                    <button type="button" x-ref="previewClose" class="btn-secondary px-3" @click="closeBook()" aria-label="Close book preview">Close <span aria-hidden="true">×</span></button>
                </div>
                <template x-if="selected">
                    <div>
                        <h2 id="preview-title" class="font-serif text-2xl leading-snug" x-text="selected.title"></h2>
                        <p class="mt-2 text-sm text-stone-600" x-text="selected.author"></p>
                        <dl class="my-6 grid grid-cols-2 gap-4 text-sm">
                            <div><dt class="text-stone-600">ISBN</dt><dd class="mt-1 font-medium tabular-nums" x-text="selected.isbn"></dd></div>
                            <div><dt class="text-stone-600">Published</dt><dd class="mt-1 font-medium" x-text="selected.year"></dd></div>
                            <div class="col-span-2"><dt class="text-stone-600">Subject</dt><dd class="mt-1 font-medium" x-text="selected.category"></dd></div>
                        </dl>
                        <p class="rounded-lg bg-stone-50 p-4 text-sm leading-6 text-stone-700" x-text="selected.reference ? 'Reference material. Read this book inside the library; it cannot be borrowed.' : selected.borrowed ? 'This copy is currently on loan. Check back when it has been returned.' : 'Available for a 14-day loan. Return your copy at the circulation desk.'"></p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a :href="selected.url" class="btn-secondary">Full book details</a>
                            @auth
                                <template x-if="!selected.reference && !selected.borrowed"><form method="POST" :action="selected.borrowUrl">@csrf<button type="submit" class="btn-primary">Borrow for 14 days</button></form></template>
                            @else
                                <a href="{{ route('login') }}" class="btn-primary">Sign in to borrow</a>
                            @endauth
                        </div>
                    </div>
                </template>
            </div>
        </dialog>
    </div>
</x-app-layout>
