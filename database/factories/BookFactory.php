<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'author_id' => Author::factory(),
            'isbn' => fake()->unique()->numerify('#############'),
            'title' => fake()->catchPhrase(),
            'published_year' => fake()->numberBetween(1980, 2025),
            'is_reference' => fake()->boolean(15), // ~15% reference books
            'cover_path' => null,
        ];
    }

    public function reference(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_reference' => true,
        ]);
    }
}
