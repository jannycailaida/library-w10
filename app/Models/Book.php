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

    /**
     * Category slug for filtering.
     */
    public function getCategorySlugAttribute(): string
    {
        if ($this->is_reference) {
            return 'reference';
        }

        $author = $this->author?->name ?? '';
        if (str_contains($author, 'Martin') || str_contains($author, 'Cormen') || str_contains($author, 'Silberschatz')) {
            return 'cs';
        }
        if (str_contains($author, 'Stewart')) {
            return 'math';
        }
        if (str_contains($author, 'Young')) {
            return 'physics';
        }
        if (str_contains($author, 'Campbell')) {
            return 'biology';
        }
        if (str_contains($author, 'Mankiw')) {
            return 'economics';
        }
        if (str_contains($author, 'Fitzgerald') || str_contains($author, 'Orwell')) {
            return 'literature';
        }

        return 'general';
    }

    /**
     * Category display name.
     */
    public function getCategoryNameAttribute(): string
    {
        return match ($this->category_slug) {
            'cs' => 'Computer Science',
            'math' => 'Mathematics',
            'physics' => 'Physics & Engineering',
            'biology' => 'Biology & Life Sciences',
            'economics' => 'Economics & Business',
            'literature' => 'Literature & Classics',
            'reference' => 'Reference Collection',
            default => 'General Studies',
        };
    }

    /**
     * Category Icon emoji.
     */
    public function getCategoryIconAttribute(): string
    {
        return match ($this->category_slug) {
            'cs' => '💻',
            'math' => '📐',
            'physics' => '🔬',
            'biology' => '🧬',
            'economics' => '📈',
            'literature' => '📚',
            'reference' => '🔒',
            default => '📖',
        };
    }
}
