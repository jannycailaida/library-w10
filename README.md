# 📚 LibraryHub: University Digital Library Management System

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="220" alt="Laravel">
</p>

<p align="center">
  <strong>A responsive university library system built with Laravel 13, PHP 8.5, Tailwind CSS 3, and Alpine.js.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.5">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js">
  <img src="https://img.shields.io/badge/Tests-PHPUnit%20%2B%20Playwright-success?style=for-the-badge" alt="PHPUnit and Playwright tests">
</p>

---

## 📖 Overview

**LibraryHub** supports university book discovery, self-service borrowing, and staff circulation. Its academic interface uses warm neutral surfaces, a restrained forest green accent, readable book covers, and role-specific navigation. Inventory and loan information come from the database.

---

## ✨ Key Features

- **Academic interface:** Consistent headings, controls, feedback, keyboard focus, and responsive layouts across every page.
- **Public catalogue:** Live title/author/ISBN search, subject and availability filters, sorting, and pagination. Search also works with a regular form submission when JavaScript is disabled.
- **Accessible Quick View:** Safe title serialization and a native dialog with keyboard dismissal, focus restoration, and background scroll lock.
- **🎓 14-Day Self-Service Student Loans:** One-click borrowing, visible due dates, overdue filtering, and personal loan history. Registration creates a linked library card.
- **🔒 Non-Circulating Reference Safeguards:** Protection preventing reference volumes from leaving the premises.
- **📋 Staff Circulation Desk (`/desk`):** Search by book, ISBN, reader, or email; filter active, overdue, and returned loans; record verified physical returns. Existing overlapping loans are flagged for review.
- **👑 Administrator User Role Control (`/admin/users`):** Real-time role reassignment between `member`, `librarian`, and `admin`.
- **👁️ Show / Hide Password Toggle:** Available on login, registration, recovery, confirmation, profile, and account-deletion password fields.
- **🧪 Regression tests:** PHPUnit covers application behavior and authorization. Playwright checks all roles at desktop, laptop, tablet, and mobile widths and exercises complete user workflows.
- **Record preservation:** Books with loan history cannot be deleted; accounts with active loans must return their books before deletion. Profile changes synchronize the linked library card.
- **Cover uploads:** Staff can upload a JPG, PNG, or WebP cover up to 2 MB with an immediate preview. Editing without a new file keeps the current cover; replacing or deleting an eligible book removes its previous managed upload.
- **Add authors while cataloguing:** In Add/Edit book, choose **+ Add a new author** and enter their name. Saving the book adds the author to future selections; an existing matching name is reused. Only librarians and administrators can save these changes.

---

## 👥 Role-Based Access Control (RBAC)

| Role | Default Email | Password | Access Privileges |
| :--- | :--- | :--- | :--- |
| **System Admin** | `admin@library.test` | `password` | Full system access, User Role Management, Circulation Desk, Catalogue CRUD |
| **Librarian** | `librarian@library.test` | `password` | Circulation Desk check-in/returns, Catalogue Book CRUD, Borrowing |
| **Student / Member** | `member@library.test` | `password` | Catalogue browsing, Self-service 14-day borrowing, Personal loan history |
| **Guest / Visitor** | *Unauthenticated* | — | Public catalogue browsing, search, category filters, registration |

---

## 🚀 Quick Start / Local Installation

### Prerequisites
- PHP 8.3 or higher (PHP 8.5 used for this project)
- Composer 2.x
- Node.js 22.12+ and NPM (Node.js 24 used for this project)
- MySQL/MariaDB with the `pdo_mysql` PHP extension; start MySQL in XAMPP and create an empty `library_w10` database in phpMyAdmin for a new installation.

### Setup Instructions
```bash
# 1. Clone the repository
git clone https://github.com/jannycailaida/library-w10.git
cd library-w10

# 2. Install dependencies
composer install
npm install

# 3. Create .env for a new installation (PowerShell: Copy-Item .env.example .env)
cp .env.example .env
# Check the MySQL connection settings in .env before continuing
php artisan key:generate

# 4. Migrate database and seed library books & demo users
php artisan migrate --seed
php artisan storage:link

# 5. Build frontend assets
npm run build

# 6. Launch development server
php artisan serve
```

The environment example uses `DB_CONNECTION=mysql`, `DB_DATABASE=library_w10`, database sessions, and `MAIL_MAILER=log`. Set `DB_HOST`, `DB_PORT`, `DB_USERNAME`, and `DB_PASSWORD` to match your local MySQL installation. For an existing installation, keep its `.env` and database; seeding is for a new demo database. Password-reset messages are written to `storage/logs/laravel.log` rather than delivered to an inbox.

Access the application in your browser at: **`http://127.0.0.1:8000`**

---

## 🧪 Running Automated Tests

Run the test suite using PHPUnit:

```bash
php artisan test --compact
```

