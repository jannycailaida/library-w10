<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\Member;
use App\Models\User;

class LoanPolicy
{
    /**
     * Admin gets automatic pass via before().
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    /**
     * View all loans across the library (desk view).
     * Librarians and admins only.
     */
    public function viewAny(User $user): bool
    {
        return $user->isLibrarian();
    }

    /**
     * A member may view only their own loans; librarians view all.
     */
    public function view(User $user, Member $member): bool
    {
        return $user->isLibrarian() || ($user->member && $user->member->id === $member->id);
    }

    /**
     * Only librarians (and admins via before()) mark a loan returned.
     */
    public function return(User $user): bool
    {
        return $user->isLibrarian();
    }

    /**
     * Check if a user can borrow a book:
     * Must have a linked member record, and reference books cannot be borrowed.
     */
    public function borrow(User $user, Book $book): bool
    {
        return $user->member !== null && $book->isBorrowable();
    }
}
