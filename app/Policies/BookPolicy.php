<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    /**
     * Runs before every other method.
     * Admin gets automatic pass; null delegates to specific method.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    /**
     * Anyone (including guests) can view the book list.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Anyone (including guests) can view a specific book.
     */
    public function view(?User $user, Book $book): bool
    {
        return true;
    }

    /**
     * Only librarians (and admins via before()) can create books.
     */
    public function create(User $user): bool
    {
        return $user->isLibrarian();
    }

    /**
     * Only librarians (and admins via before()) can update books.
     */
    public function update(User $user, Book $book): bool
    {
        return $user->isLibrarian();
    }

    /**
     * Only librarians (and admins via before()) can delete books.
     */
    public function delete(User $user, Book $book): bool
    {
        return $user->isLibrarian();
    }
}