Run browser checks after building assets:

```bash
npx playwright install
npm run build
npm run test:browser
```

The browser test server creates and seeds a temporary SQLite database on port 8011. It verifies the connection before migrating and isolates uploaded files and session storage in the same temporary workspace. It does not borrow, return, edit, or delete records in your configured application database. Browser reports are written to the ignored `playwright-report/` and `test-results/` directories.

See [AUDIT_REPORT.md](AUDIT_REPORT.md) for the redesign coverage, verified results, documentation conflicts, and remaining limitations. Passing tests are not a claim of 100% code coverage.

Run just the Week 10 access matrix:

```bash
npx playwright test --grep "guideline access matrix"
```

Each of its four role tests attaches a JSON log with the action, request method/path, expected status, actual status, and redirect destination. Use `npx playwright show-report` after the default test run to view these attachments.

To format code according to project standards:
```bash
vendor/bin/pint --format agent
```

---

## Week 10 guideline compliance

This project follows **§8, “Library Accounts and Staff”**, in `WEBDEV3_Week10_Authentication_Authorization_Guide.docx`. The earlier Course examples illustrate the same techniques; the library exercise uses a server-selected member card as the borrower instead of adding a course-style owner column to books.

### Implementation evidence

| Requirement | Implementation |
| --- | --- |
| MySQL database and Breeze authentication | `.env.example` defaults to `library_w10`; Breeze supplies registration, login, logout, password recovery, and session authentication. The currently configured application also uses MySQL. |
| Roles default to `member` | The role migration provides the default; `User::$fillable` excludes `role`. Registration creates a linked member card and cannot accept a privileged role. |
| Library schema and relationships | Migrations and models provide authors, books, members, and the `book_member` loan pivot with borrowing/return timestamps and the required relationships. |
| Demo dataset and accounts | The fresh-database seeder creates 10 authors, 40 books, 30 members, 1–3 loans per member, and the three demo accounts above. It prevents overlapping active loans for one title. |
| BookPolicy and LoanPolicy with `before()` | Administrators pass through `before()`; a `null` result lets the ability method decide. Public book viewing is allowed; catalogue writes and staff returns are restricted. `Member` is explicitly mapped to `LoanPolicy` in `AppServiceProvider`. |
| Controller, Form Request, and Blade enforcement | Every book resource action calls `Gate::authorize`; `BookRequest::authorize()` also checks create/update. Personal loans call `LoanPolicy::view` for the linked card and retain an ownership-scoped query. Blade `@can` controls available actions. |
| Server-selected borrower | Borrowing derives the member from the authenticated user, ignoring a submitted `member_id`. Reference books cannot be borrowed by any role; availability is checked under a book lock. |
| Multiple-role middleware and admin controls | `/desk` requires `role:librarian,admin`; `/admin/users` requires `role:admin`. UserPolicy prevents an administrator from changing their own role. |
| Consistent auth-aware interface | Public pages work for guests; shared layouts/navigation show destinations and controls appropriate to the signed-in role. |

Breeze is installed and its flows are tested. The order in which it was originally installed cannot be established from the current files; retain genuine setup/commit evidence if your instructor asks for that historical checkpoint.

### Four-role, eight-action test matrix (§8.1)

Each cell is **expected / automated actual HTTP status**. All 32 cells matched in the Chromium run on **2026-10-15**, using the isolated temporary test database. Redirects were inspected before following them, so a login page returning 200 cannot conceal a 302.

| Role | A: Catalogue | B: Create | C: Update | D: Delete | E: Own loans | F: Desk | G: Users | H: Borrow |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| Guest | 200 / 200 | 302 / 302 | 302 / 302 | 302 / 302 | 302 / 302 | 302 / 302 | 302 / 302 | 302 / 302 |
| Member | 200 / 200 | 403 / 403 | 403 / 403 | 403 / 403 | 200 / 200 | 403 / 403 | 403 / 403 | 302 / 302 |
| Librarian | 200 / 200 | 302 / 302 | 302 / 302 | 302 / 302 | 200 / 200 | 200 / 200 | 403 / 403 | 302 / 302 |
| Admin | 200 / 200 | 302 / 302 | 302 / 302 | 302 / 302 | 200 / 200 | 200 / 200 | 200 / 200 | 302 / 302 |

| Action | Exact request and precondition | Meaning of a successful response |
| --- | --- | --- |
| A | `GET /books` | 200: public catalogue. `GET /books/{book}` is also public. |
| B | `POST /books`, valid fields and an unused 13-digit ISBN | 302 to the newly created book; the saved title is verified. The create form itself returns 200 to staff and 403 to members. |
| C | `PUT /books/{book}`, valid fields for an existing book | 302 to the updated book; the changed title is verified. The edit form returns 200 to staff and 403 to members. |
| D | `DELETE /books/{book}`, a disposable book with no loan history | 302 to `/books`; the deleted detail URL subsequently returns 404. |
| E | `GET /my/loans` | 200: only the logged-in account's history. An account without a card receives an empty history. |
| F | `GET /desk` | 200: the staff circulation desk. |
| G | `GET /admin/users` | 200: the administrator user-management page. |
| H | `POST /books/{book}/borrow`, an available, non-reference book | 302 to `/my/loans`; the borrowed title appears in that account's history. |

