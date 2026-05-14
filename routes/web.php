<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Citizen\ComplaintController as CitizenComplaintController;
use App\Http\Controllers\Citizen\NotificationController as CitizenNotificationController;
use App\Http\Controllers\Staff\ComplaintController as StaffComplaintController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Role-based dashboard redirect after login
Route::get('/dashboard', function () {
    return match(auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'staff' => redirect()->route('staff.dashboard'),
        default => redirect()->route('citizen.dashboard'),
    };
})->middleware('auth')->name('dashboard');

// Citizen routes
Route::middleware(['auth', 'role:citizen'])
    ->prefix('citizen')
    ->name('citizen.')
    ->group(function () {
        Route::get('/dashboard', [CitizenComplaintController::class, 'dashboard'])->name('dashboard');
        Route::get('/complaints', [CitizenComplaintController::class, 'index'])->name('complaints.index');
        Route::get('/complaints/create', [CitizenComplaintController::class, 'create'])->name('complaints.create');
        Route::post('/complaints', [CitizenComplaintController::class, 'store'])->middleware('throttle:10,1')->name('complaints.store');
        Route::get('/complaints/{complaint}', [CitizenComplaintController::class, 'show'])->name('complaints.show');

        // Notifications
        Route::get('/notifications', [CitizenNotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{notification}/read', [CitizenNotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [CitizenNotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    });

// Staff routes
Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/dashboard', [StaffComplaintController::class, 'dashboard'])->name('dashboard');
        Route::get('/complaints', [StaffComplaintController::class, 'index'])->name('complaints.index');
        Route::get('/complaints/{complaint}', [StaffComplaintController::class, 'show'])->name('complaints.show');
        Route::patch('/complaints/{complaint}/status', [StaffComplaintController::class, 'updateStatus'])->name('complaints.updateStatus');
    });

// Admin routes
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // User management
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggleActive');

        // Category management
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::patch('/categories/{category}/toggle-active', [AdminCategoryController::class, 'toggleActive'])->name('categories.toggleActive');
    });

// Profile (any authenticated user)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
