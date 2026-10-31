<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Các trang công khai
Route::get('/', [CourseController::class, 'index'])->name('courses.index');
Route::get('/khoa-hoc', [CourseController::class, 'explore'])->name('courses.explore');
Route::get('/khoa-hoc/{slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/hoc/{courseSlug}/{lessonSlug}', [CourseController::class, 'learn'])->name('courses.learn');

// Khu vực dành cho Học viên đã đăng nhập
Route::middleware(['auth'])->group(function () {
    Route::get('/khoa-hoc-cua-toi', [CourseController::class, 'myCourses'])->name('courses.my');
    Route::post('/khoa-hoc/{slug}/dang-ky', [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::delete('/khoa-hoc/{slug}/huy-dang-ky', [CourseController::class, 'unenroll'])->name('courses.unenroll');
    Route::post('/bai-hoc/{lesson}/tien-do', [CourseController::class, 'toggleProgress'])->name('lessons.progress');
    Route::post('/bai-hoc/{lesson}/ghi-chu', [CourseController::class, 'saveNote'])->name('lessons.note');

    // Hồ sơ cá nhân & Nạp tiền
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/deposit', [ProfileController::class, 'deposit'])->name('profile.deposit');
    Route::get('/profile/deposit/{transaction}/status', [ProfileController::class, 'depositStatus'])->name('profile.deposit.status');
    Route::delete('/profile/deposit/{transaction}', [ProfileController::class, 'cancelDeposit'])->name('profile.deposit.cancel');
    Route::post('/profile/deposit/{transaction}/simulate', [ProfileController::class, 'simulateDeposit'])->name('profile.deposit.simulate');
});

// HỆ THỐNG QUẢN TRỊ VIÊN (ADMIN CMS)
// Middleware 'auth' yêu cầu đăng nhập, 'admin' đảm bảo chỉ role=admin mới vào được
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Danh mục
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');

    // Khóa học
    Route::post('/courses', [AdminController::class, 'storeCourse'])->name('courses.store');
    Route::put('/courses/{course}', [AdminController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{course}', [AdminController::class, 'destroyCourse'])->name('courses.destroy');

    // Chương & Bài giảng
    Route::post('/courses/{course}/sections', [AdminController::class, 'storeSection'])->name('sections.store');
    Route::delete('/sections/{section}', [AdminController::class, 'destroySection'])->name('sections.destroy');
    Route::post('/sections/{section}/lessons', [AdminController::class, 'storeLesson'])->name('lessons.store');
    Route::put('/lessons/{lesson}', [AdminController::class, 'updateLesson'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [AdminController::class, 'destroyLesson'])->name('lessons.destroy');

    // Người dùng & Số dư
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{user}/balance', [AdminController::class, 'adjustBalance'])->name('users.balance');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Duyệt giao dịch nạp tiền
    Route::post('/transactions/{transaction}/approve', [AdminController::class, 'approveTransaction'])->name('transactions.approve');
    Route::post('/transactions/{transaction}/reject', [AdminController::class, 'rejectTransaction'])->name('transactions.reject');
});

Route::get('/dashboard', function () {
    return auth()->user()?->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('courses.my');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
