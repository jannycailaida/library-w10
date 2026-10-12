<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Redirect root to book catalogue
Route::get('/', fn () => redirect()->route('books.index'));

// Dashboard
Route::get('/dashboard', function () {
    return redirect()->route('books.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // /my/loans - Member loans
    Route::get('/my/loans', [LoanController::class, 'myLoans'])->name('my.loans');

    // Borrow action on book page
    Route::post('/books/{book}/borrow', [BookController::class, 'borrow'])->name('books.borrow');

    // Book management (create, store, edit, update, destroy protected by BookPolicy)
    Route::resource('books', BookController::class)->except(['index', 'show']);

    // /desk - Librarian circulation desk (Librarians and Admins only)
    Route::middleware('role:librarian,admin')->group(function () {
        Route::get('/desk', [LoanController::class, 'desk'])->name('desk');
        Route::patch('/desk/loans/{loan}/return', [LoanController::class, 'returnBook'])->name('desk.return');
    });

    // /admin/users - Admin user & role management (Admins only)
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.update-role');
    });
});

// Public book catalogue: index and show
// Placed after resource except to prevent route conflicts
Route::resource('books', BookController::class)->only(['index', 'show']);

require __DIR__.'/auth.php';
