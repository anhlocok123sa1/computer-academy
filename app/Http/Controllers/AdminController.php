<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminController extends Controller
{
    // Trang Dashboard Quản trị tổng hợp
    // Bảo vệ bởi middleware 'admin' đã đăng ký trong routes/web.php
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'total_enrollments' => Enrollment::count(),
            'pending_deposits' => Transaction::where('type', 'deposit')->where('status', 'pending')->count(),
            'total_revenue' => Transaction::where('type', 'deposit')->where('status', 'approved')->sum('amount'),
        ];

        return Inertia::render('Admin/Index', [
            'stats' => $stats,
            'categories' => Category::withCount('courses')->latest()->get(),
            'courses' => Course::with(['category', 'sections.lessons'])->withCount('enrollments')->latest()->get(),
            'users' => User::withCount('enrollments')->latest()->get(),
            'transactions' => Transaction::with('user')->latest()->take(50)->get(),
        ]);
    }

    // ================= QUẢN LÝ DANH MỤC =================
    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:190']);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . rand(10, 99),
        ]);

        return back();
    }

    public function destroyCategory(Category $category)
    {
        $category->delete();
        return back();
    }

    // ================= QUẢN LÝ KHÓA HỌC =================
    public function storeCourse(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:190',
            'thumbnail' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'level' => 'required|in:beginner,intermediate,advanced',
            'is_published' => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['title']) . '-' . rand(100, 999);
        $course = Course::create($data);

        // Tự động tạo Chương 1 mặc định cho khóa học mới
        Section::create([
            'course_id' => $course->id,
            'title' => 'Chương 1: Giới thiệu & Kiến thức nền tảng',
            'sort_order' => 1,
        ]);

        return back();
    }

    public function updateCourse(Request $request, Course $course)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:190',
            'thumbnail' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'level' => 'required|in:beginner,intermediate,advanced',
            'is_published' => 'boolean',
        ]);

        $course->update($data);
        return back();
    }

    public function destroyCourse(Course $course)
    {
        $course->delete();
        return back();
    }

    // ================= QUẢN LÝ CHƯƠNG & BÀI GIẢNG =================
    public function storeSection(Request $request, Course $course)
    {
        $request->validate(['title' => 'required|string|max:190']);

        Section::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'sort_order' => $course->sections()->count() + 1,
        ]);

        return back();
    }

    public function destroySection(Section $section)
    {
        $section->delete();
        return back();
    }

    public function storeLesson(Request $request, Section $section)
    {
        $data = $request->validate([
            'title' => 'required|string|max:190',
            'type' => 'nullable|string|max:50',
            'video_url' => 'nullable|string',
            'image_url' => 'nullable|string',
            'attachment_url' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'content' => 'nullable|string',
            'exercises' => 'nullable|array',
            'is_free_preview' => 'boolean',
        ]);

        $data['section_id'] = $section->id;
        $data['slug'] = Str::slug($data['title']) . '-' . rand(100, 999);
        $data['sort_order'] = $section->lessons()->count() + 1;
        $data['type'] = $data['type'] ?: 'multimedia';

        Lesson::create($data);
        return back();
    }

    public function updateLesson(Request $request, Lesson $lesson)
    {
        $data = $request->validate([
            'title' => 'required|string|max:190',
            'type' => 'nullable|string|max:50',
            'video_url' => 'nullable|string',
            'image_url' => 'nullable|string',
            'attachment_url' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'content' => 'nullable|string',
            'exercises' => 'nullable|array',
            'is_free_preview' => 'boolean',
        ]);

        $data['type'] = $data['type'] ?: 'multimedia';
        $lesson->update($data);
        return back();
    }

    public function destroyLesson(Lesson $lesson)
    {
        $lesson->delete();
        return back();
    }

    // ================= QUẢN LÝ NGƯỜI DÙNG & SỐ DƯ =================
    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'role' => 'required|in:admin,student',
            'balance' => 'required|numeric|min:0',
        ]);

        $user->update($data);
        return back();
    }

    public function adjustBalance(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $amount = (float) $request->amount;
        $user->increment('balance', $amount);

        Transaction::create([
            'user_id' => $user->id,
            'code' => 'ADM' . strtoupper(substr(uniqid(), -6)),
            'amount' => abs($amount),
            'type' => 'admin_adjust',
            'payment_method' => 'Quản trị viên điều chỉnh',
            'status' => 'approved',
            'note' => $request->note ?: ($amount >= 0 ? 'Admin cộng tiền vào ví' : 'Admin trừ tiền ví'),
        ]);

        return back();
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back();
        }
        $user->delete();
        return back();
    }

    // ================= DUYỆT NẠP TIỀN =================
    public function approveTransaction(Transaction $transaction)
    {
        if ($transaction->status === 'pending') {
            $transaction->update(['status' => 'approved']);
            // Cộng tiền tự động vào ví của người dùng
            $transaction->user->increment('balance', $transaction->amount);
        }

        return back();
    }

    public function rejectTransaction(Transaction $transaction)
    {
        if ($transaction->status === 'pending') {
            $transaction->update(['status' => 'rejected']);
        }

        return back();
    }
}
