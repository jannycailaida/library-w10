<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        if (array_key_exists('active_loans_count', $this->attributes)) {
            return $this->active_loans_count > 0;
        }

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

    /** @return array<string, array{name: string, authors: list<string>}> */
    public static function categories(): array
    {
        return [
            'cs' => ['name' => 'Computer science', 'authors' => ['Martin', 'Cormen', 'Silberschatz']],
            'math' => ['name' => 'Mathematics', 'authors' => ['Stewart']],
            'physics' => ['name' => 'Physics & engineering', 'authors' => ['Young']],
            'biology' => ['name' => 'Biology & life sciences', 'authors' => ['Campbell']],
            'economics' => ['name' => 'Economics & business', 'authors' => ['Mankiw']],
            'literature' => ['name' => 'Literature & classics', 'authors' => ['Fitzgerald', 'Orwell']],
            'reference' => ['name' => 'Reference collection', 'authors' => []],
            'general' => ['name' => 'General studies', 'authors' => []],
        ];
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_reference', false)
            ->whereDoesntHave('members', fn (Builder $loans): Builder => $loans->whereNull('returned_at'));
    }

    public function scopeInCategory(Builder $query, string $category): Builder
    {
        if ($category === 'reference') {
            return $query->where('is_reference', true);
        }

        $keywords = self::categories()[$category]['authors'] ?? [];
        $query->where('is_reference', false);

        if ($category === 'general') {
            $keywords = array_merge(...array_column(self::categories(), 'authors'));

            return $query->whereHas('author', function (Builder $authors) use ($keywords): void {
                foreach ($keywords as $keyword) {
                    $authors->where('name', 'not like', "%{$keyword}%");
                }
            });
        }

        return $query->whereHas('author', function (Builder $authors) use ($keywords): void {
            $authors->where(function (Builder $names) use ($keywords): void {
                foreach ($keywords as $keyword) {
                    $names->orWhere('name', 'like', "%{$keyword}%");
                }
            });
        });
    }

    /**
     * Category slug for filtering.
     */
    public function getCategorySlugAttribute(): string
    {
        if ($this->is_reference) {
            return 'reference';
        }

        foreach (self::categories() as $slug => $category) {
            foreach ($category['authors'] as $keyword) {
                if (stripos($this->author?->name ?? '', $keyword) !== false) {
                    return $slug;
                }
            }
        }

        return 'general';
    }

    /**
     * Category display name.
     */
    public function getCategoryNameAttribute(): string
    {
        return self::categories()[$this->category_slug]['name'];
    }
}
