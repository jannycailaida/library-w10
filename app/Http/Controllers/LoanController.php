<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class LoanController extends Controller
{
    /**
     * /my/loans - member sees their own loans (auth required).
     */
    public function myLoans(Request $request)
    {
        $user = $request->user();

        $member = $user->member;
        $loans = collect();

        if ($member) {
            $loans = DB::table('book_member')
                ->join('books', 'book_member.book_id', '=', 'books.id')
                ->join('authors', 'books.author_id', '=', 'authors.id')
                ->where('book_member.member_id', $member->id)
                ->select(
                    'book_member.id as loan_id',
                    'book_member.borrowed_at',
                    'book_member.returned_at',
                    'books.id as book_id',
                    'books.title',
                    'books.isbn',
                    'authors.name as author_name'
                )
                ->orderByRaw('book_member.returned_at IS NULL DESC')
                ->orderByDesc('book_member.borrowed_at')
                ->get();
        }

        return view('loans.my_loans', compact('loans', 'member'));
    }

    /**
     * /desk - librarian circulation desk (role:librarian,admin).
     */
    public function desk(Request $request)
    {
        Gate::authorize('viewAny', Member::class);

        $filter = $request->query('status', 'active'); // 'active', 'all', 'returned'

        $query = DB::table('book_member')
            ->join('books', 'book_member.book_id', '=', 'books.id')
            ->join('authors', 'books.author_id', '=', 'authors.id')
            ->join('members', 'book_member.member_id', '=', 'members.id')
            ->select(
                'book_member.id as loan_id',
                'book_member.borrowed_at',
                'book_member.returned_at',
                'books.id as book_id',
                'books.title as book_title',
                'books.isbn',
                'authors.name as author_name',
                'members.id as member_id',
                'members.name as member_name',
                'members.email as member_email'
            );

        if ($filter === 'active') {
            $query->whereNull('book_member.returned_at');
        } elseif ($filter === 'returned') {
            $query->whereNotNull('book_member.returned_at');
        }

        $loans = $query->orderByRaw('book_member.returned_at IS NULL DESC')
            ->orderByDesc('book_member.borrowed_at')
            ->paginate(15)
            ->withQueryString();

        $activeCount = DB::table('book_member')->whereNull('returned_at')->count();
        $totalCount = DB::table('book_member')->count();

        return view('loans.desk', compact('loans', 'filter', 'activeCount', 'totalCount'));
    }

    /**
     * Mark a loan as returned (role:librarian,admin).
     */
    public function returnBook(Request $request, int $loanId)
    {
        // Only librarians and admins may mark as returned
        if (! $request->user()->hasRole('librarian', 'admin')) {
            abort(403, 'Only library staff can mark books as returned.');
        }

        $loan = DB::table('book_member')->where('id', $loanId)->first();

        if (! $loan) {
            return back()->withErrors(['error' => 'Loan record not found.']);
        }

        if ($loan->returned_at !== null) {
            return back()->with('status', 'This book has already been returned.');
        }

        DB::table('book_member')
            ->where('id', $loanId)
            ->update([
                'returned_at' => now(),
                'updated_at' => now(),
            ]);

        return back()->with('status', 'Book successfully marked as returned.');
    }
}
