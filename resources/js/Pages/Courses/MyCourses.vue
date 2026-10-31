<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    enrolledCourses: Array
});

const statusFilter = ref('all'); // all | learning | completed
const searchQuery = ref('');

// Thống kê tổng quan của học viên
const stats = computed(() => {
    const total = props.enrolledCourses.length;
    const completed = props.enrolledCourses.filter(c => c.progress_percent === 100).length;
    const inProgress = total - completed;
    const totalLessonsDone = props.enrolledCourses.reduce((sum, c) => sum + c.completed_lessons, 0);
    const totalMinutesLearned = props.enrolledCourses.reduce((sum, c) => sum + c.learned_duration, 0);

    return { total, completed, inProgress, totalLessonsDone, totalMinutesLearned };
});

// Lọc danh sách khóa học của tôi
const filteredCourses = computed(() => {
    return props.enrolledCourses.filter(course => {
        const matchSearch = course.title.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchStatus =
            statusFilter.value === 'all' ||
            (statusFilter.value === 'learning' && course.progress_percent < 100) ||
            (statusFilter.value === 'completed' && course.progress_percent === 100);

        return matchSearch && matchStatus;
    });
});

// Hủy đăng ký khóa học
const confirmUnenroll = (course) => {
    if (confirm(`Bạn có chắc muốn xóa khóa học "${course.title}" khỏi danh sách học tập của mình?`)) {
        router.delete(route('courses.unenroll', course.slug));
    }
};
</script>

