<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function myLoans(Request $request): View
    {
        $request->validate(['status' => ['nullable', Rule::in(['all', 'active', 'overdue', 'returned'])]]);
        $member = $request->user()->member;

        if ($member !== null) {
            Gate::authorize('view', $member);
        }

        $filter = $request->input('status') ?: 'all';
        $base = DB::table('book_member')->where('member_id', $member?->id ?? 0);
        $activeCount = (clone $base)->whereNull('returned_at')->count();
        $totalCount = (clone $base)->count();
        $overdueCount = (clone $base)->whereNull('returned_at')->where('borrowed_at', '<', now()->subDays(14))->count();
        $query = $base->join('books', 'book_member.book_id', '=', 'books.id')
            ->join('authors', 'books.author_id', '=', 'authors.id')
            ->select('book_member.id as loan_id', 'book_member.borrowed_at', 'book_member.returned_at', 'books.id as book_id', 'books.title', 'books.isbn', 'authors.name as author_name');
        $this->filterLoans($query, $filter);
        $loans = $query->orderByRaw('book_member.returned_at IS NULL DESC')
            ->orderByDesc('book_member.borrowed_at')->orderByDesc('book_member.id')->paginate(15)->withQueryString();

        return view('loans.my_loans', compact('loans', 'member', 'filter', 'activeCount', 'totalCount', 'overdueCount'));
    }

    public function desk(Request $request): View
    {
        Gate::authorize('viewAny', Member::class);
        $request->validate([
            'status' => ['nullable', Rule::in(['all', 'active', 'overdue', 'returned'])],
            'search' => ['nullable', 'string', 'max:255'],
        ]);
        $filter = $request->input('status') ?: 'active';
        $search = trim($request->string('search')->toString());
        $query = DB::table('book_member')
            ->join('books', 'book_member.book_id', '=', 'books.id')
            ->join('authors', 'books.author_id', '=', 'authors.id')
            ->join('members', 'book_member.member_id', '=', 'members.id')
            ->select('book_member.id as loan_id', 'book_member.borrowed_at', 'book_member.returned_at', 'books.id as book_id', 'books.title as book_title', 'books.isbn', 'authors.name as author_name', 'members.id as member_id', 'members.name as member_name', 'members.email as member_email');

        $this->filterLoans($query, $filter);
        if ($search !== '') {
            $query->where(function (Builder $records) use ($search): void {
                $records->where('books.title', 'like', "%{$search}%")
                    ->orWhere('books.isbn', 'like', "%{$search}%")
                    ->orWhere('members.name', 'like', "%{$search}%")
                    ->orWhere('members.email', 'like', "%{$search}%");
            });
        }

        $loans = $query->orderByRaw('book_member.returned_at IS NULL DESC')
            ->orderByDesc('book_member.borrowed_at')->orderByDesc('book_member.id')->paginate(15)->withQueryString();
        $activeCount = DB::table('book_member')->whereNull('returned_at')->count();
        $totalCount = DB::table('book_member')->count();
        $overdueCount = DB::table('book_member')->whereNull('returned_at')->where('borrowed_at', '<', now()->subDays(14))->count();
        $overlappingBookIds = array_map('intval', DB::table('book_member')->whereNull('returned_at')
            ->select('book_id')->groupBy('book_id')->havingRaw('COUNT(*) > 1')->pluck('book_id')->all());

        return view('loans.desk', compact('loans', 'filter', 'search', 'activeCount', 'totalCount', 'overdueCount', 'overlappingBookIds'));
    }

    public function returnBook(Request $request, int $loanId): RedirectResponse
    {
        Gate::authorize('return', Member::class);
        $loan = DB::table('book_member')->where('id', $loanId)->first();

        if (! $loan) {
            return back()->withErrors(['error' => 'Loan record not found. Refresh the circulation desk and try again.']);
        }

        $updated = DB::table('book_member')->where('id', $loanId)->whereNull('returned_at')
            ->update(['returned_at' => now(), 'updated_at' => now()]);

        return back()->with('status', $updated ? 'Return recorded.' : 'This loan has already been returned.');
    }

    private function filterLoans(Builder $query, string $filter): void
    {
        if ($filter === 'active' || $filter === 'overdue') {
            $query->whereNull('book_member.returned_at');
        } elseif ($filter === 'returned') {
            $query->whereNotNull('book_member.returned_at');
        }

        if ($filter === 'overdue') {
            $query->where('book_member.borrowed_at', '<', now()->subDays(14));
        }
    }
}
