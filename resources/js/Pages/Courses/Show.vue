<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    course: Object,
    isEnrolled: Boolean,
    relatedCourses: Array
});

// Lấy flash message từ session (VD: enrollment-required)
const flash = computed(() => usePage().props.flash ?? {});
const showEnrollAlert = computed(() => flash.value.status === 'enrollment-required');

// Quản lý đóng/mở từng chương (mặc định mở tất cả)
const openSections = ref(props.course.sections?.map(s => s.id) || []);

const toggleSection = (id) => {
    if (openSections.value.includes(id)) {
        openSections.value = openSections.value.filter(item => item !== id);
    } else {
        openSections.value.push(id);
    }
};

const firstLesson = computed(() => {
    return props.course.sections?.[0]?.lessons?.[0] || null;
});

const totalLessons = computed(() => {
    return props.course.sections?.reduce((acc, s) => acc + (s.lessons?.length || 0), 0) || 0;
});

const totalDuration = computed(() => {
    return props.course.sections?.reduce((acc, s) => {
        return acc + (s.lessons?.reduce((sum, l) => sum + (l.duration || 0), 0) || 0);
    }, 0) || 0;
});

const enrollCourse = () => {
    router.post(route('courses.enroll', props.course.slug));
};
</script>

<template>
    <Head :title="course.title + ' - Computer Academy'" />

    <AppLayout>
        <!-- BANNER: Yêu cầu đăng ký trước khi học (hiện khi bị chặn từ learn()) -->
        <div
            v-if="showEnrollAlert"
            class="bg-amber-500/10 border-b border-amber-500/30 px-4 py-3"
        >
            <div class="max-w-7xl mx-auto flex items-center gap-3">
                <span class="text-amber-400 text-lg shrink-0">⚠</span>
                <p class="text-sm text-amber-300">
                    <span class="font-semibold">Khóa học này có phí.</span>
                    Bạn cần đăng ký và thanh toán để truy cập bài học.
                    Vui lòng nhấn <span class="font-semibold text-amber-200">"Đăng ký khóa học"</span> bên dưới.
                </p>
            </div>
        </div>
        <!-- TOP BANNER HEADER -->
        <div class="bg-gradient-to-b from-slate-900 via-[#0a1022] to-[#060913] border-b border-slate-800/80 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <div class="flex items-center gap-2 text-xs text-slate-400 mb-6">
                    <Link href="/" class="hover:text-blue-400 transition">Trang chủ</Link>
                    <span>/</span>
                    <span class="text-blue-400 font-medium">{{ course.category?.name }}</span>
                    <span>/</span>
                    <span class="text-slate-300 truncate max-w-xs">{{ course.title }}</span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                    <!-- Cột trái: Tiêu đề & Thông tin chính -->
                    <div class="lg:col-span-8 space-y-5">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-semibold">
                            <span>Chuyên ngành: {{ course.category?.name }}</span>
                        </div>

                        <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
                            {{ course.title }}
                        </h1>

                        <p class="text-slate-300 text-base leading-relaxed">
                            {{ course.description }}
                        </p>

                        <!-- Thông số nhanh -->
                        <div class="flex flex-wrap items-center gap-6 pt-2 text-sm text-slate-300">
                            <div class="flex items-center gap-1.5">
                                <span class="text-amber-400 font-bold">★ 4.9</span>
                                <span class="text-slate-400">(Đánh giá học viên)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-blue-400">📚</span>
                                <span>{{ course.sections?.length || 0 }} chương • {{ totalLessons }} bài giảng</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-emerald-400">⏱</span>
                                <span>Thời lượng {{ totalDuration }} phút</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN BODY (2 CỘT) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

                <!-- CỘT TRÁI (8 CỘT): Lợi ích & Đề cương khóa học -->
                <div class="lg:col-span-8 space-y-10">
                    <!-- Box: Bạn sẽ học được gì? -->
                    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 sm:p-8">
                        <h2 class="text-xl font-bold text-white mb-5">Kiến thức đạt được sau khóa học</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-slate-300">
                            <div class="flex items-start gap-3">
                                <span class="text-emerald-400 font-bold mt-0.5">✓</span>
                                <span>Nắm vững nguyên lý cốt lõi và quy trình triển khai thực tế.</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="text-emerald-400 font-bold mt-0.5">✓</span>
                                <span>Thực hành trực tiếp qua các bài Lab và tình huống thực chiến.</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="text-emerald-400 font-bold mt-0.5">✓</span>
                                <span>Tự tin xử lý sự cố (Troubleshooting) và tối ưu hóa hệ thống.</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="text-emerald-400 font-bold mt-0.5">✓</span>
                                <span>Áp dụng ngay vào công việc và các dự án doanh nghiệp.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Đề cương khóa học (Syllabus Accordion) -->
                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <h2 class="text-2xl font-bold text-white">Nội dung khóa học</h2>
                                <p class="text-xs text-slate-400 mt-1">
                                    {{ course.sections?.length || 0 }} chương • {{ totalLessons }} bài học • Tổng thời lượng {{ totalDuration }} phút
                                </p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div
                                v-for="section in course.sections"
                                :key="section.id"
                                class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden"
                            >
                                <!-- Tiêu đề Chương -->
                                <button
                                    @click="toggleSection(section.id)"
                                    class="w-full px-6 py-4 bg-slate-800/60 hover:bg-slate-800 flex items-center justify-between text-left transition cursor-pointer"
                                >
                                    <div class="flex items-center gap-3">
                                        <svg
                                            class="w-4 h-4 text-blue-400 transition-transform duration-200"
                                            :class="{ 'rotate-90': openSections.includes(section.id) }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                        <span class="font-bold text-white text-base">{{ section.title }}</span>
                                    </div>
                                    <span class="text-xs text-slate-400 font-medium">
                                        {{ section.lessons?.length || 0 }} bài học
                                    </span>
                                </button>

                                <!-- Danh sách Bài học trong Chương -->
                                <ul v-show="openSections.includes(section.id)" class="divide-y divide-slate-800/70">
                                    <li
                                        v-for="lesson in section.lessons"
                                        :key="lesson.id"
                                        class="px-6 py-4 flex items-center justify-between hover:bg-slate-800/30 transition gap-4"
                                    >
                                        <div class="flex items-center gap-3.5 min-w-0">
                                            <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center shrink-0 text-xs">
                                                ▶
                                            </div>
                                            <div class="min-w-0">
                                                <Link
                                                    :href="route('courses.learn', [course.slug, lesson.slug])"
                                                    class="text-sm font-medium text-slate-200 hover:text-blue-400 transition block truncate"
                                                >
                                                    {{ lesson.title }}
                                                </Link>
                                                <span
                                                    v-if="lesson.is_free_preview"
                                                    class="inline-block mt-1 text-[11px] bg-emerald-500/15 text-emerald-400 px-2 py-0.5 rounded font-medium"
                                                >
                                                    Học thử miễn phí
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-4 shrink-0">
                                            <span class="text-xs text-slate-400">{{ lesson.duration }} phút</span>
                                            <Link
                                                :href="route('courses.learn', [course.slug, lesson.slug])"
                                                class="text-xs font-semibold text-blue-400 hover:text-blue-300 bg-blue-500/10 px-3 py-1.5 rounded-lg transition"
                                            >
                                                Vào học
                                            </Link>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CỘT PHẢI (4 CỘT): STICKY ENROLL CARD -->
                <div class="lg:col-span-4 lg:sticky lg:top-28">
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-2xl">
                        <!-- Ảnh Preview có nút Play -->
                        <div class="relative h-52 w-full bg-slate-800 overflow-hidden group">
                            <img :src="course.thumbnail" :alt="course.title" class="w-full h-full object-cover" />
                            <div class="absolute inset-0 bg-slate-950/40 flex items-center justify-center">
                                <Link
                                    v-if="firstLesson"
                                    :href="route('courses.learn', [course.slug, firstLesson.slug])"
                                    class="w-14 h-14 rounded-full bg-blue-600/90 hover:bg-blue-500 text-white flex items-center justify-center shadow-xl hover:scale-110 transition"
                                >
                                    ▶
                                </Link>
                            </div>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Giá khóa học -->
                            <div>
                                <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Trọn gói khóa học</span>
                                <div class="text-3xl font-extrabold mt-1" :class="Number(course.price) === 0 ? 'text-emerald-400' : 'text-white'">
                                    {{ Number(course.price) === 0 ? 'Miễn phí 100%' : Number(course.price).toLocaleString('vi-VN') + ' đ' }}
                                </div>
                            </div>

                            <!-- Nút hành động chính -->
                            <div class="space-y-3">
                                <Link
                                    v-if="firstLesson"
                                    :href="route('courses.learn', [course.slug, firstLesson.slug])"
                                    class="w-full py-3.5 px-6 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-600/25 flex items-center justify-center gap-2 transition"
                                >
                                    <span>▶ Bắt đầu học ngay</span>
                                </Link>

                                <button
                                    v-if="!isEnrolled"
                                    @click="enrollCourse"
                                    class="w-full py-3 px-6 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition cursor-pointer"
                                >
                                    + Lưu vào khóa học của tôi
                                </button>
                            </div>

                            <!-- Quyền lợi đi kèm -->
                            <div class="pt-5 border-t border-slate-800 text-xs text-slate-300 space-y-3">
                                <div class="font-semibold text-white text-sm mb-2">Khóa học này bao gồm:</div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Số lượng bài giảng:</span>
                                    <span class="font-semibold">{{ totalLessons }} bài học</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Tổng thời lượng video:</span>
                                    <span class="font-semibold">{{ totalDuration }} phút</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Hình thức học:</span>
                                    <span class="font-semibold text-emerald-400">Online 24/7 trọn đời</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Tài liệu & Source code:</span>
                                    <span class="font-semibold">Đính kèm từng bài</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
