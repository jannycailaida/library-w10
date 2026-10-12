<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalBooks = Book::count();
        $availableCopies = Book::query()->get()->sum(fn (Book $book) => $book->availableCopies());
        $activeLoans = Loan::query()->whereNull('returned_at')->where('status', 'borrowed')->count();
        $overdueLoans = Loan::query()->whereNull('returned_at')->where('status', 'borrowed')->where('due_at', '<', now())->count();
        $recentLoans = Loan::query()->with(['book', 'user'])->latest('borrowed_at')->take(5)->get();
        $popularBooks = Book::query()->with(['author', 'category'])
            ->withCount(['activeLoans as active_copies' => fn ($query) => $query->whereNull('returned_at')])
            ->orderByDesc('active_copies')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalBooks',
            'availableCopies',
            'activeLoans',
            'overdueLoans',
            'recentLoans',
            'popularBooks',
        ));
    }
}
