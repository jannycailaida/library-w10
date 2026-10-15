# LibraryHub audit and implementation report

Date: October 15, 2026. Scope: the existing Laravel application and its documented guest, member, librarian, and administrator workflows.

## 1. Pages and modules audited

The Word project document, README, routes, controllers, models, database schema, policies, middleware, Blade templates/components, frontend assets, factories, seeder, and tests were reviewed before implementation. Two independent baseline assessments inspected the design and browser evidence. No reservation, payment, scanner, multi-campus, or separate report-generation module exists, so none was added.

| Page/module | Coverage and resulting behavior |
| --- | --- |
| `/` | Preserved redirect to the public catalogue. |
| `/books` | Guest and all authenticated roles; live search, subject/availability filters, sorting, pagination, empty results, book statuses, and Quick View. |
| `/books/{book}` | Available, borrowed, and reference states; guest sign-in; authenticated borrowing; staff edit/delete controls. |
| `/books/create`, `/books/{book}/edit` | Shared book form, field feedback, ISBN/year/cover validation, reference restrictions, save/cancel navigation. |
| `/dashboard` | Separate personal member overview and staff overview; admin management shortcuts are role restricted. |
| `/my/loans` | Personal history, visible due dates, active/overdue/returned filters, pagination, and empty states. |
| `/desk` | Staff-only search, status filtering, due dates, returns, pagination, and warnings for existing overlapping active loans. |
| `/admin/users` | Admin-only account search, role filter, pagination, labelled role controls, confirmation, and self-role protection. |
| `/profile` | Profile information, synchronized card details, password changes, persistent feedback, and confirmed account deletion. |
| `/login`, `/register` | Labelled compact forms, feedback, autocomplete, password visibility, and connected recovery/sign-in links. |
| `/forgot-password`, `/reset-password/{token}` | Consistent recovery screens, clear instructions, form feedback, password visibility, and return links. |
| `/confirm-password`, `/verify-email` | Consistent secure-area and verification screens. Existing signed verification/resend/redirect behavior is preserved. |
| Errors and shared UI | Branded 403, 404, 419, 429, 500, and 503 templates; shared navigation, footer, fields, buttons, alerts, covers, statuses, empty states, and native dialogs. |

## 2. UI/UX improvements

- Replaced decorative gradients, pulsing status badges, emoji chrome, oversized promotional sections, and heavily cropped covers with warm stone/white surfaces and a restrained forest green palette. LibraryHub branding and real book artwork remain.
- Put the catalogue search near the top of the first mobile viewport. Filters have persistent labels and no longer depend on a horizontally clipped subject strip.
- Standardized type hierarchy, spacing, button sizes, field feedback, focus indicators, and page titles across the whole application, including recovery and profile screens.
- Moved the full navigation breakpoint to laptop width. Tablet and phone navigation exposes a labelled menu with expanded state and role-appropriate destinations.
- Replaced wide circulation/user tables with readable responsive records. Dates, statuses, role controls, and return actions remain reachable through ordinary vertical scrolling.
- Added a skip link, semantic headings and landmarks, meaningful labels, live search/status feedback, accessible password controls, and native dialog behavior. Reduced-motion preferences are respected.

## 3. Dashboard improvements

Members now see their own active loans, overdue returns, borrowing history, due dates, and library card. Campus-wide totals are no longer presented as personal borrowing counts.

Staff see actual active loan records, overdue returns, available titles, cardholders, recent circulation, and relevant daily actions. Administrators additionally receive user-management access. Unnecessary category breakdowns, the external Chart.js script, and its repeated per-day analytics queries were removed. No production statistics are hardcoded.

## 4. Redundancy removed

- Shared `BookRequest` validation replaces duplicated create/update rules.
- Subject definitions and availability constraints are centralized on `Book`; loaded author data and active-loan counts are reused.
- Shared feedback, page headings, password fields, covers, book/loan statuses, empty states, and confirmation dialogs replace repeated implementations.
- Removed the unused Laravel welcome template, empty author/category controllers, and unreferenced Breeze dropdown/navigation/modal/status components after checking references.
- Removed duplicate welcome banners, marketing decorations, transient profile save messages, and unsupported export/download instructions.

