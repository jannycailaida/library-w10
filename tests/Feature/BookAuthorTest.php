<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class BookAuthorTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['librarian'])]
    #[TestWith(['admin'])]
    public function test_staff_can_save_a_new_author_with_a_book_and_select_them_later(string $role): void
    {
        $staff = User::factory()->create(['role' => $role]);

        $this->actingAs($staff)->post('/books', $this->bookData())
            ->assertRedirect()->assertSessionHasNoErrors();

        $author = Author::sole();
        $book = Book::sole();
        $this->assertSame('Mina Dela Cruz', $author->name);
        $this->assertSame($author->id, $book->author_id);
        $this->get(route('books.create'))->assertOk()
            ->assertViewHas('authors', fn ($authors): bool => $authors->contains('id', $author->id));
    }

    public function test_editing_a_book_can_add_a_new_author_without_renaming_the_previous_author(): void
    {
        $staff = User::factory()->librarian()->create();
        $book = Book::factory()->create();
        $previousAuthor = $book->author;

        $this->actingAs($staff)->put(route('books.update', $book), $this->bookData())
            ->assertRedirect(route('books.show', $book))->assertSessionHasNoErrors();

        $this->assertSame('Mina Dela Cruz', $book->fresh()->author->name);
        $this->assertDatabaseHas('authors', ['id' => $previousAuthor->id, 'name' => $previousAuthor->name]);
        $this->assertDatabaseCount('authors', 2);
    }

    public function test_an_existing_author_with_the_same_trimmed_name_is_reused(): void
    {
        $staff = User::factory()->librarian()->create();
        $author = Author::factory()->create(['name' => 'Mina Dela Cruz']);

        $this->actingAs($staff)->post('/books', [...$this->bookData(), 'new_author_name' => '  Mina Dela Cruz  '])
            ->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame($author->id, Book::sole()->author_id);
        $this->assertDatabaseCount('authors', 1);
    }

    public function test_selecting_an_existing_author_ignores_a_stale_new_author_field(): void
    {
        $staff = User::factory()->librarian()->create();
        $author = Author::factory()->create();

        $this->actingAs($staff)->post('/books', [
            ...$this->bookData(), 'author_id' => $author->id, 'new_author_name' => ['unexpected'],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame($author->id, Book::sole()->author_id);
        $this->assertDatabaseCount('authors', 1);
    }

    #[TestWith([null, 'Select an author or choose "Add a new author".'])]
    #[TestWith(['missing', 'Select an author from the list or choose "Add a new author".'])]
    #[TestWith([999999, 'Select an author from the list or choose "Add a new author".'])]
    #[TestWith([['new'], 'Select an author from the list or choose "Add a new author".'])]
    public function test_invalid_author_selection_is_rejected_without_creating_an_author(mixed $authorId, string $message): void
    {
        $staff = User::factory()->librarian()->create();

        $this->actingAs($staff)->from(route('books.create'))->post('/books', [...$this->bookData(), 'author_id' => $authorId])
            ->assertRedirect(route('books.create'))->assertSessionHasErrors(['author_id' => $message]);

        $this->assertDatabaseCount('authors', 0);
        $this->assertDatabaseCount('books', 0);
        $this->get(route('books.create'))->assertOk();
    }

    #[TestWith([null, "Enter the new author's name."])]
    #[TestWith(['   ', "Enter the new author's name."])]
    #[TestWith([['unexpected'], "Enter the new author's name as text."])]
    public function test_a_new_author_requires_a_nonempty_text_name(mixed $name, string $message): void
    {
        $staff = User::factory()->librarian()->create();

        $this->actingAs($staff)->from(route('books.create'))->post('/books', [...$this->bookData(), 'new_author_name' => $name])
            ->assertRedirect(route('books.create'))->assertSessionHasErrors(['new_author_name' => $message]);

        $this->assertDatabaseCount('authors', 0);
        $this->assertDatabaseCount('books', 0);
        $this->get(route('books.create'))->assertOk();
    }

    public function test_a_new_author_name_cannot_exceed_the_database_length(): void
    {
        $staff = User::factory()->librarian()->create();

        $this->actingAs($staff)->post('/books', [...$this->bookData(), 'new_author_name' => str_repeat('a', 192)])
            ->assertSessionHasErrors(['new_author_name' => "The author's name must be 191 characters or fewer."]);

        $this->assertDatabaseCount('authors', 0);
        $this->assertDatabaseCount('books', 0);
    }

    public function test_book_validation_retains_the_new_author_choice_and_name_without_saving_it(): void
    {
        $staff = User::factory()->librarian()->create();

        $this->actingAs($staff)->from(route('books.create'))->post('/books', [...$this->bookData(), 'isbn' => 'abcdefghijklm'])
            ->assertRedirect(route('books.create'))->assertSessionHasErrors('isbn')
            ->assertSessionHas('_old_input.author_id', 'new')
            ->assertSessionHas('_old_input.new_author_name', 'Mina Dela Cruz');

        $this->assertDatabaseCount('authors', 0);
        $this->assertDatabaseCount('books', 0);
        $this->get(route('books.create'))->assertOk()->assertSee('value="new" selected', false)->assertSee('Mina Dela Cruz');
    }

    #[TestWith([null, 302])]
    #[TestWith(['member', 403])]
    public function test_guests_and_members_cannot_create_authors_through_book_writes(?string $role, int $status): void
    {
        $book = Book::factory()->create();
        if ($role !== null) {
            $this->actingAs(User::factory()->create(['role' => $role]));
        }

        $this->post('/books', $this->bookData())->assertStatus($status);
        $this->put(route('books.update', $book), $this->bookData())->assertStatus($status);

        $this->assertDatabaseMissing('authors', ['name' => 'Mina Dela Cruz']);
        $this->assertDatabaseCount('authors', 1);
        $this->assertDatabaseCount('books', 1);
    }

    public function test_a_rejected_reference_conversion_does_not_create_a_new_author(): void
    {
        $staff = User::factory()->librarian()->create();
        $book = Book::factory()->create(['is_reference' => false]);
        Member::factory()->create()->books()->attach($book, ['borrowed_at' => now()]);

        $this->actingAs($staff)->put(route('books.update', $book), [...$this->bookData(), 'is_reference' => 1])
            ->assertSessionHasErrors('is_reference');

        $this->assertDatabaseMissing('authors', ['name' => 'Mina Dela Cruz']);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'author_id' => $book->author_id, 'is_reference' => false]);
    }

    public function test_new_author_names_are_escaped_on_book_pages_and_in_the_author_picker(): void
    {
        $staff = User::factory()->librarian()->create();
        $name = "O'Reilly <script>alert(1)</script>";

        $this->actingAs($staff)->post('/books', [...$this->bookData(), 'new_author_name' => $name])
            ->assertRedirect()->assertSessionHasNoErrors();

        $this->get(route('books.show', Book::sole()))->assertOk()->assertSee($name)->assertDontSee('<script>alert(1)</script>', false);
        $this->get(route('books.create'))->assertOk()->assertSee($name)->assertDontSee('<script>alert(1)</script>', false);
    }

    /** @return array{title: string, author_id: string, new_author_name: string, isbn: string, published_year: int, is_reference: int} */
    private function bookData(): array
    {
        return [
            'title' => 'A book by a new author',
            'author_id' => 'new',
            'new_author_name' => 'Mina Dela Cruz',
            'isbn' => '9781234567897',
            'published_year' => 2026,
            'is_reference' => 0,
        ];
    }
}
