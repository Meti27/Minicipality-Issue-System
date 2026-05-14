# Municipality Issue System

A full-stack web application where citizens report infrastructure issues, municipality staff review and process them, and admins manage the system. Built as a university project using Laravel 11.

---

## Table of Contents

- [Tech Stack](#tech-stack)
- [Quick Start](#quick-start)
- [Default Credentials](#default-credentials)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Roles & Access Control](#roles--access-control)
- [Features by Role](#features-by-role)
- [Complaint Lifecycle](#complaint-lifecycle)
- [Routes Reference](#routes-reference)
- [Controllers](#controllers)
- [Models & Relationships](#models--relationships)
- [Form Validation](#form-validation)
- [Blade Views](#blade-views)
- [Reusable Components](#reusable-components)
- [Seeders](#seeders)
- [Tests](#tests)
- [Known Gaps & What Needs Attention](#known-gaps--what-needs-attention)

---

## Tech Stack

| Layer       | Technology                          |
|-------------|-------------------------------------|
| Framework   | Laravel 11                          |
| Language    | PHP 8.2                             |
| Auth        | Laravel Breeze (Blade stack)        |
| Frontend    | Blade templates, Tailwind CSS, Alpine.js |
| Database    | MySQL (SQLite also supported)       |
| Build tool  | Vite                                |
| Testing     | PHPUnit 11                          |

---

## Quick Start

```bash
# 1. Install PHP dependencies
composer install

# 2. Install Node dependencies
npm install

# 3. Copy environment file and configure your DB credentials
cp .env.example .env
php artisan key:generate

# 4. Create the database, then run migrations + seed
php artisan migrate --seed

# 5. Create the storage symlink (needed for complaint image uploads)
php artisan storage:link

# 6. Run the dev server (two terminals)
php artisan serve
npm run dev
```

Or use the all-in-one script (runs server, queue, log tail, and Vite together):
```bash
composer run dev
```

---

## Default Credentials

After running `php artisan migrate --seed`, one admin account and five categories are created:

| Role  | Email                       | Password   |
|-------|-----------------------------|------------|
| Admin | admin@municipality.gov      | password   |

Citizens and staff accounts must be created by an admin through the user management panel.

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Citizen/
│   │   │   ├── ComplaintController.php     # Submit, list, view complaints
│   │   │   └── NotificationController.php  # View, mark-read notifications
│   │   ├── Staff/
│   │   │   └── ComplaintController.php     # Review, update status, see all complaints
│   │   ├── Admin/
│   │   │   ├── DashboardController.php     # System-wide stats
│   │   │   ├── UserController.php          # Create and manage users
│   │   │   └── CategoryController.php      # Create and manage categories
│   │   ├── Auth/                           # Breeze auth controllers (login, register, etc.)
│   │   └── ProfileController.php           # Profile edit/delete (all roles)
│   ├── Middleware/
│   │   └── RoleMiddleware.php              # Role gate + is_active check
│   └── Requests/
│       ├── StoreComplaintRequest.php
│       ├── UpdateComplaintStatusRequest.php
│       ├── StoreUserRequest.php
│       ├── StoreCategoryRequest.php
│       └── UpdateCategoryRequest.php
├── Models/
│   ├── User.php
│   ├── Complaint.php
│   ├── Category.php
│   ├── ComplaintStatusHistory.php
│   └── Notification.php
└── View/Components/
    ├── AppLayout.php
    └── GuestLayout.php

database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── ...cache and jobs tables...
│   ├── 2026_05_14_075731_create_categories_table.php
│   ├── 2026_05_14_075732_create_complaints_table.php
│   ├── 2026_05_14_075733_create_complaint_status_histories_table.php
│   └── 2026_05_14_075734_create_notifications_table.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── AdminSeeder.php
    └── CategorySeeder.php

resources/views/
├── layouts/
│   ├── app.blade.php           # Main authenticated layout
│   ├── guest.blade.php         # Login/register layout
│   └── navigation.blade.php    # Navbar with notification bell + role badge
├── components/                 # Reusable Blade components
├── citizen/                    # Citizen-facing pages
├── staff/                      # Staff-facing pages
├── admin/                      # Admin-facing pages
├── auth/                       # Breeze auth pages
├── profile/                    # Profile edit pages
└── errors/
    ├── 403.blade.php
    └── 404.blade.php

routes/
└── web.php                     # All routes grouped by role middleware
```

---

## Database Schema

### `users`
| Column             | Type      | Notes                                         |
|--------------------|-----------|-----------------------------------------------|
| id                 | bigint PK |                                               |
| name               | string    |                                               |
| email              | string    | unique                                        |
| email_verified_at  | timestamp | nullable                                      |
| password           | string    | bcrypt hashed                                 |
| role               | enum      | `citizen` \| `staff` \| `admin`, default `citizen` |
| is_active          | boolean   | default `true`; inactive users are logged out |
| remember_token     | string    | nullable                                      |
| timestamps         |           |                                               |

### `categories`
| Column      | Type      | Notes                           |
|-------------|-----------|---------------------------------|
| id          | bigint PK |                                 |
| name        | string    |                                 |
| description | text      | nullable                        |
| is_active   | boolean   | default `true`                  |
| timestamps  |           |                                 |

### `complaints`
| Column           | Type      | Notes                                                                 |
|------------------|-----------|-----------------------------------------------------------------------|
| id               | bigint PK |                                                                       |
| user_id          | FK        | → `users.id` CASCADE DELETE                                           |
| category_id      | FK        | → `categories.id` CASCADE DELETE                                      |
| title            | string    |                                                                       |
| description      | text      |                                                                       |
| location         | string    |                                                                       |
| image_path       | string    | nullable; relative path under `storage/app/public/complaints/`        |
| status           | enum      | `submitted` \| `pending_review` \| `validated` \| `in_progress` \| `resolved` \| `closed` \| `rejected` |
| priority         | enum      | `low` \| `medium` \| `high`, default `medium` — **in DB but not yet exposed in forms** |
| rejection_reason | text      | nullable; required when status = `rejected`                           |
| timestamps       |           |                                                                       |

### `complaint_status_histories`
| Column       | Type      | Notes                                        |
|--------------|-----------|----------------------------------------------|
| id           | bigint PK |                                              |
| complaint_id | FK        | → `complaints.id` CASCADE DELETE             |
| changed_by   | FK        | → `users.id` CASCADE DELETE                  |
| old_status   | string    | nullable (null on initial submission)        |
| new_status   | string    |                                              |
| comment      | text      | nullable; optional staff note per transition |
| timestamps   |           |                                              |

### `notifications`
| Column       | Type      | Notes                                         |
|--------------|-----------|-----------------------------------------------|
| id           | bigint PK |                                               |
| user_id      | FK        | → `users.id` CASCADE DELETE                   |
| complaint_id | FK        | → `complaints.id` SET NULL on delete; nullable |
| message      | text      |                                               |
| is_read      | boolean   | default `false`                               |
| timestamps   |           |                                               |

---

## Roles & Access Control

Three roles stored in `users.role`: `citizen`, `staff`, `admin`.

**`RoleMiddleware`** (`app/Http/Middleware/RoleMiddleware.php`) handles two guards in one:
1. **Role check** — aborts 403 if the authenticated user's role is not in the middleware's allowed list.
2. **Active check** — if `is_active` is `false`, the user is forcibly logged out and redirected to the login page with an error message. This means deactivating a user takes effect immediately on their next request.

The middleware is registered as the `role` alias in `bootstrap/app.php` and applied to all three route groups.

> Admin cannot deactivate their own account — the `UserController::toggleActive` method prevents it.

---

## Features by Role

### Citizen

| Feature                   | Route                            | Notes                                                    |
|---------------------------|----------------------------------|----------------------------------------------------------|
| Dashboard                 | `GET /citizen/dashboard`         | Stats: total, submitted, in-progress, resolved, rejected |
| List my complaints        | `GET /citizen/complaints`        | Paginated (10/page), with category                       |
| Submit complaint          | `GET/POST /citizen/complaints`   | Image upload optional (jpg/jpeg/png/gif, max 2 MB)       |
| View complaint detail     | `GET /citizen/complaints/{id}`   | Includes full status history timeline                    |
| Notification inbox        | `GET /citizen/notifications`     | Marks all as read on page visit; paginated (20/page)     |
| Mark single notif read    | `GET /citizen/notifications/{id}/read` | Redirects to the linked complaint if applicable   |
| Mark all notifications read | `POST /citizen/notifications/mark-all-read` | One-click bulk action                       |

Citizens can **only see their own complaints** — `abort_unless($complaint->user_id === auth()->id(), 403)` is enforced in the controller.

The navbar notification bell (top-right) shows a red badge with unread count (capped at `9+`), a live dropdown of the 6 most recent notifications, and a "Mark all read" link.

---

### Staff

| Feature                  | Route                                      | Notes                                                             |
|--------------------------|--------------------------------------------|-------------------------------------------------------------------|
| Dashboard                | `GET /staff/dashboard`                     | System-wide stats + 8 most recent pending complaints              |
| List all complaints      | `GET /staff/complaints`                    | Paginated (15/page); filter by status; search by title, location, or citizen name |
| View complaint detail    | `GET /staff/complaints/{id}`               | Status history, allowed next transitions, and 5 potential duplicates in same category |
| Update complaint status  | `PATCH /staff/complaints/{id}/status`      | Only allowed transitions enforced; rejection requires a reason    |

**Status transition rules** are enforced server-side via a `TRANSITIONS` constant in `Staff\ComplaintController`:

```
submitted      → pending_review
pending_review → validated | rejected
validated      → in_progress
in_progress    → resolved
resolved       → closed
closed         → (terminal)
rejected       → (terminal)
```

Any attempt to jump to a non-allowed next status returns a validation error. An optional `comment` can be left on each transition. On every status change, a `ComplaintStatusHistory` record and a `Notification` for the citizen are automatically created.

---

### Admin

| Feature                  | Route                                         | Notes                                                   |
|--------------------------|-----------------------------------------------|---------------------------------------------------------|
| Dashboard                | `GET /admin/dashboard`                        | User stats, complaint stats, 6 recent complaints, 5 recent users |
| List users               | `GET /admin/users`                            | Paginated (15/page); filter by role; search by name/email |
| Create user              | `GET/POST /admin/users`                       | Can create `citizen` or `staff` accounts only (not admin) |
| Toggle user active       | `PATCH /admin/users/{id}/toggle-active`       | Immediately deactivates/reactivates; self-deactivation blocked |
| List categories          | `GET /admin/categories`                       | Paginated (15/page) with complaint count per category   |
| Create category          | `GET/POST /admin/categories`                  | Name must be unique                                     |
| Edit category            | `GET /admin/categories/{id}/edit`             | Name uniqueness check ignores current record            |
| Update category          | `PUT /admin/categories/{id}`                  |                                                         |
| Toggle category active   | `PATCH /admin/categories/{id}/toggle-active`  | Inactive categories don't appear in the citizen submit form |

---

## Complaint Lifecycle

```
[Citizen submits]
       ↓
  submitted
       ↓ (staff)
  pending_review
       ↓ (staff)             ↘ rejected (requires rejection_reason)
  validated
       ↓ (staff)
  in_progress
       ↓ (staff)
  resolved
       ↓ (staff)
  closed
```

Every transition:
1. Updates `complaints.status` (and `rejection_reason` if rejected).
2. Inserts a row in `complaint_status_histories` (old → new, who, optional comment).
3. Inserts a `Notification` for the complaint's citizen owner.

---

## Routes Reference

```
GET  /                                       Welcome page
GET  /dashboard                              Role-based redirect (citizen/staff/admin)

# Citizen
GET  /citizen/dashboard                      citizen.dashboard
GET  /citizen/complaints                     citizen.complaints.index
GET  /citizen/complaints/create              citizen.complaints.create
POST /citizen/complaints                     citizen.complaints.store
GET  /citizen/complaints/{complaint}         citizen.complaints.show
GET  /citizen/notifications                  citizen.notifications.index
GET  /citizen/notifications/{notif}/read     citizen.notifications.read
POST /citizen/notifications/mark-all-read    citizen.notifications.markAllRead

# Staff
GET   /staff/dashboard                       staff.dashboard
GET   /staff/complaints                      staff.complaints.index
GET   /staff/complaints/{complaint}          staff.complaints.show
PATCH /staff/complaints/{complaint}/status   staff.complaints.updateStatus

# Admin
GET   /admin/dashboard                       admin.dashboard
GET   /admin/users                           admin.users.index
GET   /admin/users/create                    admin.users.create
POST  /admin/users                           admin.users.store
PATCH /admin/users/{user}/toggle-active      admin.users.toggleActive
GET   /admin/categories                      admin.categories.index
GET   /admin/categories/create               admin.categories.create
POST  /admin/categories                      admin.categories.store
GET   /admin/categories/{category}/edit      admin.categories.edit
PUT   /admin/categories/{category}           admin.categories.update
PATCH /admin/categories/{category}/toggle-active  admin.categories.toggleActive

# Profile (all authenticated users)
GET    /profile                              profile.edit
PATCH  /profile                             profile.update
DELETE /profile                             profile.destroy
```

---

## Controllers

### `Citizen\ComplaintController`
- `dashboard()` — personal stats + 5 most recent complaints
- `index()` — paginated complaint list
- `create()` — form with active categories
- `store(StoreComplaintRequest)` — saves complaint, optional image upload, creates initial status history entry
- `show(Complaint)` — 403 if not the owner; loads category + status history with who-changed

### `Citizen\NotificationController`
- `index()` — paginated list; bulk-marks all unread as read on visit
- `markRead(Notification)` — marks one read; redirects to linked complaint or notification list
- `markAllRead()` — bulk update

### `Staff\ComplaintController`
- `dashboard()` — system stats + 8 most urgent pending complaints
- `index()` — all complaints with optional `?status=` and `?search=` query strings
- `show(Complaint)` — detail view with allowed transitions array and 5 possible duplicates in same category
- `updateStatus(UpdateComplaintStatusRequest, Complaint)` — enforces transition rules, writes history, fires notification

### `Admin\DashboardController`
- `index()` — aggregated user + complaint stats, recent items

### `Admin\UserController`
- `index()` — paginated users with optional `?role=` and `?search=` filters
- `create()` / `store(StoreUserRequest)` — creates citizen or staff account
- `toggleActive(User)` — flips `is_active`; blocks self-deactivation

### `Admin\CategoryController`
- `index()` — paginated categories with `withCount('complaints')`
- `create()` / `store(StoreCategoryRequest)` — unique name enforced
- `edit(Category)` / `update(UpdateCategoryRequest, Category)` — unique name ignores self
- `toggleActive(Category)` — flips `is_active`

---

## Models & Relationships

```
User
  hasMany  Complaint            (as reporter)
  hasMany  Notification
  hasMany  ComplaintStatusHistory  (foreign key: changed_by)

Category
  hasMany  Complaint

Complaint
  belongsTo  User
  belongsTo  Category
  hasMany    ComplaintStatusHistory
  hasMany    Notification

ComplaintStatusHistory
  belongsTo  Complaint
  belongsTo  User  (as changedBy, FK: changed_by)

Notification
  belongsTo  User
  belongsTo  Complaint  (nullable)
```

**Casts in use:**
- `User` — `email_verified_at` → datetime, `password` → hashed, `is_active` → boolean
- `Category` — `is_active` → boolean
- `Notification` — `is_read` → boolean

---

## Form Validation

All validation is in Form Request classes — no inline `$request->validate()` calls in controllers.

### `StoreComplaintRequest`
| Field       | Rules                                              |
|-------------|----------------------------------------------------|
| title       | required, string, max:255                          |
| description | required, string                                   |
| category_id | required, exists:categories,id                     |
| location    | required, string, max:255                          |
| image       | nullable, image, mimes:jpg,jpeg,png,gif, max:2048  |

### `UpdateComplaintStatusRequest`
| Field            | Rules                                                                         |
|------------------|-------------------------------------------------------------------------------|
| new_status       | required, in: pending_review, validated, in_progress, resolved, closed, rejected |
| comment          | nullable, string, max:1000                                                    |
| rejection_reason | required_if:new_status,rejected; nullable, string, max:1000                  |

### `StoreUserRequest`
| Field    | Rules                                       |
|----------|---------------------------------------------|
| name     | required, string, max:255                   |
| email    | required, email, unique:users,email         |
| password | required, confirmed, Password::defaults()   |
| role     | required, in:citizen,staff                  |

### `StoreCategoryRequest`
| Field       | Rules                                     |
|-------------|-------------------------------------------|
| name        | required, string, max:255, unique:categories,name |
| description | nullable, string, max:1000                |

### `UpdateCategoryRequest`
| Field       | Rules                                                        |
|-------------|--------------------------------------------------------------|
| name        | required, string, max:255, unique:categories,name,{current_id} |
| description | nullable, string, max:1000                                   |

---

## Blade Views

```
layouts/
  app.blade.php           Main layout (navbar + content slot + flash messages)
  guest.blade.php         Auth pages layout
  navigation.blade.php    Role-aware navbar — see "Navbar" below

citizen/
  dashboard.blade.php             Stats cards + recent 5 complaints table
  complaints/
    create.blade.php              Submit form (title, description, category, location, image)
    index.blade.php               Paginated complaint list with status badges
    show.blade.php                Complaint detail + status history timeline
  notifications/
    index.blade.php               Notification list with read/unread styling

staff/
  dashboard.blade.php             Stats + pending complaints queue
  complaints/
    index.blade.php               Full list with status filter tabs + search bar
    show.blade.php                Detail with status update form, history, duplicates panel

admin/
  dashboard.blade.php             User stats + complaint stats + recent tables
  users/
    index.blade.php               User list with role filter + search + activate/deactivate toggle
    create.blade.php              Create citizen or staff account form
  categories/
    index.blade.php               Category list with complaint count + toggle
    create.blade.php              New category form
    edit.blade.php                Edit existing category form

auth/                             Breeze default pages (login, register, password reset, etc.)
profile/edit.blade.php            Update name/email + change password + delete account
errors/
  403.blade.php                   Custom "Access Denied" page with dashboard link
  404.blade.php                   Custom "Not Found" page
welcome.blade.php                 Default Laravel welcome page (not customised yet)
```

**Navbar highlights (`navigation.blade.php`):**
- Role-based navigation links (different links for citizen / staff / admin)
- Notification bell (citizens only): red badge with unread count, live dropdown of 6 most recent, "Mark all read" button, link to full inbox
- User dropdown: shows email, avatar initial, color-coded role badge (blue = citizen, amber = staff, purple = admin), Profile link, Sign Out
- Fully responsive with hamburger menu on mobile

---

## Reusable Components

| Component              | File                                   | Purpose                                                     |
|------------------------|----------------------------------------|-------------------------------------------------------------|
| `<x-status-badge>`     | `components/status-badge.blade.php`    | Color-coded pill badge for every complaint status           |
| `<x-nav-link>`         | `components/nav-link.blade.php`        | Desktop nav link with active state underline                |
| `<x-responsive-nav-link>` | `components/responsive-nav-link.blade.php` | Mobile nav link                                        |
| `<x-dropdown>`         | `components/dropdown.blade.php`        | Alpine.js dropdown wrapper (used for user menu)             |
| `<x-dropdown-link>`    | `components/dropdown-link.blade.php`   | Link inside a dropdown                                      |
| `<x-input-label>`      | `components/input-label.blade.php`     | Form label                                                  |
| `<x-text-input>`       | `components/text-input.blade.php`      | Styled text input                                           |
| `<x-input-error>`      | `components/input-error.blade.php`     | Inline validation error message                             |
| `<x-primary-button>`   | `components/primary-button.blade.php`  | Blue CTA button                                             |
| `<x-secondary-button>` | `components/secondary-button.blade.php`| Secondary/outline button                                    |
| `<x-danger-button>`    | `components/danger-button.blade.php`   | Red destructive action button                               |
| `<x-modal>`            | `components/modal.blade.php`           | Alpine.js modal dialog                                      |
| `<x-auth-session-status>` | `components/auth-session-status.blade.php` | Flash success/error banners on auth pages            |
| `<x-app-layout>`       | `app/View/Components/AppLayout.php`    | Wraps `layouts/app.blade.php`                               |
| `<x-guest-layout>`     | `app/View/Components/GuestLayout.php`  | Wraps `layouts/guest.blade.php`                             |

**`<x-status-badge status="...">`** maps each status to a distinct Tailwind color ring:
- `submitted` → blue
- `pending_review` → yellow
- `validated` → indigo
- `in_progress` → orange
- `resolved` → green
- `closed` → gray
- `rejected` → red

---

## Seeders

Run with `php artisan migrate --seed` or `php artisan db:seed`.

### `AdminSeeder`
Creates one admin user using `firstOrCreate` (safe to re-run):
- Name: System Administrator
- Email: `admin@municipality.gov`
- Password: `password`
- Role: `admin`, `is_active: true`

### `CategorySeeder`
Creates 5 default categories (using `firstOrCreate`):
1. Potholes
2. Streetlights
3. Garbage
4. Water Leaks
5. Damaged Roads

---

## Tests

The test suite currently covers only the default Breeze authentication flows:

```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── AuthenticationTest.php
│   │   ├── EmailVerificationTest.php
│   │   ├── PasswordConfirmationTest.php
│   │   ├── PasswordResetTest.php
│   │   ├── PasswordUpdateTest.php
│   │   └── RegistrationTest.php
│   ├── ExampleTest.php
│   └── ProfileTest.php
└── Unit/
    └── ExampleTest.php
```

Run all tests:
```bash
php artisan test
```

Run a specific file:
```bash
php artisan test tests/Feature/Auth/AuthenticationTest.php
```

---

## Known Gaps & What Needs Attention

These are areas that exist in the database/code but are incomplete, or features that are not yet built at all. Use this as your to-do checklist.

### Priority field is wired up in the DB but not the UI
The `complaints` table has a `priority` column (`low` / `medium` / `high`, default `medium`), and it's in the `Complaint` model's `$fillable`. However:
- `StoreComplaintRequest` does not include a `priority` rule, so citizens cannot set it.
- No priority selector exists on the submit form.
- The staff complaint list/detail views don't display or filter by priority.
- **Fix:** Add `priority` to `StoreComplaintRequest`, add a select field to the citizen submit form, and display it in the staff views.

### No application-specific feature tests
All existing tests are for Breeze auth. There are no tests for:
- Role middleware (citizen can't access staff routes, etc.)
- Complaint submission and ownership enforcement
- Status transition enforcement (invalid transitions return errors)
- Notification creation on status change
- Admin toggling user active/inactive

### Welcome page is still the default Laravel page
`resources/views/welcome.blade.php` is the stock Laravel page. It should be replaced with a landing page describing the system and linking to login/register.

### No admin view of individual complaint details
Admins can see the dashboard table with recent complaints but cannot click into a complaint detail page. There is no `Admin\ComplaintController` or admin complaint show view. Admins cannot view the full history of a complaint.

### Citizens cannot edit or delete their own complaints
Once a complaint is submitted, a citizen has no way to edit or withdraw it. There is no `edit`, `update`, or `destroy` action on `Citizen\ComplaintController`. Whether this is intentional (to preserve audit integrity) should be decided.

### No search or filter on the citizen complaints list
The staff list has search + status filter. The citizen `index` view has pagination but no filtering or searching. Adding a `?status=` filter would be straightforward.

### No staff-to-staff or admin visibility of who is handling what
There's no assignment mechanism — any staff member can update any complaint, but the system doesn't track "assigned to". The `changed_by` in history shows who last touched it but there's no dedicated assignment field.

### Email notifications not implemented
In-app notifications work. However, no email is sent to the citizen when their complaint status changes. Laravel's mail system is available but not wired up. The `Notification` model is a custom DB model, not a Laravel Notification class — so Laravel's built-in `notify()` mail delivery is not used here.

### No complaint image deletion
If a complaint has an image and is deleted (via cascade), the physical file in `storage/app/public/complaints/` is not removed. A model `deleting` observer would be needed to clean up orphaned files.

### `is_active` toggle for categories does not cascade
Deactivating a category hides it from the citizen submit form, but existing complaints that already reference it are unaffected. Reactivating works as expected.
