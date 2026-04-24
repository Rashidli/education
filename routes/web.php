<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherPayoutController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', DashboardController::class)
        ->middleware('can:dashboard.view')->name('dashboard');

    // Teachers — manage routes BEFORE {teacher} to avoid create being treated as id
    Route::middleware('can:teachers.manage')->group(function () {
        Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
    });
    Route::middleware('can:teachers.view')->group(function () {
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])
            ->whereNumber('teacher')->name('teachers.show');
    });

    // Groups
    Route::middleware('can:groups.manage')->group(function () {
        Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
        Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
        Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
        Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
        Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');
    });
    Route::middleware('can:groups.view')->group(function () {
        Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
        Route::get('/groups/{group}', [GroupController::class, 'show'])
            ->whereNumber('group')->name('groups.show');
    });

    // Students
    Route::middleware('can:students.manage')->group(function () {
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    });
    Route::middleware('can:students.view')->group(function () {
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [StudentController::class, 'show'])
            ->whereNumber('student')->name('students.show');
    });

    // Enrollments
    Route::middleware('can:enrollments.manage')->group(function () {
        Route::get('/enrollments/preview', [EnrollmentController::class, 'preview'])->name('enrollments.preview');
        Route::post('/students/{student}/enrollments', [EnrollmentController::class, 'store'])->name('students.enrollments.store');
        Route::delete('/students/{student}/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('students.enrollments.destroy');
    });

    // Teacher payouts
    Route::get('/teachers/{teacher}/payouts', [TeacherPayoutController::class, 'show'])
        ->middleware('can:payouts.view')->whereNumber('teacher')->name('teachers.payouts');
    Route::post('/teachers/{teacher}/payouts', [TeacherPayoutController::class, 'store'])
        ->middleware('can:payouts.create')->whereNumber('teacher')->name('teachers.payouts.store');
    Route::delete('/teachers/{teacher}/payouts/{payout}', [TeacherPayoutController::class, 'destroy'])
        ->middleware('can:payouts.delete')->whereNumber('teacher')->name('teachers.payouts.destroy');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])
        ->middleware('can:payments.view')->name('payments.index');
    Route::get('/payments/create', [PaymentController::class, 'create'])
        ->middleware('can:payments.create')->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])
        ->middleware('can:payments.create')->name('payments.store');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])
        ->middleware('can:payments.delete')->name('payments.destroy');

    // Settings
    Route::middleware('can:settings.manage')->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });

    // Reports
    Route::middleware('can:reports.view')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
        Route::get('/teachers', [ReportController::class, 'teachers'])->name('teachers');
        Route::get('/students', [ReportController::class, 'students'])->name('students');
    });

    // Users & Roles
    Route::middleware('can:users.manage')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
    });

    // Trash — yalnız Super Admin
    Route::middleware('role:Super Admin')->prefix('trash')->name('trash.')->group(function () {
        Route::get('/', [TrashController::class, 'index'])->name('index');
        Route::post('/{type}/{id}/restore', [TrashController::class, 'restore'])->name('restore');
        Route::delete('/{type}/{id}', [TrashController::class, 'forceDelete'])->name('forceDelete');
    });
});
