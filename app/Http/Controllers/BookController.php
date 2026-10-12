<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookController extends Controller
{
    /**
     * Display a listing of books with search and filter.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $filter = $request->query('filter');

        $query = Book::with('author')->withCount([
            'members as active_loans_count' => fn ($q) => $q->whereNull('returned_at'),
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhereHas('author', fn ($aq) => $aq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($filter === 'available') {
            $query->where('is_reference', false)
                ->having('active_loans_count', 0);
        } elseif ($filter === 'reference') {
            $query->where('is_reference', true);
        }

        $books = $query->orderBy('title')->paginate(12)->withQueryString();

        $totalBooks = Book::count();
        $availableCount = Book::where('is_reference', false)
            ->whereDoesntHave('members', fn ($q) => $q->whereNull('returned_at'))
            ->count();
        $referenceCount = Book::where('is_reference', true)->count();
        $totalMembers = Member::count();

        return view('books.index', compact('books', 'search', 'filter', 'totalBooks', 'availableCount', 'referenceCount', 'totalMembers'));
    }

    /**
     * Show form to create a new book (Librarians/Admins only).
     */
    public function create()
    {
        Gate::authorize('create', Book::class);

        $authors = Author::orderBy('name')->get();
        $book = new Book(['published_year' => (int) date('Y'), 'is_reference' => false]);

        return view('books.form', compact('book', 'authors'));
    }

    /**
     * Store a newly created book.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Book::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author_id' => ['required', 'exists:authors,id'],
            'isbn' => ['required', 'string', 'size:13', 'unique:books,isbn'],
            'published_year' => ['required', 'integer', 'between:1500,'.(date('Y') + 1)],
            'is_reference' => ['boolean'],
            'cover_path' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['is_reference'] = $request->boolean('is_reference');

        $book = Book::create($validated);

        return redirect()->route('books.show', $book)
            ->with('status', "Book '{$book->title}' has been added successfully.");
    }

    /**
     * Display a specific book.
     */
    public function show(Book $book)
    {
        $book->load('author');
        $isBorrowed = $book->isBorrowed();
        $isBorrowable = $book->isBorrowable();

        return view('books.show', compact('book', 'isBorrowed', 'isBorrowable'));
    }

    /**
     * Show form to edit a book.
     */
    public function edit(Book $book)
    {
        Gate::authorize('update', $book);

        $authors = Author::orderBy('name')->get();

        return view('books.form', compact('book', 'authors'));
    }

    /**
     * Update the specified book.
     */
    public function update(Request $request, Book $book)
    {
        Gate::authorize('update', $book);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author_id' => ['required', 'exists:authors,id'],
            'isbn' => ['required', 'string', 'size:13', 'unique:books,isbn,'.$book->id],
            'published_year' => ['required', 'integer', 'between:1500,'.(date('Y') + 1)],
            'is_reference' => ['boolean'],
            'cover_path' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['is_reference'] = $request->boolean('is_reference');

        $book->update($validated);

        return redirect()->route('books.show', $book)
            ->with('status', 'Book details updated successfully.');
    }

    /**
     * Delete a book.
     */
    public function destroy(Book $book)
    {
        Gate::authorize('delete', $book);

        // Cannot delete a book with active unreturned loans
        if ($book->isBorrowed()) {
            return back()->withErrors(['error' => 'Cannot delete a book that is currently on loan.']);
        }

        $book->delete();

        return redirect()->route('books.index')
            ->with('status', 'Book removed from library catalogue.');
    }

    /**
     * Borrow action (attaches logged-in user's member record).
     * Reference books cannot be borrowed.
     */
    public function borrow(Request $request, Book $book)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Must have a linked member record
        $member = $user->member;
        if (! $member) {
            // Auto-create member record for logged in user if not yet present
            $member = $user->member()->create([
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }

        if ($book->is_reference) {
            abort(403, 'Reference books cannot be borrowed.');
        }

        if ($book->isBorrowed()) {
            return back()->withErrors(['error' => 'This book is currently borrowed by another member.']);
        }

        // Attach loan
        $member->books()->attach($book->id, [
            'borrowed_at' => now(),
            'returned_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('my.loans')
            ->with('status', "You have successfully borrowed '{$book->title}'. Happy reading!");
    }
}
