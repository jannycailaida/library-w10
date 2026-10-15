<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Book::class);

        $categories = Book::categories();
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', Rule::in(['all', ...array_keys($categories)])],
            'filter' => ['nullable', Rule::in(['all', 'available', 'borrowed', 'reference'])],
            'sort' => ['nullable', Rule::in(['title_asc', 'title_desc', 'newest', 'oldest'])],
        ]);

        $search = trim($request->string('search')->toString());
        $filter = $request->input('filter') ?: 'all';
        $category = $request->input('category') ?: 'all';
        $sort = $request->input('sort') ?: 'title_asc';
        $query = Book::with('author')->withCount([
            'members as active_loans_count' => fn (Builder $loans): Builder => $loans->whereNull('returned_at'),
        ]);

        if ($search !== '') {
            $query->where(function (Builder $books) use ($search): void {
                $books->where('title', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhereHas('author', fn (Builder $authors): Builder => $authors->where('name', 'like', "%{$search}%"));
            });
        }

        if ($filter === 'available') {
            $query->available();
        } elseif ($filter === 'reference') {
            $query->where('is_reference', true);
        } elseif ($filter === 'borrowed') {
            $query->where('is_reference', false)->whereHas('members', fn (Builder $loans): Builder => $loans->whereNull('returned_at'));
        }

        if ($category !== 'all') {
            $query->inCategory($category);
        }

        match ($sort) {
            'newest' => $query->orderByDesc('published_year'),
            'oldest' => $query->orderBy('published_year'),
            'title_desc' => $query->orderByDesc('title'),
            default => $query->orderBy('title'),
        };

        $books = $query->orderBy('id')->paginate(12)->withQueryString();
        $totalBooks = Book::count();
        $availableCount = Book::available()->count();

        return view('books.index', compact('books', 'search', 'filter', 'category', 'sort', 'categories', 'totalBooks', 'availableCount'));
    }

    public function create(): View
    {
        Gate::authorize('create', Book::class);

        return view('books.form', [
            'authors' => Author::orderBy('name')->get(),
            'book' => new Book(['published_year' => now()->year, 'is_reference' => false]),
        ]);
    }

    public function store(BookRequest $request): RedirectResponse
    {
        Gate::authorize('create', Book::class);

        $book = Book::create([...$request->validated(), 'is_reference' => $request->boolean('is_reference')]);

        return to_route('books.show', $book)->with('status', 'Book added to the catalogue.');
    }

    public function show(Book $book): View
    {
        Gate::authorize('view', $book);

        $book->load('author')->loadCount([
            'members as active_loans_count' => fn (Builder $loans): Builder => $loans->whereNull('returned_at'),
            'members as loans_count',
        ]);

        return view('books.show', [
            'book' => $book,
            'isBorrowed' => $book->isBorrowed(),
            'isBorrowable' => $book->isBorrowable(),
        ]);
    }

    public function edit(Book $book): View
    {
        Gate::authorize('update', $book);

        return view('books.form', ['book' => $book, 'authors' => Author::orderBy('name')->get()]);
    }

    public function update(BookRequest $request, Book $book): RedirectResponse
    {
        Gate::authorize('update', $book);

        DB::transaction(function () use ($request, $book): void {
            $lockedBook = Book::whereKey($book->id)->lockForUpdate()->firstOrFail();

            if ($request->boolean('is_reference') && $lockedBook->isBorrowed()) {
                throw ValidationException::withMessages(['is_reference' => 'Return the current loan before making this a reference book.']);
            }

            $lockedBook->update([...$request->validated(), 'is_reference' => $request->boolean('is_reference')]);
        });

        return to_route('books.show', $book)->with('status', 'Book details updated.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        Gate::authorize('delete', $book);

        return DB::transaction(function () use ($book): RedirectResponse {
            $lockedBook = Book::whereKey($book->id)->lockForUpdate()->firstOrFail();

            if ($lockedBook->members()->exists()) {
                return back()->withErrors(['error' => 'Books with loan history cannot be deleted. Keep this record to preserve circulation history.']);
            }

            $lockedBook->delete();

            return to_route('books.index')->with('status', 'Book removed from the catalogue.');
        });
    }

    public function borrow(Request $request, Book $book): RedirectResponse
    {
        DB::transaction(function () use ($request, $book): void {
            $lockedBook = Book::whereKey($book->id)->lockForUpdate()->firstOrFail();
            abort_if($lockedBook->is_reference, 403, 'Reference books are for library use only.');

            if ($lockedBook->isBorrowed()) {
                throw ValidationException::withMessages(['error' => 'This book is already on loan. Choose another available title.']);
            }

            $user = User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $member = $user->member()->firstOrCreate([], ['name' => $user->name, 'email' => $user->email]);
            $member->books()->attach($lockedBook->id, ['borrowed_at' => now(), 'returned_at' => null]);
        }, 3);

        return to_route('my.loans')->with('status', "You borrowed “{$book->title}”. Return it to the circulation desk by ".now()->addDays(14)->format('M j, Y').'.');
    }
}
