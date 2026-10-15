# 📚 LibraryHub: University Digital Library Management System

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="220" alt="Laravel">
</p>

<p align="center">
  <strong>A modern, responsive University Digital Library Management System built with Laravel 12, PHP 8.5, Tailwind CSS, and Alpine.js.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.5">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js">
  <img src="https://img.shields.io/badge/Tests-36%20Passed-success?style=for-the-badge&logo=checkmarx&logoColor=white" alt="Tests Passed">
</p>

---

## 📖 Overview

**LibraryHub** is an academic digital library platform engineered to streamline book cataloguing, student borrowing workflows, and staff circulation desk operations. Featuring a clean **Light Academic Editorial Aesthetic**, real-time inventory metrics, and strict role-based access control, LibraryHub provides a seamless experience for students and library personnel alike.

---

## ✨ Key Features

- **🏛️ Modern Light Academic Aesthetic:** Soft lavender and indigo gradients, high-contrast typography, and intuitive single-screen authentication forms.
- **📚 Interactive Public Catalogue:** Live multi-category subject filters, real-time title/author/ISBN search, and single-source-of-truth status badges (`Available`, `Borrowed`, `Reference`).
- **👁️ Instant Quick View Modal:** Powered by Alpine.js for zero-latency book detail previews.
- **🎓 14-Day Self-Service Student Loans:** One-click borrowing with automated due date calculation and status updates.
- **🔒 Non-Circulating Reference Safeguards:** Protection preventing reference volumes from leaving the premises.
- **📋 Staff Circulation Desk (`/desk`):** Centralized ledger for librarians to monitor campus-wide loans and process returns with one click.
- **👑 Administrator User Role Control (`/admin/users`):** Real-time role reassignment between `member`, `librarian`, and `admin`.
- **👁️ Show / Hide Password Toggle:** Interactive visibility toggles on all password fields.
- **🧪 100% Test Coverage:** 36 automated unit & feature tests verifying authentication, borrowing policies, and circulation desk operations.

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
- PHP 8.2 or higher (PHP 8.5 recommended)
- Composer 2.x
- Node.js 18+ and NPM

### Setup Instructions
```bash
# 1. Clone the repository
git clone https://github.com/jannycailaida/library-w10.git
cd library-w10

# 2. Install dependencies
composer install
npm install

# 3. Setup environment and generate app key
cp .env.example .env
php artisan key:generate

# 4. Migrate database and seed library books & demo users
php artisan migrate --seed

# 5. Build frontend assets
npm run build

# 6. Launch development server
php artisan serve
```

Access the application in your browser at: **`http://127.0.0.1:8000`**

---

## 🧪 Running Automated Tests

Run the test suite using PHPUnit:

```bash
php artisan test --compact
```

Expected result:
```
Tests:    36 passed (87 assertions)
Duration: ~2.7s
Result:   100% Passing
```

To format code according to project standards:
```bash
vendor/bin/pint --format agent
```

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