## 5. Workflow improvements

- Catalogue search updates results while retaining input focus and filter state; ordinary GET submission remains available without JavaScript. Pagination retains filters.
- A reader can preview a title, borrow an available copy, and immediately see the due date in personal loans. Reference restrictions remain visible and enforced.
- Staff can find a physical return by title, ISBN, reader name, or email, then record it with one action. Repeat returns do not overwrite the original return timestamp.
- User management supports search and role filtering; role changes and destructive actions use a consistent confirmation dialog. Administrators cannot change their own role.
- Registration creates the user and linked member card in one transaction. Profile updates keep card name/email synchronized.
- Books with circulation history are retained. Account deletion is blocked while books remain on loan, preventing orphaned active borrowing obligations.

## 6. Functional issues fixed

| Finding | Implemented fix |
| --- | --- |
| Apostrophes broke Alpine Quick View attributes; `x-if` was used on a non-template element. | Escaped `Js::from` serialization, a proper template, and a native dialog with close/Escape/focus behavior. |
| The availability filter relied on an aggregate `HAVING` expression with database compatibility problems. | Shared relationship-existence constraints work with the tested SQLite and current MySQL catalogue. |
| Two requests could both check out a copy between availability checking and insertion. | Book/user locks and a transaction serialize borrowing; reference conversion and deletion also check protected book state under a lock. |
| Registration did not create the documented member profile. | User and card creation are transactional; privileged roles remain excluded from registration input. |
| Due dates and overdue distinctions were absent from operational pages. | All relevant pages consistently derive the 14-day deadline from `borrowed_at`. |
| Member dashboard totals mixed personal and library-wide data. | Member metrics and current reading are scoped to that member; staff metrics are explicitly library-wide. |
| Repeated returns could misrepresent current state. | Atomic conditional update preserves the first timestamp; success now says “Return recorded.” without claiming availability while another legacy loan is active. |
| Book/account deletion could undermine circulation history or active obligations. | Loan-history and active-borrower guards; account deletion checks run under a user lock. |
| Public forms accepted non-digit ISBNs or values exceeding current MySQL string columns. | Numeric 13-digit ISBN validation, schema-aligned lengths, and bounded local/http(s) cover paths with field feedback. |
| Fresh seeding ignored guarded role assignments and could create multiple active loans for one copy or future return dates. | Explicit factory role states, one active seeded loan per title, and bounded historical return times. The real database was not reseeded. |

## 7. Documentation compliance and conflicts

The four documented access tiers, 14-day borrowing period, non-circulating references, public catalogue, staff CRUD/returns, personal history, and administrator role controls remain in scope. Existing route names and endpoints are preserved. No dependencies or database schema were changed.

The subsequently supplied `WEBDEV3_Week10_Authentication_Authorization_Guide.docx`, specifically §8 (Library Accounts and Staff), was also checked. The follow-up adds explicit `Gate::authorize` calls to every book resource action and personal-history authorization through `LoanPolicy::view`, while retaining ownership-scoped queries and empty history for accounts without a card. `.env.example` now defaults to the required MySQL `library_w10` database; the actual `.env` was retained. README contains the four-role/eight-action expected-versus-actual matrix, implementation evidence, reflection answers, and a manual verification/submission checklist. Manual checks remain explicitly pending the student's own work; the matrix results are identified as automated. Optional §8.3 stretch goals were not added. Original Breeze installation order cannot be verified from the current files.

Important source conflicts were handled explicitly:

