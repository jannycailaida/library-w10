<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class LibraryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeder_assigns_documented_roles_and_valid_loan_history(): void
    {
        $this->seed();

        $this->assertDatabaseHas('users', ['email' => 'admin@library.test', 'role' => 'admin']);
        $this->assertDatabaseHas('users', ['email' => 'librarian@library.test', 'role' => 'librarian']);
        $this->assertDatabaseHas('users', ['email' => 'member@library.test', 'role' => 'member']);
        $this->assertDatabaseCount('books', 40);
        $this->assertDatabaseCount('members', 30);
        $duplicates = DB::table('book_member')->whereNull('returned_at')->select('book_id')->groupBy('book_id')->havingRaw('COUNT(*) > 1')->get();
        $this->assertCount(0, $duplicates);
        $this->assertSame(0, DB::table('book_member')->where('returned_at', '>', now())->count());
    }

    public function test_registration_creates_a_member_card_without_accepting_a_privileged_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Mara Santos', 'email' => 'mara@example.test',
            'password' => 'library-password', 'password_confirmation' => 'library-password', 'role' => 'admin',
        ]);

        $response->assertRedirect('/dashboard')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['email' => 'mara@example.test', 'role' => 'member']);
        $this->assertDatabaseHas('members', ['user_id' => auth()->id(), 'email' => 'mara@example.test', 'name' => 'Mara Santos']);
    }

    public function test_available_filter_excludes_reference_and_active_loans(): void
    {
        $available = Book::factory()->create(['is_reference' => false]);
        $borrowed = Book::factory()->create(['is_reference' => false]);
        Book::factory()->reference()->create();
        $member = Member::factory()->create();
        $member->books()->attach($borrowed, ['borrowed_at' => now()]);

        $this->get('/books?filter=available')->assertOk()
            ->assertViewHas('books', fn ($books): bool => $books->pluck('id')->all() === [$available->id]);
    }

    public function test_subject_filter_excludes_reference_titles_by_the_same_author(): void
    {
        $author = Author::factory()->create(['name' => 'Robert C. Martin']);
        $normal = Book::factory()->for($author)->create(['is_reference' => false]);
        Book::factory()->for($author)->reference()->create();

        $this->get('/books?category=cs')->assertOk()
            ->assertViewHas('books', fn ($books): bool => $books->pluck('id')->all() === [$normal->id]);
    }

    #[TestWith(['Practical distributed systems'])]
    #[TestWith(['Mina Rosales'])]
    #[TestWith(['9781234567897'])]
    public function test_search_finds_title_author_or_isbn(string $search): void
    {
        $author = Author::factory()->create(['name' => 'Mina Rosales']);
        $book = Book::factory()->for($author)->create(['title' => 'Practical distributed systems', 'isbn' => '9781234567897', 'is_reference' => false]);
        Book::factory()->create(['is_reference' => false]);

        $this->get('/books?'.http_build_query(['search' => $search]))->assertOk()
            ->assertViewHas('books', fn ($books): bool => $books->pluck('id')->all() === [$book->id]);
    }

    public function test_catalogue_escapes_titles_with_apostrophes_and_html(): void
    {
        $book = Book::factory()->create(['title' => "A Craftsman's Guide <script>alert(1)</script>", 'is_reference' => false]);

        $this->get('/books')->assertOk()->assertSee($book->title)->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_invalid_query_shapes_are_rejected_instead_of_crashing(): void
    {
        $this->get('/books?search[]=unexpected')->assertRedirect()->assertSessionHasErrors('search');
    }

    public function test_member_dashboard_counts_only_their_own_loans(): void
    {
        $user = User::factory()->member()->create();
        $member = Member::factory()->for($user)->create();
        $other = Member::factory()->create();
        $member->books()->attach(Book::factory()->create(['is_reference' => false]), ['borrowed_at' => now()]);
        $other->books()->attach(Book::factory()->create(['is_reference' => false]), ['borrowed_at' => now()]);

        $this->actingAs($user)->get('/dashboard')->assertOk()
            ->assertViewHas('myActiveLoans', fn ($books): bool => $books->count() === 1)
            ->assertViewHas('myTotalLoansCount', 1)->assertViewHas('myOverdueCount', 0);
    }

    public function test_due_dates_and_overdue_filter_use_the_fourteen_day_loan_period(): void
    {
        $this->travelTo(Carbon::parse('2026-10-15 12:00:00'));
        $user = User::factory()->member()->create();
        $member = Member::factory()->for($user)->create();
        $overdue = Book::factory()->create(['title' => 'Overdue library title', 'is_reference' => false]);
        $current = Book::factory()->create(['title' => 'Current library title', 'is_reference' => false]);
        $member->books()->attach($overdue, ['borrowed_at' => '2026-09-30 12:00:00']);
        $member->books()->attach($current, ['borrowed_at' => '2026-10-14 12:00:00']);

        $this->actingAs($user)->get('/my/loans?status=overdue')->assertOk()
            ->assertSee('Oct 14, 2026')->assertSee($overdue->title)->assertDontSee($current->title)
            ->assertViewHas('overdueCount', 1);
    }

    public function test_personal_loan_history_does_not_expose_another_members_records(): void
    {
        $user = User::factory()->member()->create();
        Member::factory()->for($user)->create();
        $other = Member::factory()->create();
        $book = Book::factory()->create(['is_reference' => false]);
        $other->books()->attach($book, ['borrowed_at' => now()]);

        $this->actingAs($user)->get('/my/loans')->assertOk()->assertDontSee($book->title)->assertViewHas('totalCount', 0);
    }

    #[TestWith(['member', false])]
    #[TestWith(['librarian', true])]
    #[TestWith(['admin', true])]
    public function test_loan_policy_limits_other_members_history_to_staff(string $role, bool $canViewOtherMember): void
    {
        $user = User::factory()->create(['role' => $role]);
        $ownMember = Member::factory()->for($user)->create();
        $otherMember = Member::factory()->create();

        $this->assertTrue(Gate::forUser($user)->allows('view', $ownMember));
        $this->assertSame($canViewOtherMember, Gate::forUser($user)->allows('view', $otherMember));
    }

    #[TestWith(['member'])]
    #[TestWith(['librarian'])]
    #[TestWith(['admin'])]
    public function test_account_without_a_library_card_sees_empty_personal_loans(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);
        $otherMember = Member::factory()->create();
        $book = Book::factory()->create(['is_reference' => false]);
        $otherMember->books()->attach($book, ['borrowed_at' => now()]);

        $this->actingAs($user)->get('/my/loans')->assertOk()
            ->assertViewHas('member', null)->assertViewHas('totalCount', 0)->assertDontSee($book->title);
        $this->assertDatabaseCount('members', 1);
    }

    public function test_already_borrowed_book_does_not_create_a_second_loan(): void
    {
        $user = User::factory()->member()->create();
        $book = Book::factory()->create(['is_reference' => false]);
        $first = Member::factory()->create();
        $first->books()->attach($book, ['borrowed_at' => now()]);

        $this->actingAs($user)->from('/books')->post(route('books.borrow', $book))
            ->assertRedirect('/books')->assertSessionHasErrors('error');
        $this->assertDatabaseCount('book_member', 1);
        $this->assertDatabaseHas('book_member', ['book_id' => $book->id, 'member_id' => $first->id, 'returned_at' => null]);
    }

    public function test_borrowing_ignores_a_member_id_supplied_by_the_client(): void
    {
        $user = User::factory()->member()->create();
        $ownMember = Member::factory()->for($user)->create();
        $otherMember = Member::factory()->create();
        $book = Book::factory()->create(['is_reference' => false]);

        $this->actingAs($user)->post(route('books.borrow', $book), ['member_id' => $otherMember->id])
            ->assertRedirect('/my/loans')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('book_member', ['book_id' => $book->id, 'member_id' => $ownMember->id]);
        $this->assertDatabaseCount('book_member', 1);
    }

    #[TestWith(['librarian'])]
    #[TestWith(['admin'])]
    public function test_staff_can_borrow_and_receive_a_member_card(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);
        $book = Book::factory()->create(['is_reference' => false]);

        $this->actingAs($user)->post(route('books.borrow', $book))->assertRedirect('/my/loans')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('members', ['user_id' => $user->id]);
        $this->assertDatabaseHas('book_member', ['book_id' => $book->id, 'member_id' => $user->member->id]);
    }

    public function test_member_cannot_record_returns(): void
    {
        $user = User::factory()->member()->create();
        $member = Member::factory()->for($user)->create();
        $book = Book::factory()->create(['is_reference' => false]);
        $member->books()->attach($book, ['borrowed_at' => now()]);
        $loanId = $member->books()->first()->pivot->id;

        $this->actingAs($user)->patch(route('desk.return', $loanId))->assertForbidden();
        $this->assertDatabaseHas('book_member', ['id' => $loanId, 'returned_at' => null]);
    }

    public function test_staff_return_restores_availability(): void
    {
        $this->travelTo(Carbon::parse('2026-10-15 12:00:00'));
        $staff = User::factory()->librarian()->create();
        $member = Member::factory()->create();
        $book = Book::factory()->create(['is_reference' => false]);
        $member->books()->attach($book, ['borrowed_at' => now()->subDays(5)]);
        $loanId = $member->books()->first()->pivot->id;

        $this->actingAs($staff)->from('/desk')->patch(route('desk.return', $loanId))->assertRedirect('/desk')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('book_member', ['id' => $loanId, 'returned_at' => '2026-10-15 12:00:00']);
        $this->assertTrue($book->fresh()->isBorrowable());
    }

    public function test_repeated_return_preserves_the_original_return_time(): void
    {
        $staff = User::factory()->librarian()->create();
        $member = Member::factory()->create();
        $book = Book::factory()->create(['is_reference' => false]);
        $member->books()->attach($book, ['borrowed_at' => '2026-10-01 12:00:00', 'returned_at' => '2026-10-05 12:00:00']);
        $loanId = $member->books()->first()->pivot->id;

        $this->actingAs($staff)->from('/desk')->patch(route('desk.return', $loanId))->assertRedirect('/desk');
        $this->assertDatabaseHas('book_member', ['id' => $loanId, 'returned_at' => '2026-10-05 12:00:00']);
    }

    public function test_returning_one_overlapping_loan_keeps_the_other_loan_active_without_claiming_availability(): void
    {
        $this->travelTo(Carbon::parse('2026-10-15 12:00:00'));
        $staff = User::factory()->librarian()->create();
        $book = Book::factory()->create(['is_reference' => false]);
        $members = Member::factory()->count(2)->create();
        foreach ($members as $member) {
            $member->books()->attach($book, ['borrowed_at' => '2026-10-01 12:00:00']);
        }
        $loanId = $members->first()->books()->first()->pivot->id;

        $this->actingAs($staff)->from('/desk')->patch(route('desk.return', $loanId))
            ->assertRedirect('/desk')->assertSessionHas('status', 'Return recorded.');
        $this->assertDatabaseHas('book_member', ['id' => $loanId, 'returned_at' => '2026-10-15 12:00:00']);
        $this->assertDatabaseHas('book_member', ['book_id' => $book->id, 'member_id' => $members->last()->id, 'returned_at' => null]);
        $this->assertFalse($book->fresh()->isBorrowable());
    }

    public function test_desk_search_finds_a_reader_by_email(): void
    {
        $staff = User::factory()->librarian()->create();
        $member = Member::factory()->create(['email' => 'circulation@example.test']);
        $book = Book::factory()->create(['is_reference' => false]);
        $member->books()->attach($book, ['borrowed_at' => now()]);

        $this->actingAs($staff)->get('/desk?search=circulation%40example.test')->assertOk()
            ->assertViewHas('loans', fn ($loans): bool => $loans->total() === 1)->assertSee($book->title);
    }

    public function test_desk_flags_existing_overlapping_loans_without_modifying_them(): void
    {
        $staff = User::factory()->librarian()->create();
        $book = Book::factory()->create(['is_reference' => false]);
        foreach (Member::factory()->count(2)->create() as $member) {
            $member->books()->attach($book, ['borrowed_at' => now()]);
        }

        $this->actingAs($staff)->get('/desk')->assertOk()->assertSee('more than one active loan')
            ->assertViewHas('overlappingBookIds', [$book->id]);
        $this->assertDatabaseCount('book_member', 2);
        $this->assertSame(2, DB::table('book_member')->whereNull('returned_at')->count());
    }

    public function test_librarian_can_create_a_book(): void
    {
        $staff = User::factory()->librarian()->create();
        $author = Author::factory()->create();

        $this->actingAs($staff)->post('/books', ['title' => 'Campus computing', 'author_id' => $author->id, 'isbn' => '9781234567897', 'published_year' => 2025, 'is_reference' => 0])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('books', ['title' => 'Campus computing', 'isbn' => '9781234567897', 'is_reference' => false]);
    }

    public function test_book_edit_accepts_its_own_unchanged_isbn(): void
    {
        $staff = User::factory()->librarian()->create();
        $book = Book::factory()->create(['is_reference' => false]);

        $this->actingAs($staff)->put(route('books.update', $book), ['title' => 'Revised campus computing', 'author_id' => $book->author_id, 'isbn' => $book->isbn, 'published_year' => 2026, 'is_reference' => 0])
            ->assertRedirect(route('books.show', $book))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => 'Revised campus computing']);
    }

    public function test_isbn_requires_digits_instead_of_any_thirteen_characters(): void
    {
        $staff = User::factory()->librarian()->create();
        $author = Author::factory()->create();

        $this->actingAs($staff)->post('/books', ['title' => 'Invalid ISBN example', 'author_id' => $author->id, 'isbn' => 'abcdefghijklm', 'published_year' => 2025])
            ->assertSessionHasErrors(['isbn' => 'Enter an ISBN containing exactly 13 digits.']);
        $this->assertDatabaseCount('books', 0);
    }

    public function test_active_loan_cannot_be_converted_into_reference_material(): void
    {
        $staff = User::factory()->librarian()->create();
        $book = Book::factory()->create(['is_reference' => false]);
        Member::factory()->create()->books()->attach($book, ['borrowed_at' => now()]);

        $this->actingAs($staff)->put(route('books.update', $book), ['title' => $book->title, 'author_id' => $book->author_id, 'isbn' => $book->isbn, 'published_year' => $book->published_year, 'is_reference' => 1])
            ->assertSessionHasErrors('is_reference');
        $this->assertDatabaseHas('books', ['id' => $book->id, 'is_reference' => false]);
    }

    #[TestWith([null])]
    #[TestWith(['2026-10-05 12:00:00'])]
    public function test_book_deletion_preserves_existing_loan_history(?string $returnedAt): void
    {
        $staff = User::factory()->librarian()->create();
        $book = Book::factory()->create(['is_reference' => false]);
        Member::factory()->create()->books()->attach($book, ['borrowed_at' => '2026-10-01 12:00:00', 'returned_at' => $returnedAt]);

        $this->actingAs($staff)->delete(route('books.destroy', $book))->assertSessionHasErrors('error');
        $this->assertModelExists($book);
        $this->assertDatabaseCount('book_member', 1);
    }

    public function test_staff_can_delete_a_book_without_loan_history(): void
    {
        $staff = User::factory()->librarian()->create();
        $book = Book::factory()->create(['is_reference' => false]);

        $this->actingAs($staff)->delete(route('books.destroy', $book))->assertRedirect('/books');
        $this->assertModelMissing($book);
    }

    public function test_profile_update_also_updates_the_linked_library_card(): void
    {
        $user = User::factory()->create();
        $member = Member::factory()->for($user)->create();

        $this->actingAs($user)->patch('/profile', ['name' => 'Updated Reader', 'email' => 'updated@example.test'])->assertSessionHasNoErrors()->assertRedirect('/profile');
        $this->assertDatabaseHas('members', ['id' => $member->id, 'name' => 'Updated Reader', 'email' => 'updated@example.test']);
    }

    public function test_active_borrowers_cannot_delete_their_account(): void
    {
        $user = User::factory()->create();
        $member = Member::factory()->for($user)->create();
        $book = Book::factory()->create(['is_reference' => false]);
        $member->books()->attach($book, ['borrowed_at' => now()]);

        $this->actingAs($user)->from('/profile')->delete('/profile', ['password' => 'password'])
            ->assertRedirect('/profile')->assertSessionHasErrorsIn('userDeletion', 'password');
        $this->assertAuthenticatedAs($user);
        $this->assertModelExists($user);
        $this->assertDatabaseCount('book_member', 1);
    }

    public function test_admin_user_search_and_pagination_are_scoped_to_the_query(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->member()->create(['name' => 'Reader target']);
        User::factory()->librarian()->create();

        $this->actingAs($admin)->get('/admin/users?search=Reader%20target&role=member')->assertOk()
            ->assertViewHas('users', fn ($users): bool => $users->pluck('id')->all() === [$target->id]);
    }
}