<template>
    <Head title="Khóa học của tôi - Computer Academy" />

    <AppLayout>
        <!-- HEADER DASHBOARD HỌC VIÊN -->
        <section class="bg-gradient-to-b from-slate-900 via-[#0a1022] to-[#060913] border-b border-slate-800/80 py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-2xl font-extrabold text-white shadow-lg shadow-blue-600/25">
                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-blue-400">Không gian học tập cá nhân</span>
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">
                                Xin chào, {{ $page.props.auth.user.name }}! 👋
                            </h1>
                            <p class="text-slate-400 text-xs sm:text-sm mt-0.5">
                                Quản lý tiến độ khóa học và tiếp tục các bài giảng đang theo dõi.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('courses.explore')"
                            class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-lg shadow-blue-600/25 transition"
                        >
                            + Đăng ký thêm khóa học
                        </Link>
                        <Link
                            :href="route('profile.edit')"
                            class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs sm:text-sm font-semibold rounded-xl transition"
                        >
                            ⚙ Cài đặt tài khoản
                        </Link>
                    </div>
                </div>

                <!-- 4 THẺ THỐNG KÊ TIẾN ĐỘ -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5">
                        <span class="text-xs text-slate-400 font-medium">Khóa học đã đăng ký</span>
                        <div class="text-2xl sm:text-3xl font-extrabold text-white mt-1">{{ stats.total }}</div>
                        <span class="text-[11px] text-blue-400 mt-1 block">Trọn đời truy cập</span>
                    </div>

                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5">
                        <span class="text-xs text-slate-400 font-medium">Đang theo học</span>
                        <div class="text-2xl sm:text-3xl font-extrabold text-amber-400 mt-1">{{ stats.inProgress }}</div>
                        <span class="text-[11px] text-slate-400 mt-1 block">Khóa học đang mở</span>
                    </div>

                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5">
                        <span class="text-xs text-slate-400 font-medium">Đã hoàn thành (100%)</span>
                        <div class="text-2xl sm:text-3xl font-extrabold text-emerald-400 mt-1">{{ stats.completed }}</div>
                        <span class="text-[11px] text-emerald-400/80 mt-1 block">Đủ điều kiện cấp chứng nhận</span>
                    </div>

                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5">
                        <span class="text-xs text-slate-400 font-medium">Bài giảng đã hoàn tất</span>
                        <div class="text-2xl sm:text-3xl font-extrabold text-indigo-400 mt-1">{{ stats.totalLessonsDone }}</div>
                        <span class="text-[11px] text-slate-400 mt-1 block">⏱ Tích lũy {{ stats.totalMinutesLearned }} phút học</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- DANH SÁCH KHÓA HỌC CỦA TÔI -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <!-- Thanh lọc Tabs & Tìm kiếm -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-2 bg-slate-900 p-1.5 rounded-2xl border border-slate-800 self-start">
                    <button
                        @click="statusFilter = 'all'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition cursor-pointer"
                        :class="statusFilter === 'all' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white'"
                    >
                        Tất cả ({{ enrolledCourses.length }})
                    </button>
                    <button
                        @click="statusFilter = 'learning'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition cursor-pointer"
                        :class="statusFilter === 'learning' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white'"
                    >
                        Đang học ({{ stats.inProgress }})
                    </button>
                    <button
                        @click="statusFilter = 'completed'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition cursor-pointer"
                        :class="statusFilter === 'completed' ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white'"
                    >
                        Đã hoàn thành ({{ stats.completed }})
                    </button>
                </div>

                <!-- Tìm kiếm trong khóa học của tôi -->
                <div class="relative w-full sm:w-72">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Tìm trong khóa học của tôi..."
                        class="w-full pl-9 pr-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500"
                    />
                    <svg class="w-4 h-4 text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Trạng thái chưa đăng ký khóa học nào -->
            <div
                v-if="enrolledCourses.length === 0"
                class="bg-slate-900/60 border border-slate-800 rounded-3xl p-12 text-center max-w-xl mx-auto my-8 space-y-5"
            >
                <div class="w-16 h-16 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center mx-auto text-3xl">
                    📚
                </div>
                <div class="space-y-2">
                    <h3 class="text-xl font-bold text-white">Bạn chưa đăng ký khóa học nào</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Hãy khám phá thư viện khóa học Máy tính & CNTT để thêm khóa học đầu tiên vào lộ trình cá nhân của bạn.
                    </p>
                </div>
                <Link
                    :href="route('courses.explore')"
                    class="inline-block px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-600/25 transition"
                >
                    Khám phá khóa học ngay &rarr;
                </Link>
            </div>

            <!-- Danh sách Card Quản lý Khóa học -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
                <div
                    v-for="course in filteredCourses"
                    :key="course.id"
                    class="bg-slate-900/80 border border-slate-800/90 rounded-2xl overflow-hidden hover:border-blue-500/50 transition flex flex-col shadow-xl"
                >
                    <!-- Ảnh khóa học & Badge trạng thái -->
                    <div class="relative h-44 w-full bg-slate-800 overflow-hidden">
                        <img :src="course.thumbnail" :alt="course.title" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/20 to-transparent"></div>

                        <!-- Trạng thái hoàn thành -->
                        <span
                            class="absolute top-3 left-3 text-[11px] font-bold px-2.5 py-1 rounded-lg backdrop-blur-md border"
                            :class="course.progress_percent === 100
                                ? 'bg-emerald-500/20 border-emerald-500/40 text-emerald-300'
                                : 'bg-blue-500/20 border-blue-500/40 text-blue-300'"
                        >
                            {{ course.progress_percent === 100 ? '✓ Đã hoàn thành' : 'Đang học' }}
                        </span>

                        <!-- Nút xóa/hủy đăng ký góc phải -->
                        <button
                            @click="confirmUnenroll(course)"
                            class="absolute top-3 right-3 w-8 h-8 rounded-lg bg-slate-950/75 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer"
                            title="Xóa khỏi khóa học của tôi"
                        >
                            ✕
                        </button>

                        <div class="absolute bottom-3 left-4 right-4 flex items-center justify-between text-xs text-slate-300">
                            <span class="font-semibold text-blue-400">{{ course.category_name }}</span>
                            <span>Đăng ký: {{ course.enrolled_at }}</span>
                        </div>
                    </div>

                    <!-- Nội dung & Thanh Tiến độ -->
                    <div class="p-6 flex-1 flex flex-col justify-between space-y-5">
                        <div>
                            <Link :href="route('courses.show', course.slug)">
                                <h3 class="text-lg font-bold text-white hover:text-blue-400 transition line-clamp-2 mb-4">
                                    {{ course.title }}
                                </h3>
                            </Link>

                            <!-- Progress Bar -->
                            <div class="space-y-2 bg-slate-950/80 border border-slate-800/80 p-3.5 rounded-xl">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-400">
                                        Hoàn thành <strong class="text-white">{{ course.completed_lessons }}/{{ course.total_lessons }}</strong> bài học
                                    </span>
                                    <span
                                        class="font-extrabold"
                                        :class="course.progress_percent === 100 ? 'text-emerald-400' : 'text-blue-400'"
                                    >
                                        {{ course.progress_percent }}%
                                    </span>
                                </div>
                                <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                                    <div
                                        class="h-full transition-all duration-500 rounded-full"
                                        :class="course.progress_percent === 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-blue-500 to-indigo-500'"
                                        :style="{ width: course.progress_percent + '%' }"
                                    ></div>
                                </div>
                            </div>

                            <!-- Gợi ý bài học tiếp theo -->
                            <div v-if="course.next_lesson" class="mt-4 text-xs text-slate-400">
                                <span class="block text-[11px] uppercase tracking-wider text-slate-500 font-semibold mb-1">
                                    {{ course.progress_percent === 100 ? 'Ôn tập lại bài giảng:' : 'Bài học tiếp theo:' }}
                                </span>
                                <span class="text-slate-200 font-medium line-clamp-1">
                                    ▶ {{ course.next_lesson.title }}
                                </span>
                            </div>
                        </div>

                        <!-- Nút hành động -->
                        <div class="pt-4 border-t border-slate-800/80 flex items-center gap-2.5">
                            <Link
                                v-if="course.next_lesson"
                                :href="route('courses.learn', [course.slug, course.next_lesson.slug])"
                                class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold text-center text-white transition shadow-lg"
                                :class="course.progress_percent === 100
                                    ? 'bg-emerald-600 hover:bg-emerald-500 shadow-emerald-600/20'
                                    : 'bg-blue-600 hover:bg-blue-500 shadow-blue-600/20'"
                            >
                                {{ course.progress_percent === 0 ? '▶ Bắt đầu học' : (course.progress_percent === 100 ? '↻ Xem lại khóa học' : '▶ Học tiếp ngay') }}
                            </Link>

                            <Link
                                :href="route('courses.show', course.slug)"
                                class="py-2.5 px-3.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs sm:text-sm font-semibold rounded-xl transition"
                                title="Xem đề cương khóa học"
                            >
                                Đề cương
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
