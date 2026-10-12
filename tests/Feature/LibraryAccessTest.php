<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $librarian;

    private User $memberUser;

    private Member $member;

    private Book $normalBook;

    private Book $referenceBook;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->librarian = User::factory()->librarian()->create();
        $this->memberUser = User::factory()->member()->create();

        $this->member = Member::create([
            'user_id' => $this->memberUser->id,
            'name' => $this->memberUser->name,
            'email' => $this->memberUser->email,
        ]);

        $author = Author::create(['name' => 'Robert C. Martin']);

        $this->normalBook = Book::create([
            'author_id' => $author->id,
            'title' => 'Clean Code',
            'isbn' => '9780132350884',
            'published_year' => 2008,
            'is_reference' => false,
        ]);

        $this->referenceBook = Book::create([
            'author_id' => $author->id,
            'title' => 'Oxford English Dictionary',
            'isbn' => '9780198611868',
            'published_year' => 2010,
            'is_reference' => true,
        ]);
    }

    public function test_guest_can_view_catalogue_and_book_details(): void
    {
        $this->get('/books')->assertStatus(200);
        $this->get("/books/{$this->normalBook->id}")->assertStatus(200);
    }

    public function test_guest_redirected_to_login_when_accessing_protected_routes(): void
    {
        $this->get('/books/create')->assertRedirect('/login');
        $this->get('/my/loans')->assertRedirect('/login');
        $this->get('/desk')->assertRedirect('/login');
        $this->get('/admin/users')->assertRedirect('/login');
    }

    public function test_member_cannot_create_books(): void
    {
        $this->actingAs($this->memberUser)
            ->get('/books/create')
            ->assertStatus(403);
    }

    public function test_librarian_and_admin_can_access_book_creation(): void
    {
        $this->actingAs($this->librarian)
            ->get('/books/create')
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get('/books/create')
            ->assertStatus(200);
    }

    public function test_member_can_borrow_standard_book(): void
    {
        $response = $this->actingAs($this->memberUser)
            ->post("/books/{$this->normalBook->id}/borrow");

        $response->assertRedirect('/my/loans');
        $this->assertDatabaseHas('book_member', [
            'book_id' => $this->normalBook->id,
            'member_id' => $this->member->id,
            'returned_at' => null,
        ]);
    }

    public function test_member_cannot_borrow_reference_book(): void
    {
        $this->actingAs($this->memberUser)
            ->post("/books/{$this->referenceBook->id}/borrow")
            ->assertStatus(403);
    }

    public function test_member_cannot_access_circulation_desk_or_admin(): void
    {
        $this->actingAs($this->memberUser)
            ->get('/desk')
            ->assertStatus(403);

        $this->actingAs($this->memberUser)
            ->get('/admin/users')
            ->assertStatus(403);
    }

    public function test_librarian_can_access_circulation_desk_but_not_admin(): void
    {
        $this->actingAs($this->librarian)
            ->get('/desk')
            ->assertStatus(200);

        $this->actingAs($this->librarian)
            ->get('/admin/users')
            ->assertStatus(403);
    }

    public function test_admin_can_access_all_areas(): void
    {
        $this->actingAs($this->admin)
            ->get('/desk')
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get('/admin/users')
            ->assertStatus(200);
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $this->actingAs($this->admin)
            ->patch("/admin/users/{$this->admin->id}/role", [
                'role' => 'member',
            ])
            ->assertStatus(403);
    }

    public function test_admin_can_change_other_user_role(): void
    {
        $this->actingAs($this->admin)
            ->patch("/admin/users/{$this->memberUser->id}/role", [
                'role' => 'librarian',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $this->memberUser->id,
            'role' => 'librarian',
        ]);
    }
}
