# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Municipal Complaint & Issue Tracking System** — a Laravel 11 web application where citizens report infrastructure issues (potholes, broken streetlights, etc.), municipality staff review and update complaints, and admins manage the system.

**Stack:** Laravel 11, PHP 8.2, Laravel Breeze (Blade), Tailwind CSS, MySQL

## Common Commands

```bash
# Install dependencies
composer install
npm install

# Run development server
php artisan serve
npm run dev           # Vite asset watcher (run in parallel with serve)

# Database
php artisan migrate
php artisan migrate:fresh --seed   # Wipe and reseed (dev only)
php artisan db:seed

# Tests
php artisan test                        # Run all tests
php artisan test --filter=ComplaintTest # Run a single test class
php artisan test tests/Feature/Auth/    # Run a directory of tests

# Artisan helpers
php artisan make:model Complaint -mrc  # Model + migration + resource controller
php artisan make:middleware RoleMiddleware
php artisan make:request StoreComplaintRequest
php artisan route:list                 # Inspect registered routes
php artisan storage:link               # Expose public disk for file uploads
```

## Architecture

Standard Laravel MVC. No service layer — logic lives in controllers and models via Eloquent.

```
app/
  Http/
    Controllers/
      Citizen/       # CitizenComplaintController (submit, list, show)
      Staff/         # StaffComplaintController (review, validate, reject, update status)
      Admin/         # AdminUserController, AdminCategoryController, AdminDashboardController
    Middleware/
      RoleMiddleware  # Checks auth()->user()->role against allowed roles
    Requests/        # Form request validation classes
  Models/
    User.php
    Category.php
    Complaint.php
    ComplaintStatusHistory.php
    Notification.php
resources/views/
  layouts/app.blade.php
  citizen/
  staff/
  admin/
database/
  migrations/
  seeders/        # AdminSeeder, CategorySeeder
```

Routes in `routes/web.php` are grouped by role middleware:
- `/citizen/*` — `role:citizen`
- `/staff/*` — `role:staff`
- `/admin/*` — `role:admin`

## Roles & Authorization

Three roles stored in `users.role`: `citizen`, `staff`, `admin`.

`RoleMiddleware` checks `auth()->user()->role` and aborts 403 on mismatch. Applied via route groups — no Laravel Gates/Policies are used (keep it simple for the university scope).

## Models & Key Relationships

```
User          hasMany Complaint (as reporter)
              hasMany Notification
              hasMany ComplaintStatusHistory (as changed_by)

Category      hasMany Complaint

Complaint     belongsTo User, Category
              hasMany ComplaintStatusHistory
              hasMany Notification

ComplaintStatusHistory  belongsTo Complaint
                        belongsTo User (changed_by)

Notification  belongsTo User
              belongsTo Complaint (nullable)
```

## Complaint Lifecycle

Valid statuses (in order): `submitted → pending_review → validated → in_progress → resolved → closed`  
Rejection path: any pre-resolved status → `rejected` (requires `rejection_reason`).

**On every status change:**
1. Insert a `ComplaintStatusHistory` record (old_status, new_status, changed_by, comment).
2. Insert a `Notification` record for the complaint's owner citizen.

Only staff and admin may change complaint status. Citizens read their own complaints only.

## Validation

Use Form Request classes (not inline controller validation) for:
- `StoreComplaintRequest` — title, description, category_id, location required; image optional (mimes:jpg,jpeg,png,gif|max:2048)
- `UpdateComplaintStatusRequest` — new_status required|in:[valid statuses]; rejection_reason required_if:new_status,rejected
- `StoreUserRequest` / `StoreCategoryRequest` — admin forms

## File Uploads

Images stored via `Storage::disk('public')`. Run `php artisan storage:link` once. Store relative path in `complaints.image_path`. Display with `asset('storage/' . $complaint->image_path)`.

## Database Seeding

`DatabaseSeeder` calls:
- `AdminSeeder` — creates one admin user (`admin@municipality.gov` / `password`)
- `CategorySeeder` — inserts initial categories (Potholes, Streetlights, Garbage, Water Leaks, Damaged Roads)

## Implementation Order (for reference)

1. `laravel new`, configure `.env` for MySQL, `npm install`
2. Install Laravel Breeze (`php artisan breeze:install blade`)
3. Add `role`, `is_active` to users migration; run migrations
4. Create `RoleMiddleware`; register in `bootstrap/app.php`
5. Create all models with migrations
6. Create seeders; run `php artisan migrate --seed`
7. Citizen views (dashboard, submit form, complaint list/detail)
8. Staff views (dashboard, complaint detail, validate/reject/status update)
9. Admin views (dashboard stats, user management, category management)
10. Notifications display in navbar/dropdown
