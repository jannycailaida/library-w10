<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $member = $user->member;
        $myActiveLoans = $member?->activeLoans()->with('author')->orderBy('book_member.borrowed_at')->get() ?? collect();
        $myTotalLoansCount = $member?->books()->count() ?? 0;
        $myOverdueCount = $myActiveLoans->filter(fn (Book $book): bool => now()->gt(Carbon::parse($book->pivot->borrowed_at)->addDays(14)))->count();
        $availableBooksCount = Book::available()->count();
        $totalBooks = Book::count();
        $activeLoansCount = 0;
        $overdueLoansCount = 0;
        $totalMembers = 0;
        $recentLoans = collect();

        if ($user->hasRole('librarian', 'admin')) {
            $activeLoansCount = DB::table('book_member')->whereNull('returned_at')->count();
            $overdueLoansCount = DB::table('book_member')->whereNull('returned_at')->where('borrowed_at', '<', now()->subDays(14))->count();
            $totalMembers = Member::count();
            $recentLoans = DB::table('book_member')
                ->join('books', 'book_member.book_id', '=', 'books.id')
                ->join('members', 'book_member.member_id', '=', 'members.id')
                ->select('book_member.borrowed_at', 'book_member.returned_at', 'books.id as book_id', 'books.title as book_title', 'members.name as member_name')
                ->orderByDesc('book_member.updated_at')->orderByDesc('book_member.id')->limit(6)->get();
        }

        return view('dashboard', compact('user', 'member', 'myActiveLoans', 'myTotalLoansCount', 'myOverdueCount', 'availableBooksCount', 'totalBooks', 'activeLoansCount', 'overdueLoansCount', 'totalMembers', 'recentLoans'));
    }
}
