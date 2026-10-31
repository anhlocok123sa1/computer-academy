<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CourseController extends Controller
{
    // 1. Trang chủ
    public function index()
    {
        $categories = Category::all();
        $courses = Course::with(['category', 'sections.lessons'])
            ->where('is_published', true)
            ->latest()
            ->get();

        return Inertia::render('Courses/Index', [
            'categories' => $categories,
            'courses' => $courses
        ]);
    }

    // 2. Trang Khám phá khóa học
    public function explore(Request $request)
    {
        $categories = Category::all();
        $courses = Course::with(['category', 'sections.lessons'])
            ->where('is_published', true)
            ->latest()
            ->get();

        return Inertia::render('Courses/Explore', [
            'categories' => $categories,
            'courses' => $courses,
            'initialFilters' => [
                'search' => $request->query('search', ''),
                'category' => $request->query('category', 'all'),
            ]
        ]);
    }

    // 3. Trang Chi tiết khóa học
    public function show($slug)
    {
        $course = Course::with(['category', 'sections.lessons'])
            ->where('slug', $slug)
            ->firstOrFail();

        $isEnrolled = auth()->check()
            ? Enrollment::where('user_id', auth()->id())->where('course_id', $course->id)->exists()
            : false;

        return Inertia::render('Courses/Show', [
            'course' => $course,
            'isEnrolled' => $isEnrolled,
        ]);
    }

    // 4. Đăng ký / Mua khóa học bằng số dư Ví
    public function enroll($slug)
    {
        $course = Course::with('sections.lessons')->where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        // Kiểm tra nếu đã đăng ký rồi thì chuyển thẳng vào trang Khóa học của tôi
        $alreadyEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($alreadyEnrolled) {
            return redirect()->route('courses.my');
        }

        $price = (float) $course->price;

        // Nếu khóa học có phí, kiểm tra số dư ví trước khi vào transaction
        if ($price > 0 && (float) $user->balance < $price) {
            return redirect()->route('profile.edit')
                ->with('status', 'insufficient-balance');
        }

        // Bọc toàn bộ thao tác tài chính + đăng ký trong DB transaction
        // để đảm bảo tính nguyên tử (atomic): hoặc thành công tất cả, hoặc rollback tất cả
        try {
            DB::transaction(function () use ($user, $course, $price) {
                if ($price > 0) {
                    // Khóa bản ghi user để tránh race condition (double-spend)
                    $user->lockForUpdate()->find($user->id);

                    // Kiểm tra lại số dư trong transaction (sau khi lock)
                    $user->refresh();
                    if ((float) $user->balance < $price) {
                        throw new \RuntimeException('insufficient-balance');
                    }

                    // Trừ tiền và ghi lịch sử giao dịch
                    $user->decrement('balance', $price);

                    Transaction::create([
                        'user_id'        => $user->id,
                        'code'           => 'MUA' . strtoupper(Str::random(6)),
                        'amount'         => $price,
                        'type'           => 'purchase',
                        'payment_method' => 'Ví ComputerAcademy',
                        'status'         => 'approved',
                        'note'           => 'Thanh toán khóa học: ' . $course->title,
                    ]);
                }

                Enrollment::create([
                    'user_id'   => $user->id,
                    'course_id' => $course->id,
                ]);
            });
        } catch (\RuntimeException $e) {
            // Số dư không đủ sau khi lock — rollback đã xảy ra tự động
            return redirect()->route('profile.edit')
                ->with('status', 'insufficient-balance');
        }

        return redirect()->route('courses.my');
    }

    // 5. Hủy đăng ký khóa học khỏi danh sách cá nhân
    public function unenroll($slug)
    {
        $course = Course::with('sections.lessons')->where('slug', $slug)->firstOrFail();

        // Xóa bản ghi đăng ký
        Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->delete();

        // Xóa tiến độ bài học thuộc khóa này của user
        $lessonIds = $course->sections->flatMap(fn($s) => $s->lessons->pluck('id'));
        LessonProgress::where('user_id', auth()->id())
            ->whereIn('lesson_id', $lessonIds)
            ->delete();

        return redirect()->route('courses.my');
    }

    // 6. Hệ thống Quản lý Khóa học của tôi (Student Dashboard)
    public function myCourses()
    {
        $userId = auth()->id();

        // Lấy danh sách ID tất cả bài giảng user đã hoàn thành
        $completedLessonIds = LessonProgress::where('user_id', $userId)
            ->pluck('lesson_id')
            ->toArray();

        // Lấy danh sách khóa học đã đăng ký
        $enrollments = Enrollment::with(['course.category', 'course.sections.lessons'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $enrolledCourses = $enrollments->map(function ($enrollment) use ($completedLessonIds) {
            $course = $enrollment->course;
            $allLessons = $course->sections->flatMap(fn($sec) => $sec->lessons);

            $totalLessons = $allLessons->count();
            $completedLessons = $allLessons->whereIn('id', $completedLessonIds);
            $completedCount = $completedLessons->count();

            $progressPercent = $totalLessons > 0
                ? round(($completedCount / $totalLessons) * 100)
                : 0;

            // Tìm bài học đầu tiên chưa hoàn thành để làm nút "Học tiếp"
            $nextLesson = $allLessons->first(fn($l) => !in_array($l->id, $completedLessonIds))
                ?? $allLessons->first();

            $totalDuration = $allLessons->sum('duration');
            $learnedDuration = $completedLessons->sum('duration');

            return [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'thumbnail' => $course->thumbnail,
                'level' => $course->level,
                'category_name' => $course->category?->name ?? 'CNTT',
                'enrolled_at' => $enrollment->created_at->format('d/m/Y'),
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedCount,
                'progress_percent' => $progressPercent,
                'total_duration' => $totalDuration,
                'learned_duration' => $learnedDuration,
                'next_lesson' => $nextLesson ? [
                    'title' => $nextLesson->title,
                    'slug' => $nextLesson->slug,
                ] : null,
            ];
        });

        return Inertia::render('Courses/MyCourses', [
            'enrolledCourses' => $enrolledCourses
        ]);
    }

    // 7. Phòng học Video (Đồng bộ với tiến độ Database)
    public function learn($courseSlug, $lessonSlug)
    {
        $course = Course::with(['category', 'sections.lessons'])
            ->where('slug', $courseSlug)
            ->firstOrFail();

        $lesson = Lesson::where('slug', $lessonSlug)->firstOrFail();

        $user = auth()->user();
        $completedLessonIds = [];

        if ($user) {
            $isEnrolled = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->exists();

            if (! $isEnrolled) {
                if ((float) $course->price > 0) {
                    // Khóa học có phí — chưa thanh toán, không được vào
                    // Redirect về trang chi tiết khóa học với thông báo
                    return redirect()->route('courses.show', $course->slug)
                        ->with('status', 'enrollment-required');
                }

                // Khóa học miễn phí — tự động enroll để theo dõi tiến độ
                Enrollment::create([
                    'user_id'   => $user->id,
                    'course_id' => $course->id,
                ]);
            }

            $completedLessonIds = LessonProgress::where('user_id', $user->id)
                ->pluck('lesson_id')
                ->toArray();

            $userNote = \App\Models\LessonNote::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->value('content');
        } else {
            // Chưa đăng nhập: chỉ cho xem bài preview miễn phí
            if (! $lesson->is_free_preview) {
                return redirect()->route('login')
                    ->with('status', 'login-required');
            }
            $userNote = null;
        }

        return Inertia::render('Courses/Watch', [
            'course'             => $course,
            'currentLesson'      => $lesson,
            'completedLessonIds' => $completedLessonIds,
            'initialNote'        => $userNote ?? '',
        ]);
    }

    // 8. Lưu trạng thái hoàn thành bài học vào Database
    public function toggleProgress(Lesson $lesson)
    {
        $userId = auth()->id();

        $existing = LessonProgress::where('user_id', $userId)
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            LessonProgress::create([
                'user_id' => $userId,
                'lesson_id' => $lesson->id
            ]);
        }

        return back();
    }

    // 9. Lưu ghi chú cá nhân của bài học (CKEditor)
    public function saveNote(\Illuminate\Http\Request $request, Lesson $lesson)
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để lưu ghi chú'], 401);
        }

        $note = \App\Models\LessonNote::updateOrCreate(
            ['user_id' => $userId, 'lesson_id' => $lesson->id],
            ['content' => $request->input('content', '')]
        );

        return response()->json(['success' => true, 'note' => $note]);
    }
}