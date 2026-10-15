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
     * Seed the application's database with realistic student academic books.
     */
    public function run(): void
    {
        // 1. Seed one user per role (password: password)
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@library.test',
            'password' => Hash::make('password'),
        ]);

        $librarian = User::factory()->librarian()->create([
            'name' => 'Librarian User',
            'email' => 'librarian@library.test',
            'password' => Hash::make('password'),
        ]);

        $memberUser = User::factory()->member()->create([
            'name' => 'Member User',
            'email' => 'member@library.test',
            'password' => Hash::make('password'),
        ]);

        // Link the member user to a members row
        $primaryMember = Member::create([
            'user_id' => $memberUser->id,
            'name' => $memberUser->name,
            'email' => $memberUser->email,
        ]);

        // 2. Real Academic Authors and 40 Student Books
        $catalogData = [
            [
                'author' => 'Robert C. Martin',
                'books' => [
                    ['title' => 'Clean Code: A Handbook of Agile Software Craftsmanship', 'isbn' => '9780132350884', 'year' => 2008, 'is_ref' => false],
                    ['title' => 'Clean Architecture: A Craftsman\'s Guide to Software Structure', 'isbn' => '9780134494166', 'year' => 2017, 'is_ref' => false],
                    ['title' => 'The Clean Coder: A Code of Conduct for Professional Programmers', 'isbn' => '9780137081073', 'year' => 2011, 'is_ref' => false],
                    ['title' => 'Agile Software Development: Principles, Patterns, and Practices', 'isbn' => '9780135974445', 'year' => 2002, 'is_ref' => false],
                ],
            ],
            [
                'author' => 'Thomas H. Cormen',
                'books' => [
                    ['title' => 'Introduction to Algorithms (4th Edition)', 'isbn' => '9780262046305', 'year' => 2022, 'is_ref' => false],
                    ['title' => 'Algorithms Unlocked: A Friendly Guide for Students', 'isbn' => '9780262518802', 'year' => 2013, 'is_ref' => false],
                    ['title' => 'Data Structures and Algorithm Analysis in C++', 'isbn' => '9780132847376', 'year' => 2014, 'is_ref' => false],
                    ['title' => 'Discrete Mathematics with Applications for Computer Scientists', 'isbn' => '9780495391326', 'year' => 2011, 'is_ref' => false],
                ],
            ],
            [
                'author' => 'James Stewart',
                'books' => [
                    ['title' => 'Calculus: Early Transcendentals (9th Edition)', 'isbn' => '9781337613927', 'year' => 2020, 'is_ref' => false],
                    ['title' => 'Precalculus: Mathematics for Calculus', 'isbn' => '9781305071759', 'year' => 2015, 'is_ref' => false],
                    ['title' => 'Multivariable Calculus: Concepts and Contexts', 'isbn' => '9780495560548', 'year' => 2009, 'is_ref' => false],
                    ['title' => 'Single Variable Calculus: Early Transcendentals', 'isbn' => '9781305270336', 'year' => 2016, 'is_ref' => false],
                ],
            ],
            [
                'author' => 'Abraham Silberschatz',
                'books' => [
                    ['title' => 'Operating System Concepts (10th Edition)', 'isbn' => '9781119456339', 'year' => 2018, 'is_ref' => false],
                    ['title' => 'Database System Concepts (7th Edition)', 'isbn' => '9780078022159', 'year' => 2019, 'is_ref' => false],
                    ['title' => 'Computer System Architecture & Fundamentals', 'isbn' => '9780131755635', 'year' => 2007, 'is_ref' => false],
                    ['title' => 'Distributed Systems: Principles and Paradigms', 'isbn' => '9781530281756', 'year' => 2017, 'is_ref' => false],
                ],
            ],
            [
                'author' => 'Hugh D. Young',
                'books' => [
                    ['title' => 'University Physics with Modern Physics (15th Edition)', 'isbn' => '9780135159553', 'year' => 2019, 'is_ref' => false],
                    ['title' => 'Sears and Zemansky\'s College Physics', 'isbn' => '9780134876986', 'year' => 2019, 'is_ref' => false],
                    ['title' => 'Fundamentals of Engineering Mechanics & Statics', 'isbn' => '9780133918922', 'year' => 2015, 'is_ref' => false],
                    ['title' => 'Thermodynamics for Science and Engineering Students', 'isbn' => '9780073398174', 'year' => 2014, 'is_ref' => false],
                ],
            ],
            [
                'author' => 'Neil A. Campbell',
                'books' => [
                    ['title' => 'Campbell Biology: Concepts and Connections (12th Edition)', 'isbn' => '9780135188743', 'year' => 2020, 'is_ref' => false],
                    ['title' => 'Essential Biology with Physiology for Undergraduates', 'isbn' => '9780134764825', 'year' => 2018, 'is_ref' => false],
                    ['title' => 'Genetics: Analysis and Principles in Modern Science', 'isbn' => '9781259616020', 'year' => 2017, 'is_ref' => false],
                    ['title' => 'Principles of Biochemistry and Cellular Metabolism', 'isbn' => '9780134292731', 'year' => 2016, 'is_ref' => false],
                ],
            ],
            [
                'author' => 'N. Gregory Mankiw',
                'books' => [
                    ['title' => 'Principles of Economics (9th Edition)', 'isbn' => '9780357038314', 'year' => 2020, 'is_ref' => false],
                    ['title' => 'Macroeconomics: Theory and Global Policy', 'isbn' => '9781319105990', 'year' => 2019, 'is_ref' => false],
                    ['title' => 'Microeconomics for College Students & Business Analysis', 'isbn' => '9781305971493', 'year' => 2017, 'is_ref' => false],
                    ['title' => 'Financial Markets and Economic Development', 'isbn' => '9780324583182', 'year' => 2011, 'is_ref' => false],
                ],
            ],
            [
                'author' => 'F. Scott Fitzgerald',
                'books' => [
                    ['title' => 'The Great Gatsby: The Original 1925 Classic', 'isbn' => '9780743273565', 'year' => 1925, 'is_ref' => false],
                    ['title' => 'Tender Is the Night: A Romance', 'isbn' => '9780684801544', 'year' => 1934, 'is_ref' => false],
                    ['title' => 'This Side of Paradise: Student Critical Edition', 'isbn' => '9780486289991', 'year' => 1920, 'is_ref' => false],
                    ['title' => 'The Beautiful and Damned: Academic Edition', 'isbn' => '9780141180878', 'year' => 1922, 'is_ref' => false],
                ],
            ],
            [
                'author' => 'George Orwell',
                'books' => [
                    ['title' => '1984: A Dystopian Masterpiece (Annotated Edition)', 'isbn' => '9780451524935', 'year' => 1949, 'is_ref' => false],
                    ['title' => 'Animal Farm: A Fairy Story with Historical Notes', 'isbn' => '9780451526342', 'year' => 1945, 'is_ref' => false],
                    ['title' => 'Homage to Catalonia: Student Humanities Edition', 'isbn' => '9780156421171', 'year' => 1938, 'is_ref' => false],
                    ['title' => 'Down and Out in Paris and London: Literary Study', 'isbn' => '9780156262248', 'year' => 1933, 'is_ref' => false],
                ],
            ],
            [
                'author' => 'Oxford Reference Editors',
                'books' => [
                    ['title' => 'Oxford Advanced Learner\'s English Dictionary (Reference)', 'isbn' => '9780194798792', 'year' => 2020, 'is_ref' => true],
                    ['title' => 'Oxford Concise Medical Dictionary (Library Reference)', 'isbn' => '9780199687817', 'year' => 2015, 'is_ref' => true],
                    ['title' => 'The Oxford Dictionary of Law (Reference)', 'isbn' => '9780198843498', 'year' => 2021, 'is_ref' => true],
                    ['title' => 'Oxford Encyclopedia of World History & Civilization (Reference)', 'isbn' => '9780199295661', 'year' => 2018, 'is_ref' => true],
                ],
            ],
        ];

        $allBooks = collect();

        foreach ($catalogData as $group) {
            $author = Author::create([
                'name' => $group['author'],
            ]);

            foreach ($group['books'] as $bookData) {
                $coverFile = "images/books/isbn_{$bookData['isbn']}.jpg";
                $coverPath = file_exists(public_path($coverFile))
                    ? "/{$coverFile}"
                    : null;

                $book = Book::create([
                    'author_id' => $author->id,
                    'title' => $bookData['title'],
                    'isbn' => $bookData['isbn'],
                    'published_year' => $bookData['year'],
                    'is_reference' => $bookData['is_ref'],
                    'cover_path' => $coverPath,
                ]);

                $allBooks->push($book);
            }
        }

        // 3. Seed 29 additional members (total 30 members)
        $otherMembers = Member::factory(29)->create();
        $allMembers = collect([$primaryMember])->concat($otherMembers);

        // 4. Seed 1–3 loans per member, with about 20% still unreturned (only non-reference books)
        $borrowableBooks = $allBooks->where('is_reference', false)->values();
        $activeBookIds = [];

        foreach ($allMembers as $m) {
            $loanCount = rand(1, 3);
            $chosenBooks = $borrowableBooks->random(min($loanCount, $borrowableBooks->count()));

            foreach ($chosenBooks as $b) {
                $borrowedAt = now()->subDays(rand(2, 30));
                $isUnreturned = rand(1, 100) <= 20 && ! isset($activeBookIds[$b->id]);
                if ($isUnreturned) {
                    $activeBookIds[$b->id] = true;
                }
                $returnedAt = $isUnreturned ? null : (clone $borrowedAt)->addDays(rand(1, 14))->min(now());

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
