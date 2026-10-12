<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database as specified in Guide §8.1.
     */
    public function run(): void
    {
        // 1. Seed one user per role (password: password)
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@library.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $librarian = User::create([
            'name' => 'Librarian User',
            'email' => 'librarian@library.test',
            'password' => Hash::make('password'),
            'role' => 'librarian',
        ]);

        $memberUser = User::create([
            'name' => 'Member User',
            'email' => 'member@library.test',
            'password' => Hash::make('password'),
            'role' => 'member',
        ]);

        // Link the member user to a members row
        $primaryMember = Member::create([
            'user_id' => $memberUser->id,
            'name' => $memberUser->name,
            'email' => $memberUser->email,
        ]);

        // 2. Seed 10 authors × 4 books (40 books total)
        $authors = Author::factory(10)->create();
        $allBooks = collect();

        foreach ($authors as $author) {
            $books = Book::factory(4)->create([
                'author_id' => $author->id,
            ]);
            $allBooks = $allBooks->concat($books);
        }

        // 3. Seed 29 additional members (total 30 members)
        $otherMembers = Member::factory(29)->create();
        $allMembers = collect([$primaryMember])->concat($otherMembers);

        // 4. Seed 1–3 loans per member, with about 20% still unreturned (only non-reference books)
        $borrowableBooks = $allBooks->where('is_reference', false)->values();

        foreach ($allMembers as $m) {
            $loanCount = rand(1, 3);
            $chosenBooks = $borrowableBooks->random(min($loanCount, $borrowableBooks->count()));

            foreach ($chosenBooks as $b) {
                $borrowedAt = now()->subDays(rand(2, 30));
                $isUnreturned = (rand(1, 100) <= 20); // ~20% unreturned
                $returnedAt = $isUnreturned ? null : (clone $borrowedAt)->addDays(rand(1, 14));

                $m->books()->attach($b->id, [
                    'borrowed_at' => $borrowedAt,
                    'returned_at' => $returnedAt,
                    'created_at' => $borrowedAt,
                    'updated_at' => $returnedAt ?? $borrowedAt,
                ]);
            }
        }
    }
}