- The Word document and old README name Laravel 12 and PHPUnit 11; installed versions are Laravel 13.35.0 and PHPUnit 12.5.38. Implementation uses installed APIs. README was corrected; the original Word document remains intact.
- The older palette specification calls for lavender/indigo gradients. The new request expressly authorizes palette replacement and asks to avoid excessive gradients, so the newer brief governs the visual design.
- The Word data dictionary describes fields such as `due_at`, category, synopsis, student ID/phone, and author biography that are not present in the existing schema. Due dates are computed from the existing timestamp; existing author-based subject classification is centralized. The preview shows actual metadata and lending policy. No synopsis or other missing content was invented, and unrelated schema expansion was avoided.
- The original documentation’s “100% test coverage” language describes a passing suite, not measured code coverage. This report records actual executed checks and does not claim complete code coverage or accessibility certification.

## 8. Testing and validation

- Baseline: 36 PHPUnit tests, 87 assertions passed.
- Current implementation regression suite: 74 PHPUnit tests, 232 assertions passed, including ownership-policy checks, empty history without a linked card, and rejection of a client-selected borrower ID.
- Playwright: 27 tests passed after the guideline follow-up. The page matrix covers all four roles at 1440, 1024, 768, and 390 pixels: 124 routed page/role/viewport renders, plus workflow checks. Four additional role tests verify all 32 cells of the Week 10 access matrix using valid CSRF tokens, inspect redirects before following them, and check persisted state. Expected and actual HTTP statuses are attached as per-role JSON logs in the browser report.
- Browser workflows: live search and empty results; apostrophe-containing previews; keyboard dismissal/focus restoration; password visibility; restricted routes and 403/404 navigation; registration/verification view; borrow/staff return; book create/edit/confirmed delete; admin role changes/self protection; profile/password updates and confirmed account deletion.
- Runtime page-error checks passed across the page matrix and preview/search checks. No document horizontal overflow was detected in the tested viewport matrix.
- Browser writes use a freshly created, guarded temporary SQLite database, never the configured MySQL business database. A connection check runs before test migrations/seeding.
- Current MySQL public catalogue, availability/reference filters, and login were also checked through read-only requests and returned HTTP 200.
- Business-table counts were unchanged between the preservation checks: 40 books, 31 member cards, 4 users, and 64 loan records. The test server was stopped after browser verification.
- Production frontend build, Blade compilation, Laravel Pint, and whitespace checks passed. The mechanical UI detector reported no findings; browser and human-style source/screenshot review remain the stronger evidence.

Email sending/reset/verification behavior is covered with framework fakes; actual external email delivery was not tested. Browser automation used Chromium. Full Firefox/WebKit workflow testing, assistive-technology testing, and parallel MySQL load testing were not performed. The 419/429/500/503 templates were compiled and source reviewed; their complete runtime triggers were not simulated.

Final independent review disposition: **PASS**.

| Finding | Final verdict | Evidence |
| --- | --- | --- |
| F1: a return confirmation incorrectly claimed availability when another legacy loan remained active | Resolved | Neutral confirmation and a passing regression proving the other loan stays active and the title remains unavailable. |

The independent reviewer found no remaining material issues in the redesign. That review preceded the later Week 10 guideline follow-up; the follow-up was source reviewed and verified with the full PHPUnit and Playwright suites described above.

## 9. Remaining issues and operational handoff

1. **Legacy circulation records:** the existing MySQL database has three titles with multiple active loans. Those records were preserved and are flagged in the circulation desk for staff verification. Closing records automatically would invent a return event. New borrowing and fresh seed data prevent new overlaps.
2. **Metadata limits:** accurate editable subject categories and real synopses require a separately agreed content/schema update; current metadata is preserved rather than fabricated.
3. **External services:** production mail delivery, hosting, external fonts/cover URLs, backups, and concurrency/load behavior require environment-specific verification.
4. **Accessibility scope:** labels, contrast-conscious styling, focus, dialogs, touch controls, and responsive behavior were improved, but no formal WCAG or screen-reader certification is claimed.
5. **Coursework handoff:** complete the manual test log and screenshots in README, then incorporate the results and reflection answers into the formal submission document. Automated checks do not substitute for the guide's explicit hand-verification requirement.

The implementation and reproducible tests accompany this report. Deployment and reconciliation of legacy business records remain separate operational tasks.
