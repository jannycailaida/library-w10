<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'isbn',
        'title',
        'published_year',
        'is_reference',
        'cover_path',
    ];

    protected function casts(): array
    {
        return [
            'is_reference' => 'boolean',
            'published_year' => 'integer',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'book_member')
            ->withPivot(['id', 'borrowed_at', 'returned_at'])
            ->withTimestamps();
    }

    /**
     * Check if the book is currently borrowed (active loan).
     */
    public function isBorrowed(): bool
    {
        return $this->members()
            ->wherePivotNull('returned_at')
            ->exists();
    }

    /**
     * Check if this book can be borrowed.
     * Reference books cannot be borrowed.
     */
    public function isBorrowable(): bool
    {
        return ! $this->is_reference && ! $this->isBorrowed();
    }
}
