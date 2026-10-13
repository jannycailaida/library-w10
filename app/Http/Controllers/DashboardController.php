<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Global metrics
        $totalBooks = Book::count();
        $totalAuthors = Author::count();
        $totalMembers = Member::count();

        $activeLoansCount = DB::table('book_member')->whereNull('returned_at')->count();
        $returnedLoansCount = DB::table('book_member')->whereNotNull('returned_at')->count();

        $availableBooksCount = Book::where('is_reference', false)
            ->whereDoesntHave('members', fn ($q) => $q->whereNull('returned_at'))
            ->count();

        // User specific metrics (for member)
        $member = $user->member;
        $myActiveLoans = collect();
        $myTotalLoansCount = 0;

        if ($member) {
            $myActiveLoans = $member->books()
                ->wherePivotNull('returned_at')
                ->with('author')
                ->get();

            $myTotalLoansCount = $member->books()->count();
        }

        // Recent library-wide loans (for librarians & admins)
        $recentLoans = collect();
        if ($user->hasRole('librarian', 'admin')) {
            $recentLoans = DB::table('book_member')
                ->join('books', 'book_member.book_id', '=', 'books.id')
                ->join('members', 'book_member.member_id', '=', 'members.id')
                ->join('authors', 'books.author_id', '=', 'authors.id')
                ->select(
                    'book_member.id as loan_id',
                    'book_member.borrowed_at',
                    'book_member.returned_at',
                    'books.id as book_id',
                    'books.title as book_title',
                    'authors.name as author_name',
                    'members.name as member_name'
                )
                ->orderByRaw('book_member.returned_at IS NULL DESC')
                ->orderByDesc('book_member.borrowed_at')
                ->take(6)
                ->get();
        }

        // Popular / featured books
        $featuredBooks = Book::with('author')
            ->withCount(['members as borrow_count'])
            ->orderByDesc('borrow_count')
            ->take(4)
            ->get();

        return view('dashboard', compact(
            'user',
            'member',
            'totalBooks',
            'totalAuthors',
            'totalMembers',
            'activeLoansCount',
            'returnedLoansCount',
            'availableBooksCount',
            'myActiveLoans',
            'myTotalLoansCount',
            'recentLoans',
            'featuredBooks'
        ));
    }
}