For guests, every protected 302 above points to `/login`. Authenticated 403 responses mean the role is forbidden. A 302 alone does not prove a write succeeded: the matrix also verifies redirect destinations and saved/deleted/borrowed state. Failed validation and attempts to delete books with history redirect with errors. Those are separate cases, outside the valid-input matrix.

Additional regression coverage verifies reference-book rejection, member return rejection, staff returns, repeated returns, private loan history, ignored borrower-ID injection, registration role injection, and administrator self-demotion protection. Three-role policy checks verify that only staff may view another member's loan history.

### Manual verification and submission evidence (§8.2)

The guide explicitly asks for verification **by hand**. The actual results above are automated evidence; the following manual log is still pending the student's own checks. Use a demo/test database and disposable book records for write actions. Preserve existing loan records.

| Account | Actions to verify | Manual actual statuses / notes | Screenshot or evidence file |
| --- | --- | --- | --- |
| Guest, signed out | A–H | Pending manual verification | Pending |
| `member@library.test` | A–H | Pending manual verification | Pending |
| `librarian@library.test` | A–H | Pending manual verification | Pending |
| `admin@library.test` | A–H | Pending manual verification | Pending |

1. Open browser Developer Tools → Network and enable **Preserve log**. Record the original request's status and redirect `Location`, rather than just the final page's status. For denied write controls that are hidden, submit the same method/path with the current session and a valid CSRF token using a request client; do not disable CSRF protection. The labelled fields above define valid payloads.
2. Complete each row with eight actual statuses and capture the relevant pages or network entries. Also demonstrate that a member cannot record a return, a librarian can record it, reference borrowing is blocked, and an admin cannot demote themselves.
3. Include an overview/objectives, role/module descriptions, the schema/relationships, screenshots of the catalogue/authentication/dashboards/loans/desk/admin pages, this test log, and the reflection answers in the submission document. Follow any separate formatting instructions from your instructor.
4. Review the final code and use accurate commit messages. Keep the existing setup history; do not invent earlier Part A–C commits.

### Reflection answers (§8.4)

**Why separate gates/policies from middleware when both can return 403?** Middleware filters requests before the controller, for example by requiring a signed-in user or a staff role. A gate or policy describes whether that user may perform a particular action, often on a particular record. Here, role middleware protects the desk, while LoanPolicy determines whether a member may view a specific member's history. The same policy rule can be checked by controllers, Form Requests, and Blade, which keeps the permission decision consistent. Hiding a button alone does not secure an endpoint.

**What would a token-based mobile client need instead of a session cookie?** It would need an API authentication flow that issues an access token after validating credentials, stores that token securely, and sends it with requests as `Authorization: Bearer <token>`. The server must validate the token and still enforce the same policies. Token expiry and revocation must be handled. A Laravel API could use Sanctum; this browser application currently uses session authentication and does not add a token API.

For the demo, `before()` returning `true` allows, `false` denies, and `null` defers to the ability method. With the current `SESSION_DRIVER=database`, session data lives in the `sessions` table; the browser holds the session identifier cookie. Passwords are stored as hashes, and login regenerates the session ID.

### Optional stretch goals (§8.3)

Backed-enum roles, mandatory email verification before borrowing, and custom logging for every 403 are optional extensions. They are not required for the core §8 activity. Role strings, logged password-reset messages, and the existing browser session workflow remain the implemented baseline.

---

## 📂 Project Structure

```
app/
├── Http/Controllers/
│   ├── AdminUserController.php   # User role management
│   ├── BookController.php        # Catalogue & borrowing logic
│   ├── DashboardController.php   # Student dashboard metrics
│   └── LoanController.php        # Circulation desk & loan history
├── Models/
│   ├── Author.php                # Book authors
│   ├── Book.php                  # Library titles & status logic
│   ├── Member.php                # Student membership profile
│   └── User.php                  # Authentication & RBAC
database/
├── migrations/                   # Database schema definitions
└── seeders/                      # Sample library titles & default accounts
resources/
├── views/
│   ├── auth/                     # Clean, minimalist authentication forms
│   ├── books/                    # Public catalogue & quick view modal
│   ├── dashboard.blade.php       # Member statistics banner
│   └── layouts/                  # Academic guest and app layouts
```

---

## 📄 License & Attribution

Developed by **Jannyca Ilaida** for university coursework submission.  
Licensed under the [MIT License](LICENSE).
