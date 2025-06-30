<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\GroupController;

// Language Switch
Route::get('/lang/{locale}', function ($locale) {
    session()->put('locale', $locale);
    return redirect()->back();
})->name('lang.switch');

// Authentication Routes
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Course Management
    Route::resource('courses', CourseController::class);
    Route::get('courses/{course}/assign', [CourseController::class, 'showAssignForm'])->name('courses.assign.form');
    Route::post('courses/{course}/assign', [CourseController::class, 'assign'])->name('courses.assign');
    Route::get('courses/report', [CourseController::class, 'report'])->name('courses.report');

    // Chapter Management
    Route::resource('courses.chapters', ChapterController::class);

    // Material Management
    Route::resource('chapters.materials', MaterialController::class);

    // Group Management (for HR)
    Route::resource('groups', GroupController::class)->middleware('can:manage-groups');
    
    // Progress Tracking (for students)
    Route::post('progress/update', [ProgressController::class, 'update'])->name('progress.update');
});
