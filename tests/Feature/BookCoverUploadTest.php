<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Member;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class BookCoverUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_upload_a_cover_when_creating_a_book(): void
    {
        Storage::fake('public');
        $staff = User::factory()->librarian()->create();
        $image = $this->coverImage();

        $this->actingAs($staff)->post('/books', [...$this->bookData(), 'cover_image' => $image])
            ->assertRedirect()->assertSessionHasNoErrors();

        $book = Book::sole();
        $this->assertSame('/storage/book-covers/'.$image->hashName(), $book->cover_path);
        Storage::disk('public')->assertExists('book-covers/'.$image->hashName());
        $this->get(route('books.show', $book))->assertOk()->assertSee(asset($book->cover_path));
        $this->get('/books')->assertOk()->assertSee(asset($book->cover_path));
    }

    public function test_editing_without_a_new_upload_keeps_the_current_cover(): void
    {
        Storage::fake('public');
        $staff = User::factory()->librarian()->create();
        $path = $this->coverImage()->store('book-covers', 'public');
        $book = Book::factory()->create(['is_reference' => false, 'cover_path' => '/storage/'.$path]);

        $this->actingAs($staff)->put(route('books.update', $book), $this->bookData($book))
            ->assertRedirect(route('books.show', $book))->assertSessionHasNoErrors();

        $this->assertSame('/storage/'.$path, $book->fresh()->cover_path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_uploading_a_replacement_removes_the_previous_uploaded_cover(): void
    {
        Storage::fake('public');
        $staff = User::factory()->librarian()->create();
        $previousPath = $this->coverImage()->store('book-covers', 'public');
        $book = Book::factory()->create(['is_reference' => false, 'cover_path' => '/storage/'.$previousPath]);
        $image = $this->coverImage();

        $this->actingAs($staff)->put(route('books.update', $book), [...$this->bookData($book), 'cover_image' => $image])
            ->assertRedirect(route('books.show', $book))->assertSessionHasNoErrors();

        $this->assertSame('/storage/book-covers/'.$image->hashName(), $book->fresh()->cover_path);
        Storage::disk('public')->assertExists('book-covers/'.$image->hashName());
        Storage::disk('public')->assertMissing($previousPath);
    }

    #[TestWith(['/images/books/original.jpg'])]
    #[TestWith(['https://example.test/original.jpg'])]
    public function test_existing_local_and_remote_covers_survive_edits_without_an_upload(string $coverPath): void
    {
        $staff = User::factory()->librarian()->create();
        $book = Book::factory()->create(['is_reference' => false, 'cover_path' => $coverPath]);

        $this->actingAs($staff)->put(route('books.update', $book), $this->bookData($book))
            ->assertRedirect(route('books.show', $book))->assertSessionHasNoErrors();

        $this->assertSame($coverPath, $book->fresh()->cover_path);
    }

    #[TestWith(['/storage/keep.png'])]
    #[TestWith(['/storage/book-covers/../keep.png'])]
    public function test_replacing_a_legacy_path_does_not_delete_unrelated_files(string $coverPath): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('keep.png', 'Existing file outside managed covers');
        $staff = User::factory()->librarian()->create();
        $book = Book::factory()->create(['is_reference' => false, 'cover_path' => $coverPath]);
        $image = $this->coverImage();

        $this->actingAs($staff)->put(route('books.update', $book), [...$this->bookData($book), 'cover_image' => $image])
            ->assertRedirect(route('books.show', $book))->assertSessionHasNoErrors();

        $this->assertSame('/storage/book-covers/'.$image->hashName(), $book->fresh()->cover_path);
        Storage::disk('public')->assertExists('keep.png');
    }

    #[TestWith(['cover.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>', 'image/svg+xml'])]
    #[TestWith(['cover.png', 'This is not an image.', 'text/plain'])]
    public function test_invalid_image_contents_are_rejected_without_storing_a_book(string $name, string $contents, string $mimeType): void
    {
        Storage::fake('public');
        $staff = User::factory()->librarian()->create();
        $file = UploadedFile::fake()->createWithContent($name, $contents)->mimeType($mimeType);

        $this->actingAs($staff)->post('/books', [...$this->bookData(), 'cover_image' => $file])
            ->assertRedirect()->assertSessionHasErrors(['cover_image' => 'Choose a JPG, PNG, or WebP image.']);

        $this->assertDatabaseCount('books', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_covers_larger_than_two_megabytes_are_rejected(): void
    {
        Storage::fake('public');
        $staff = User::factory()->librarian()->create();
        $image = $this->coverImage()->size(2049);

        $this->actingAs($staff)->post('/books', [...$this->bookData(), 'cover_image' => $image])
            ->assertRedirect()->assertSessionHasErrors(['cover_image' => 'The cover image must be 2 MB or smaller.']);

        $this->assertDatabaseCount('books', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_gif_images_are_rejected_by_the_supported_format_list(): void
    {
        Storage::fake('public');
        $staff = User::factory()->librarian()->create();
        $image = UploadedFile::fake()->createWithContent('cover.gif', base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=='));

        $this->actingAs($staff)->post('/books', [...$this->bookData(), 'cover_image' => $image])
            ->assertRedirect()->assertSessionHasErrors(['cover_image' => 'Choose a JPG, PNG, or WebP image.']);

        $this->assertDatabaseCount('books', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_a_storage_write_failure_does_not_create_a_book_without_its_cover(): void
    {
        $staff = User::factory()->librarian()->create();
        $disk = Mockery::mock(FilesystemAdapter::class);
        $disk->shouldReceive('putFileAs')->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('public')->andReturn($disk);

        $this->actingAs($staff)->post('/books', [...$this->bookData(), 'cover_image' => $this->coverImage()])
            ->assertRedirect()->assertSessionHasErrors(['cover_image' => 'The cover image could not be saved. Please try again.']);

        $this->assertDatabaseCount('books', 0);
    }

    public function test_rejected_book_updates_remove_the_new_file_and_preserve_the_old_cover(): void
    {
        Storage::fake('public');
        $staff = User::factory()->librarian()->create();
        $previousPath = $this->coverImage()->store('book-covers', 'public');
        $book = Book::factory()->create(['is_reference' => false, 'cover_path' => '/storage/'.$previousPath]);
        Member::factory()->create()->books()->attach($book, ['borrowed_at' => now()]);
        $image = $this->coverImage();

        $this->actingAs($staff)->put(route('books.update', $book), [...$this->bookData($book), 'is_reference' => 1, 'cover_image' => $image])
            ->assertRedirect()->assertSessionHasErrors('is_reference');

        $this->assertSame('/storage/'.$previousPath, $book->fresh()->cover_path);
        Storage::disk('public')->assertExists($previousPath);
        Storage::disk('public')->assertMissing('book-covers/'.$image->hashName());
    }

    public function test_deleting_an_unused_book_removes_its_uploaded_cover(): void
    {
        Storage::fake('public');
        $staff = User::factory()->librarian()->create();
        $path = $this->coverImage()->store('book-covers', 'public');
        $book = Book::factory()->create(['is_reference' => false, 'cover_path' => '/storage/'.$path]);

        $this->actingAs($staff)->delete(route('books.destroy', $book))->assertRedirect('/books');

        $this->assertModelMissing($book);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_a_book_with_loan_history_keeps_its_cover_when_deletion_is_blocked(): void
    {
        Storage::fake('public');
        $staff = User::factory()->librarian()->create();
        $path = $this->coverImage()->store('book-covers', 'public');
        $book = Book::factory()->create(['is_reference' => false, 'cover_path' => '/storage/'.$path]);
        Member::factory()->create()->books()->attach($book, ['borrowed_at' => now()]);

        $this->actingAs($staff)->delete(route('books.destroy', $book))->assertSessionHasErrors('error');

        $this->assertModelExists($book);
        Storage::disk('public')->assertExists($path);
    }

    public function test_members_cannot_upload_covers_through_book_creation(): void
    {
        Storage::fake('public');
        $user = User::factory()->member()->create();

        $this->actingAs($user)->post('/books', [...$this->bookData(), 'cover_image' => $this->coverImage()])
            ->assertForbidden();

        $this->assertDatabaseCount('books', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    /** @return array{title: string, author_id: int, isbn: string, published_year: int, is_reference: int} */
    private function bookData(?Book $book = null): array
    {
        return [
            'title' => 'Campus computing',
            'author_id' => $book?->author_id ?? Author::factory()->create()->id,
            'isbn' => $book?->isbn ?? '9781234567897',
            'published_year' => 2026,
            'is_reference' => 0,
        ];
    }

    private function coverImage(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('cover.png', base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l9sAAAAASUVORK5CYII='
        ));
    }
}
