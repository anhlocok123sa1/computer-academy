<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Ckeditor from '@/Components/Ckeditor.vue';

const props = defineProps({
    stats: Object,
    categories: Array,
    courses: Array,
    users: Array,
    transactions: Array,
});

const activeTab = ref('courses'); // courses | deposits | users | categories

// =========================================================================
// 1. QUẢN LÝ KHÓA HỌC (TẠO / SỬA / XÓA)
// =========================================================================
const showCourseModal = ref(false);
const editingCourse = ref(null);

const courseForm = useForm({
    category_id: props.categories[0]?.id || '',
    title: '',
    thumbnail: 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=900&q=80',
    description: '',
    price: 0,
    level: 'beginner',
    is_published: true,
});

const openCreateCourse = () => {
    editingCourse.value = null;
    courseForm.reset();
    courseForm.category_id = props.categories[0]?.id || '';
    showCourseModal.value = true;
};

const openEditCourse = (course) => {
    editingCourse.value = course;
    courseForm.category_id = course.category_id;
    courseForm.title = course.title;
    courseForm.thumbnail = course.thumbnail;
    courseForm.description = course.description;
    courseForm.price = course.price;
    courseForm.level = course.level;
    courseForm.is_published = Boolean(course.is_published);
    showCourseModal.value = true;
};

const submitCourse = () => {
    if (editingCourse.value) {
        courseForm.put(route('admin.courses.update', editingCourse.value.id), {
            onSuccess: () => (showCourseModal.value = false),
        });
    } else {
        courseForm.post(route('admin.courses.store'), {
            onSuccess: () => (showCourseModal.value = false),
        });
    }
};

const deleteCourse = (course) => {
    if (confirm(`Xác nhận xóa vĩnh viễn khóa học "${course.title}"?`)) {
        router.delete(route('admin.courses.destroy', course.id));
    }
};

// =========================================================================
// 2. QUẢN LÝ CHƯƠNG & BÀI HỌC ĐA ĐỊNH DẠNG (VIDEO + ẢNH + TÀI LIỆU)
// =========================================================================
const selectedCourseForCurriculum = ref(null);
const sectionForm = useForm({ title: '' });

// Form thêm bài học mới
const lessonForm = useForm({
    section_id: null,
    title: '',
    type: 'multimedia',
    video_url: '',
    image_url: '',
    attachment_url: '',
    duration: 15,
    content: '',
    exercises: [],
    is_free_preview: false,
});

// Modal & Form chỉnh sửa bài học
const showEditLessonModal = ref(false);
const editingLesson = ref(null);
const editLessonForm = useForm({
    title: '',
    type: 'multimedia',
    video_url: '',
    image_url: '',
    attachment_url: '',
    duration: 15,
    content: '',
    exercises: [],
    is_free_preview: false,
});

// Quản lý cấu trúc Bài tập riêng biệt (Trắc nghiệm & Thực hành)
const addQuizQuestion = (form) => {
    if (!form.exercises) form.exercises = [];
    form.exercises.push({
        id: 'quiz_' + Date.now(),
        type: 'quiz',
        question: '',
        options: ['', '', '', ''],
        correct_index: 0,
        explanation: '',
    });
};

const addPracticalTask = (form) => {
    if (!form.exercises) form.exercises = [];
    form.exercises.push({
        id: 'practical_' + Date.now(),
        type: 'practical',
        title: '',
        description: '',
        requirements: '',
        solution: '',
    });
};

const removeExercise = (form, index) => {
    form.exercises.splice(index, 1);
};

const addOption = (exercise) => {
    if (!exercise.options) exercise.options = [];
    if (exercise.options.length < 6) {
        exercise.options.push('');
    }
};

const removeOption = (exercise, optIdx) => {
    if (exercise.options.length > 2) {
        exercise.options.splice(optIdx, 1);
        if (exercise.correct_index >= exercise.options.length) {
            exercise.correct_index = 0;
        }
    }
};

const addSection = (course) => {
    if (!sectionForm.title.trim()) return;
    sectionForm.post(route('admin.sections.store', course.id), {
        preserveScroll: true,
        onSuccess: () => sectionForm.reset(),
    });
};

const deleteSection = (sectionId) => {
    if (confirm('Xác nhận xóa chương này và toàn bộ bài học bên trong?')) {
        router.delete(route('admin.sections.destroy', sectionId), { preserveScroll: true });
    }
};

const openAddLessonForm = (sectionId) => {
    if (lessonForm.section_id === sectionId) {
        lessonForm.section_id = null;
    } else {
        lessonForm.reset();
        lessonForm.section_id = sectionId;
        lessonForm.duration = 15;
        lessonForm.type = 'multimedia';
        lessonForm.exercises = [];
        lessonForm.is_free_preview = false;
    }
};

const addLesson = (sectionId) => {
    lessonForm.post(route('admin.lessons.store', sectionId), {
        preserveScroll: true,
        onSuccess: () => {
            lessonForm.reset();
            lessonForm.section_id = null;
        },
    });
};

const openEditLesson = (lesson) => {
    editingLesson.value = lesson;
    editLessonForm.title = lesson.title;
    editLessonForm.type = lesson.type || 'multimedia';
    editLessonForm.video_url = lesson.video_url || '';
    editLessonForm.image_url = lesson.image_url || '';
    editLessonForm.attachment_url = lesson.attachment_url || '';
    editLessonForm.duration = lesson.duration || 15;
    editLessonForm.content = lesson.content || '';
    editLessonForm.exercises = Array.isArray(lesson.exercises) ? JSON.parse(JSON.stringify(lesson.exercises)) : [];
    editLessonForm.is_free_preview = Boolean(lesson.is_free_preview);
    showEditLessonModal.value = true;
};

const submitEditLesson = () => {
    if (!editingLesson.value) return;
    editLessonForm.put(route('admin.lessons.update', editingLesson.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEditLessonModal.value = false;
            editingLesson.value = null;
        },
    });
};

const deleteLesson = (lessonId) => {
    if (confirm('Xác nhận xóa bài học này?')) {
        router.delete(route('admin.lessons.destroy', lessonId), { preserveScroll: true });
    }
};

// =========================================================================
// 3. QUẢN LÝ NGƯỜI DÙNG & CỘNG TIỀN VÍ
// =========================================================================
const balanceModalUser = ref(null);
const balanceForm = useForm({
    amount: 100000,
    note: 'Quản trị viên cộng tiền khuyến mãi / nạp hộ',
});

const openBalanceModal = (user) => {
    balanceModalUser.value = user;
    balanceForm.amount = 100000;
};

const submitBalanceAdjust = () => {
    balanceForm.post(route('admin.users.balance', balanceModalUser.value.id), {
        preserveScroll: true,
        onSuccess: () => (balanceModalUser.value = null),
    });
};

const toggleUserRole = (user) => {
    const newRole = user.role === 'admin' ? 'student' : 'admin';
    router.put(route('admin.users.update', user.id), {
        name: user.name,
        role: newRole,
        balance: user.balance,
    }, { preserveScroll: true });
};

const deleteUser = (user) => {
    if (confirm(`Xác nhận xóa tài khoản "${user.name}"?`)) {
        router.delete(route('admin.users.destroy', user.id), { preserveScroll: true });
    }
};

// =========================================================================
// 4. DUYỆT GIAO DỊCH NẠP TIỀN
// =========================================================================
const txFilter = ref('pending'); // all | pending | approved | rejected
const txSearch = ref('');

const filteredAdminTransactions = computed(() => {
    return props.transactions.filter(tx => {
        const matchStatus = txFilter.value === 'all' || tx.status === txFilter.value;
        const q = txSearch.value.trim().toLowerCase();
        const matchSearch = !q ||
            tx.code.toLowerCase().includes(q) ||
            (tx.user?.name && tx.user.name.toLowerCase().includes(q)) ||
            (tx.user?.email && tx.user.email.toLowerCase().includes(q));
        return matchStatus && matchSearch;
    });
});

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const approveTx = (tx) => {
    if (confirm(`Xác nhận duyệt và cộng ${Number(tx.amount).toLocaleString('vi-VN')} đ vào ví học viên "${tx.user?.name}"?`)) {
        router.post(route('admin.transactions.approve', tx.id), {}, { preserveScroll: true });
    }
};

const rejectTx = (tx) => {
    if (confirm(`Từ chối giao dịch ${tx.code}?`)) {
        router.post(route('admin.transactions.reject', tx.id), {}, { preserveScroll: true });
    }
};

// =========================================================================
// 5. QUẢN LÝ CHUYÊN NGÀNH
// =========================================================================
const categoryForm = useForm({ name: '' });
const addCategory = () => {
    categoryForm.post(route('admin.categories.store'), {
        onSuccess: () => categoryForm.reset(),
    });
};
</script>

<template>
    <Head title="Hệ thống Quản trị Admin - Computer Academy" />

    <AppLayout>
        <!-- TOP ADMIN HEADER -->
        <section class="bg-gradient-to-r from-slate-900 via-[#0e162e] to-slate-900 border-b border-slate-800 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/15 border border-rose-500/30 text-rose-400 text-xs font-bold uppercase">
                            🛡️ Administrator Control Panel
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white mt-2">
                            Trung Tâm Quản Trị Hệ Thống
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-400 mt-1">
                            Quản lý toàn diện khóa học, bài học đa phương tiện, tài khoản học viên và giao dịch tài chính.
                        </p>
                    </div>
                    <button
                        @click="openCreateCourse"
                        class="px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-600/25 transition cursor-pointer flex items-center gap-2 shrink-0"
                    >
                        <span>+</span>
                        <span>Tạo Khóa Học Mới</span>
                    </button>
                </div>

                <!-- 5 THẺ THỐNG KÊ HỆ THỐNG -->
                <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mt-8">
                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
                        <span class="text-xs text-slate-400">Tổng doanh thu nạp</span>
                        <div class="text-xl font-extrabold text-emerald-400 mt-1">
                            {{ Number(stats.total_revenue).toLocaleString('vi-VN') }} đ
                        </div>
                    </div>
                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
                        <span class="text-xs text-slate-400">Lệnh nạp chờ duyệt</span>
                        <div class="text-xl font-extrabold text-amber-400 mt-1">
                            {{ stats.pending_deposits }} yêu cầu
                        </div>
                    </div>
                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
                        <span class="text-xs text-slate-400">Tổng khóa học</span>
                        <div class="text-xl font-extrabold text-blue-400 mt-1">
                            {{ stats.total_courses }} khóa học
                        </div>
                    </div>
                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
                        <span class="text-xs text-slate-400">Tổng tài khoản</span>
                        <div class="text-xl font-extrabold text-white mt-1">
                            {{ stats.total_users }} thành viên
                        </div>
                    </div>
                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
                        <span class="text-xs text-slate-400">Lượt đăng ký học</span>
                        <div class="text-xl font-extrabold text-indigo-400 mt-1">
                            {{ stats.total_enrollments }} lượt
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- MAIN ADMIN WORKSPACE -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Thanh điều hướng Phân hệ Admin -->
            <div class="flex flex-wrap gap-2 mb-8 border-b border-slate-800 pb-4">
                <button
                    @click="activeTab = 'courses'"
                    class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'courses' ? 'bg-blue-600 text-white' : 'bg-slate-900 text-slate-400 hover:text-white'"
                >
                    <span>📚 Quản lý Khóa học & Bài giảng</span>
                    <span class="px-2 py-0.5 text-[11px] rounded-full bg-slate-800 text-slate-200">
                        {{ courses.length }}
                    </span>
                </button>
                <button
                    @click="activeTab = 'deposits'"
                    class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'deposits' ? 'bg-blue-600 text-white' : 'bg-slate-900 text-slate-400 hover:text-white'"
                >
                    <span>💳 Duyệt Nạp tiền & Giao dịch</span>
                    <span v-if="stats.pending_deposits > 0" class="px-2 py-0.5 text-[11px] rounded-full bg-amber-500 text-slate-950 font-extrabold">
                        {{ stats.pending_deposits }}
                    </span>
                </button>
                <button
                    @click="activeTab = 'users'"
                    class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'users' ? 'bg-blue-600 text-white' : 'bg-slate-900 text-slate-400 hover:text-white'"
                >
                    <span>👥 Quản lý Người dùng & Ví</span>
                    <span class="px-2 py-0.5 text-[11px] rounded-full bg-slate-800 text-slate-200">
                        {{ users.length }}
                    </span>
                </button>
                <button
                    @click="activeTab = 'categories'"
                    class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'categories' ? 'bg-blue-600 text-white' : 'bg-slate-900 text-slate-400 hover:text-white'"
                >
                    <span>🏷️ Chuyên ngành Đào tạo</span>
                    <span class="px-2 py-0.5 text-[11px] rounded-full bg-slate-800 text-slate-200">
                        {{ categories.length }}
                    </span>
                </button>
            </div>

            <!-- ================= TAB 1: QUẢN LÝ KHÓA HỌC & BÀI GIẢNG ================= -->
            <div v-if="activeTab === 'courses'" class="space-y-6">
                <div v-for="course in courses" :key="course.id" class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-sm">
                    <!-- Hàng thông tin chính của Khóa học -->
                    <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="flex items-start sm:items-center gap-4 min-w-0">
                            <img :src="course.thumbnail" class="w-24 h-16 object-cover rounded-xl bg-slate-800 shrink-0 border border-slate-800" />
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                    <span class="text-blue-400 font-bold">{{ course.category?.name }}</span>
                                    <span class="text-slate-600">•</span>
                                    <span class="font-bold" :class="Number(course.price) === 0 ? 'text-emerald-400' : 'text-amber-400'">
                                        {{ Number(course.price) === 0 ? 'Miễn phí' : Number(course.price).toLocaleString('vi-VN') + ' đ' }}
                                    </span>
                                    <span class="text-slate-600">•</span>
                                    <span class="text-slate-400">{{ course.enrollments_count }} học viên</span>
                                    <span class="text-slate-600">•</span>
                                    <span class="text-slate-500 font-medium">Cấp độ: {{ course.level }}</span>
                                </div>
                                <h3 class="text-base sm:text-lg font-bold text-white mt-1 truncate">
                                    {{ course.title }}
                                </h3>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            <button
                                @click="selectedCourseForCurriculum = selectedCourseForCurriculum === course.id ? null : course.id"
                                class="px-3.5 py-2 border text-xs font-bold rounded-xl transition cursor-pointer flex items-center gap-1.5"
                                :class="selectedCourseForCurriculum === course.id
                                    ? 'bg-indigo-600 text-white border-indigo-500'
                                    : 'bg-indigo-600/20 hover:bg-indigo-600/30 border-indigo-500/30 text-indigo-300'"
                            >
                                <span>📖</span>
                                <span>Quản lý Chương & Bài giảng ({{ course.sections?.length || 0 }} chương)</span>
                            </button>
                            <button
                                @click="openEditCourse(course)"
                                class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl transition cursor-pointer"
                            >
                                ✏️ Sửa khóa học
                            </button>
                            <button
                                @click="deleteCourse(course)"
                                class="px-3.5 py-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs font-semibold rounded-xl transition cursor-pointer"
                            >
                                🗑️ Xóa
                            </button>
                        </div>
                    </div>

                    <!-- Khung Quản lý Chương & Bài giảng (Mở rộng ngay bên dưới khóa học) -->
                    <div v-if="selectedCourseForCurriculum === course.id" class="border-t border-slate-800 bg-slate-950/70 p-6 space-y-6">
                        <!-- Form thêm Chương mới -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-slate-900/60 p-4 rounded-xl border border-slate-800">
                            <div>
                                <h4 class="text-sm font-bold text-white">Thêm Chương mới vào khóa học</h4>
                                <p class="text-xs text-slate-400">Tổ chức lộ trình học theo từng chương hoặc mô-đun kiến thức.</p>
                            </div>
                            <form @submit.prevent="addSection(course)" class="flex gap-2 w-full sm:w-auto sm:min-w-[360px]">
                                <input
                                    v-model="sectionForm.title"
                                    type="text"
                                    required
                                    placeholder="Nhập tên chương (VD: Chương 2: Thiết kế Hạ tầng)..."
                                    class="flex-1 px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-blue-500"
                                />
                                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shrink-0 cursor-pointer">
                                    + Thêm Chương
                                </button>
                            </form>
                        </div>

                        <!-- Danh sách các Chương -->
                        <div class="space-y-4">
                            <div v-for="section in course.sections" :key="section.id" class="bg-slate-900 border border-slate-800 rounded-xl p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 mb-3 border-b border-slate-800">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                                        <h4 class="font-bold text-sm text-blue-400">{{ section.title }}</h4>
                                        <span class="text-xs text-slate-500">({{ section.lessons?.length || 0 }} bài học)</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="openAddLessonForm(section.id)"
                                            class="text-xs px-3 py-1.5 rounded-lg font-semibold transition cursor-pointer flex items-center gap-1.5"
                                            :class="lessonForm.section_id === section.id
                                                ? 'bg-emerald-600 text-white'
                                                : 'bg-emerald-600/20 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-600/30'"
                                        >
                                            <span>+</span>
                                            <span>{{ lessonForm.section_id === section.id ? 'Đang mở form' : 'Thêm bài học đa định dạng' }}</span>
                                        </button>
                                        <button @click="deleteSection(section.id)" class="text-xs text-rose-400 hover:underline px-2 cursor-pointer">
                                            Xóa chương
                                        </button>
                                    </div>
                                </div>

                                <!-- Form thêm Bài học mới (Hỗ trợ Video + Ảnh + Tài liệu) -->
                                <form
                                    v-if="lessonForm.section_id === section.id"
                                    @submit.prevent="addLesson(section.id)"
                                    class="bg-slate-950 border border-slate-800 rounded-xl p-5 mb-5 space-y-4"
                                >
                                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-white">Thêm bài học mới</span>
                                            <span class="text-xs text-slate-400 font-normal">(Hỗ trợ tích hợp cả Video, Hình ảnh minh họa & Tài liệu đính kèm)</span>
                                        </div>
                                        <button type="button" @click="lessonForm.section_id = null" class="text-xs text-slate-400 hover:text-white">✕ Đóng</button>
                                    </div>

                                    <!-- Dòng 1: Tiêu đề + Thời lượng -->
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div class="sm:col-span-2">
                                            <label class="block text-[11px] font-semibold text-slate-300 mb-1">Tiêu đề bài học <span class="text-rose-400">*</span></label>
                                            <input v-model="lessonForm.title" type="text" required placeholder="VD: Bài 1: Tổng quan Kiến trúc Máy tính & Phân loại" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-xs text-white" />
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-300 mb-1">Thời lượng học (phút) <span class="text-rose-400">*</span></label>
                                            <input v-model="lessonForm.duration" type="number" required min="1" placeholder="Thời lượng (phút)" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-xs text-white" />
                                        </div>
                                    </div>

                                    <!-- Dòng 2: Video URL -->
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-300 mb-1 flex items-center justify-between">
                                            <span class="flex items-center gap-1.5">
                                                <span>🎥 Link Video bài giảng</span>
                                                <span class="text-[10px] text-slate-500 font-normal">(Tùy chọn: YouTube Embed, Vimeo, MP4 direct link)</span>
                                            </span>
                                        </label>
                                        <input
                                            v-model="lessonForm.video_url"
                                            type="text"
                                            placeholder="VD: https://www.youtube.com/embed/ExxFxD4OSZ0 (Để trống nếu không có video)"
                                            class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-xs text-white"
                                        />
                                    </div>

                                    <!-- Dòng 3: Hình ảnh minh họa / Sơ đồ kiến trúc -->
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-300 mb-1 flex items-center justify-between">
                                            <span class="flex items-center gap-1.5">
                                                <span>🖼️ Link Hình ảnh minh họa / Sơ đồ kiến trúc</span>
                                                <span class="text-[10px] text-slate-500 font-normal">(Tùy chọn: URL ảnh sơ đồ, biểu đồ, infographic)</span>
                                            </span>
                                            <span v-if="lessonForm.image_url" class="text-[10px] text-emerald-400 font-medium">✓ Đã nhập link ảnh</span>
                                        </label>
                                        <input
                                            v-model="lessonForm.image_url"
                                            type="text"
                                            placeholder="VD: https://images.unsplash.com/... hoặc link ảnh sơ đồ kiến trúc hệ thống"
                                            class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-xs text-white"
                                        />
                                        <!-- Preview ảnh nếu có URL -->
                                        <div v-if="lessonForm.image_url" class="mt-2 p-2 bg-slate-900 rounded-lg border border-slate-800 max-w-sm">
                                            <p class="text-[10px] text-slate-400 mb-1">Xem trước hình ảnh:</p>
                                            <img :src="lessonForm.image_url" class="max-h-32 rounded object-contain mx-auto" @error="$event.target.style.display='none'" />
                                        </div>
                                    </div>

                                    <!-- Dòng 4: File tài liệu đính kèm (PDF, Word, Slide, Drive...) -->
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-300 mb-1 flex items-center gap-1.5">
                                            <span>📎 Link Tệp tin / Tài liệu đính kèm tải về</span>
                                            <span class="text-[10px] text-slate-500 font-normal">(Tùy chọn: Link file PDF, Slide bài giảng, Google Drive, GitHub...)</span>
                                        </label>
                                        <input
                                            v-model="lessonForm.attachment_url"
                                            type="text"
                                            placeholder="VD: https://drive.google.com/... hoặc https://example.com/tai-lieu-giao-trinh.pdf"
                                            class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-lg text-xs text-white"
                                        />
                                    </div>

                                    <!-- Dòng 5: Nội dung bài viết / Lý thuyết chi tiết (CKEditor) -->
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-300 mb-1 flex items-center justify-between">
                                            <span class="flex items-center gap-1.5">
                                                <span>📝 Nội dung bài học & Lý thuyết chuyên sâu (CKEditor)</span>
                                                <span class="text-[10px] text-slate-500 font-normal">(Soạn thảo lý thuyết, bảng biểu, code block...)</span>
                                            </span>
                                        </label>
                                        <Ckeditor
                                            v-model="lessonForm.content"
                                            placeholder="Soạn thảo nội dung bài giảng, lý thuyết, tài liệu hướng dẫn..."
                                            min-height="220px"
                                        />
                                    </div>

                                    <!-- Dòng 6: PHẦN BÀI TẬP RIÊNG BIỆT (KHÔNG DÙNG CKEDITOR) -->
                                    <div class="p-4 rounded-xl border border-blue-500/30 bg-blue-950/15 space-y-3">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-slate-800">
                                            <div class="flex items-center gap-2">
                                                <span class="text-base">🎯</span>
                                                <div>
                                                    <h4 class="text-xs font-bold text-blue-400 uppercase tracking-wider">
                                                        Phần Bài Tập & Câu Hỏi Củng Cố Riêng Biệt
                                                    </h4>
                                                    <p class="text-[10px] text-slate-400">
                                                        Quản lý câu hỏi trắc nghiệm hoặc bài tập thực hành độc lập, không cần nhập lẫn vào CKEditor.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <button
                                                    type="button"
                                                    @click="addQuizQuestion(lessonForm)"
                                                    class="px-2.5 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-xs font-semibold flex items-center gap-1 cursor-pointer transition shadow-sm"
                                                >
                                                    <span>+ Trắc nghiệm</span>
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="addPracticalTask(lessonForm)"
                                                    class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-semibold flex items-center gap-1 cursor-pointer transition shadow-sm"
                                                >
                                                    <span>+ Thực hành</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Danh sách câu hỏi đã tạo -->
                                        <div v-if="lessonForm.exercises && lessonForm.exercises.length > 0" class="space-y-3 pt-1">
                                            <div
                                                v-for="(ex, exIdx) in lessonForm.exercises"
                                                :key="ex.id || exIdx"
                                                class="p-3.5 rounded-xl border border-slate-800 bg-slate-900/90 space-y-2.5"
                                            >
                                                <div class="flex items-center justify-between">
                                                    <span
                                                        class="text-[11px] font-bold px-2 py-0.5 rounded"
                                                        :class="ex.type === 'quiz' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30'"
                                                    >
                                                        {{ ex.type === 'quiz' ? `Câu ${exIdx + 1}: Trắc nghiệm` : `Bài ${exIdx + 1}: Thực hành / Tình huống` }}
                                                    </span>
                                                    <button
                                                        type="button"
                                                        @click="removeExercise(lessonForm, exIdx)"
                                                        class="text-xs text-rose-400 hover:text-rose-300 font-semibold cursor-pointer"
                                                    >
                                                        ✕ Xóa
                                                    </button>
                                                </div>

                                                <!-- Form trắc nghiệm -->
                                                <template v-if="ex.type === 'quiz'">
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Nội dung câu hỏi:</label>
                                                        <input
                                                            v-model="ex.question"
                                                            type="text"
                                                            placeholder="VD: Thiết bị nào sau đây thuộc công đoạn Input trong mô hình IPOS?"
                                                            class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white"
                                                        />
                                                    </div>

                                                    <div class="space-y-1.5">
                                                        <label class="block text-[11px] font-semibold text-slate-400">Các phương án (Tích tròn chọn đáp án đúng):</label>
                                                        <div
                                                            v-for="(opt, optIdx) in ex.options"
                                                            :key="optIdx"
                                                            class="flex items-center gap-2"
                                                        >
                                                            <input
                                                                type="radio"
                                                                :name="`add_correct_${ex.id || exIdx}`"
                                                                :value="optIdx"
                                                                v-model="ex.correct_index"
                                                                class="text-emerald-500 focus:ring-0 cursor-pointer"
                                                                title="Chọn làm đáp án đúng"
                                                            />
                                                            <span class="text-xs font-bold text-slate-400 w-4">
                                                                {{ String.fromCharCode(65 + optIdx) }}.
                                                            </span>
                                                            <input
                                                                v-model="ex.options[optIdx]"
                                                                type="text"
                                                                :placeholder="`Phương án ${String.fromCharCode(65 + optIdx)}...`"
                                                                class="flex-1 px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white"
                                                            />
                                                            <button
                                                                v-if="ex.options.length > 2"
                                                                type="button"
                                                                @click="removeOption(ex, optIdx)"
                                                                class="text-slate-500 hover:text-rose-400 text-xs px-1"
                                                            >
                                                                ✕
                                                            </button>
                                                        </div>
                                                        <button
                                                            v-if="ex.options.length < 6"
                                                            type="button"
                                                            @click="addOption(ex)"
                                                            class="text-[11px] text-blue-400 hover:underline pt-0.5"
                                                        >
                                                            + Thêm phương án khác
                                                        </button>
                                                    </div>

                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Giải thích đáp án / Gợi ý kiến thức (Tùy chọn):</label>
                                                        <input
                                                            v-model="ex.explanation"
                                                            type="text"
                                                            placeholder="VD: Bàn phím là thiết bị Input nhận tín hiệu gõ phím từ người dùng..."
                                                            class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white"
                                                        />
                                                    </div>
                                                </template>

                                                <!-- Form thực hành -->
                                                <template v-else>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Tiêu đề bài thực hành / Tình huống:</label>
                                                        <input
                                                            v-model="ex.title"
                                                            type="text"
                                                            placeholder="VD: Lắp ráp bộ PC đồ họa 15 triệu đồng"
                                                            class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white"
                                                        />
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Mô tả đề bài:</label>
                                                        <textarea
                                                            v-model="ex.description"
                                                            rows="2"
                                                            placeholder="Nội dung đề bài hoặc tình huống thực tế..."
                                                            class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white"
                                                        ></textarea>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Các bước yêu cầu (Mỗi dòng một bước):</label>
                                                        <textarea
                                                            v-model="ex.requirements"
                                                            rows="2"
                                                            placeholder="- Bước 1: Chuẩn bị linh kiện...&#10;- Bước 2: Lắp CPU và RAM..."
                                                            class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white"
                                                        ></textarea>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Hướng dẫn giải / Đáp án mẫu:</label>
                                                        <textarea
                                                            v-model="ex.solution"
                                                            rows="2"
                                                            placeholder="Gợi ý cấu hình tối ưu và lưu ý kỹ thuật..."
                                                            class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white"
                                                        ></textarea>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                        <div v-else class="text-center py-3 text-xs text-slate-500">
                                            Bài học này chưa có bài tập riêng biệt. Bấm <strong>+ Trắc nghiệm</strong> hoặc <strong>+ Thực hành</strong> để thêm.
                                        </div>
                                    </div>

                                    <!-- Dòng 6: Xem thử miễn phí + Nút Lưu -->
                                    <div class="flex items-center justify-between pt-2 border-t border-slate-800">
                                        <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                                            <input type="checkbox" v-model="lessonForm.is_free_preview" class="rounded bg-slate-900 border-slate-700 text-blue-600 focus:ring-0" />
                                            <span>Cho phép học thử miễn phí (không bắt buộc mua khóa học)</span>
                                        </label>
                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                @click="lessonForm.section_id = null"
                                                class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg cursor-pointer"
                                            >
                                                Hủy
                                            </button>
                                            <button
                                                type="submit"
                                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-lg cursor-pointer flex items-center gap-1.5"
                                            >
                                                <span>✓</span>
                                                <span>Lưu bài học</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <!-- Danh sách Bài học trong Chương -->
                                <div v-if="!section.lessons || section.lessons.length === 0" class="text-xs text-slate-500 italic py-2">
                                    Chương này chưa có bài học nào. Hãy bấm nút "+ Thêm bài học đa định dạng" ở trên.
                                </div>

                                <ul v-else class="space-y-2">
                                    <li v-for="lesson in section.lessons" :key="lesson.id" class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs bg-slate-950/60 hover:bg-slate-950 border border-slate-800/60 px-3.5 py-3 rounded-xl transition">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <!-- Huy hiệu định dạng bài học -->
                                            <div class="flex items-center gap-1 shrink-0">
                                                <span v-if="lesson.video_url" class="p-1 rounded bg-blue-500/10 text-blue-400 text-[11px]" title="Có Video">🎥</span>
                                                <span v-if="lesson.image_url" class="p-1 rounded bg-amber-500/10 text-amber-400 text-[11px]" title="Có Hình ảnh">🖼️</span>
                                                <span v-if="lesson.attachment_url" class="p-1 rounded bg-emerald-500/10 text-emerald-400 text-[11px]" title="Có Tài liệu đính kèm">📎</span>
                                                <span v-if="lesson.content" class="p-1 rounded bg-violet-500/10 text-violet-400 text-[11px]" title="Có Nội dung bài viết">📝</span>
                                            </div>

                                            <span class="text-slate-200 font-semibold truncate">{{ lesson.title }}</span>
                                            <span class="text-slate-500 shrink-0">({{ lesson.duration }} phút)</span>
                                            <span v-if="lesson.is_free_preview" class="text-[10px] bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 px-1.5 py-0.5 rounded font-bold shrink-0">Học thử</span>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0 self-end sm:self-auto">
                                            <button
                                                @click="openEditLesson(lesson)"
                                                class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg transition cursor-pointer flex items-center gap-1"
                                            >
                                                <span>✏️</span>
                                                <span>Sửa</span>
                                            </button>
                                            <button
                                                @click="deleteLesson(lesson.id)"
                                                class="px-2.5 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs font-semibold rounded-lg transition cursor-pointer"
                                            >
                                                Xóa
                                            </button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 2: DUYỆT NẠP TIỀN & GIAO DỊCH ================= -->
            <div v-if="activeTab === 'deposits'" class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-5">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-white">Quản lý Yêu cầu Nạp tiền & Giao dịch toàn hệ thống</h2>
                        <p class="text-xs text-slate-400">Kiểm tra thông tin sao kê ngân hàng và bấm duyệt để tự động cộng tiền vào ví học viên.</p>
                    </div>

                    <!-- Tìm kiếm và bộ lọc nhanh -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        <div class="relative">
                            <input
                                v-model="txSearch"
                                type="text"
                                placeholder="Tìm mã GD, tên, email..."
                                class="pl-8 pr-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500"
                            />
                            <span class="absolute left-2.5 top-2 text-slate-500 text-xs">🔍</span>
                        </div>

                        <div class="flex rounded-xl overflow-hidden border border-slate-800 text-xs font-semibold">
                            <button
                                type="button"
                                @click="txFilter = 'pending'"
                                class="px-3 py-1.5 transition cursor-pointer"
                                :class="txFilter === 'pending' ? 'bg-amber-600 text-white' : 'bg-slate-950 text-slate-400 hover:text-white'"
                            >
                                Chờ duyệt ({{ transactions.filter(t => t.status === 'pending').length }})
                            </button>
                            <button
                                type="button"
                                @click="txFilter = 'approved'"
                                class="px-3 py-1.5 transition cursor-pointer"
                                :class="txFilter === 'approved' ? 'bg-emerald-600 text-white' : 'bg-slate-950 text-slate-400 hover:text-white'"
                            >
                                Đã duyệt
                            </button>
                            <button
                                type="button"
                                @click="txFilter = 'all'"
                                class="px-3 py-1.5 transition cursor-pointer"
                                :class="txFilter === 'all' ? 'bg-blue-600 text-white' : 'bg-slate-950 text-slate-400 hover:text-white'"
                            >
                                Tất cả ({{ transactions.length }})
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="filteredAdminTransactions.length === 0" class="text-center py-12 text-slate-400 text-sm">
                    Không có giao dịch nào phù hợp với bộ lọc hiện tại.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 uppercase text-[11px]">
                                <th class="py-3 px-3">Mã GD</th>
                                <th class="py-3 px-3">Học viên</th>
                                <th class="py-3 px-3">Số tiền</th>
                                <th class="py-3 px-3">Nội dung / Thời gian</th>
                                <th class="py-3 px-3">Trạng thái</th>
                                <th class="py-3 px-3 text-right">Thao tác duyệt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/70">
                            <tr v-for="tx in filteredAdminTransactions" :key="tx.id" class="hover:bg-slate-800/30">
                                <td class="py-3.5 px-3">
                                    <div class="font-mono font-bold text-blue-400">{{ tx.code }}</div>
                                    <span class="text-[10px] text-slate-500 uppercase">{{ tx.type }}</span>
                                </td>
                                <td class="py-3.5 px-3">
                                    <div class="font-bold text-white">{{ tx.user?.name }}</div>
                                    <div class="text-xs text-slate-400">{{ tx.user?.email }}</div>
                                </td>
                                <td class="py-3.5 px-3 font-extrabold text-emerald-400 text-sm">
                                    {{ Number(tx.amount).toLocaleString('vi-VN') }} đ
                                </td>
                                <td class="py-3.5 px-3">
                                    <div class="text-slate-200">{{ tx.note }}</div>
                                    <div class="text-[11px] text-slate-500">
                                        {{ formatDate(tx.created_at) }} • {{ tx.payment_method }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-3">
                                    <span
                                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold inline-block"
                                        :class="{
                                            'bg-amber-500/20 text-amber-400 border border-amber-500/30': tx.status === 'pending',
                                            'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30': tx.status === 'approved',
                                            'bg-rose-500/20 text-rose-400 border border-rose-500/30': tx.status === 'rejected',
                                        }"
                                    >
                                        {{ tx.status === 'pending' ? '⏳ Chờ duyệt' : (tx.status === 'approved' ? '✓ Đã duyệt' : '✕ Từ chối') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-right space-x-2">
                                    <template v-if="tx.status === 'pending'">
                                        <button
                                            @click="approveTx(tx)"
                                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-lg shadow transition cursor-pointer"
                                            title="Xác nhận đã nhận được tiền và cộng vào ví học viên"
                                        >
                                            ✓ Duyệt & Cộng tiền
                                        </button>
                                        <button
                                            @click="rejectTx(tx)"
                                            class="px-3 py-1.5 bg-rose-500/15 hover:bg-rose-500/25 text-rose-400 text-xs font-bold rounded-lg transition cursor-pointer"
                                        >
                                            ✕ Từ chối
                                        </button>
                                    </template>
                                    <span v-else class="text-xs text-slate-500">Đã xử lý</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ================= TAB 3: QUẢN LÝ NGƯỜI DÙNG & VÍ ================= -->
            <div v-if="activeTab === 'users'" class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-white">Danh sách Tài khoản & Điều chỉnh Số dư Ví</h2>
                        <p class="text-xs text-slate-400">Xem danh sách thành viên, cấp quyền quản trị và nạp/trừ tiền trực tiếp vào tài khoản.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 uppercase text-[11px]">
                                <th class="py-3 px-3">ID</th>
                                <th class="py-3 px-3">Họ tên & Email</th>
                                <th class="py-3 px-3">Vai trò</th>
                                <th class="py-3 px-3">Số dư Ví</th>
                                <th class="py-3 px-3">Khóa đang học</th>
                                <th class="py-3 px-3 text-right">Quản lý</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/70">
                            <tr v-for="u in users" :key="u.id" class="hover:bg-slate-800/30">
                                <td class="py-3.5 px-3 text-slate-400 font-mono">#{{ u.id }}</td>
                                <td class="py-3.5 px-3">
                                    <div class="font-bold text-white">{{ u.name }}</div>
                                    <div class="text-xs text-slate-400">{{ u.email }}</div>
                                </td>
                                <td class="py-3.5 px-3">
                                    <span
                                        class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase"
                                        :class="u.role === 'admin' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-blue-500/15 text-blue-400 border border-blue-500/30'"
                                    >
                                        {{ u.role === 'admin' ? 'Quản trị viên' : 'Học viên' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 font-extrabold text-emerald-400">
                                    {{ Number(u.balance).toLocaleString('vi-VN') }} đ
                                </td>
                                <td class="py-3.5 px-3 text-slate-300">{{ u.enrollments_count }} khóa</td>
                                <td class="py-3.5 px-3 text-right space-x-2">
                                    <button
                                        @click="openBalanceModal(u)"
                                        class="px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/30 text-emerald-300 text-xs font-bold rounded-lg cursor-pointer"
                                    >
                                        + Cộng/Trừ tiền
                                    </button>
                                    <button
                                        @click="toggleUserRole(u)"
                                        class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg cursor-pointer"
                                    >
                                        Đổi quyền
                                    </button>
                                    <button
                                        v-if="u.id !== $page.props.auth.user.id"
                                        @click="deleteUser(u)"
                                        class="px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs font-semibold rounded-lg cursor-pointer"
                                    >
                                        Xóa
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ================= TAB 4: QUẢN LÝ CHUYÊN NGÀNH ================= -->
            <div v-if="activeTab === 'categories'" class="grid grid-cols-1 md:grid-cols-12 gap-8">
                <div class="md:col-span-5 bg-slate-900/80 border border-slate-800 rounded-2xl p-6">
                    <h3 class="text-base font-bold text-white mb-2">Thêm Chuyên ngành đào tạo mới</h3>
                    <p class="text-xs text-slate-400 mb-4">Tạo nhóm danh mục để phân loại các khóa học trực quan.</p>
                    <form @submit.prevent="addCategory" class="space-y-4">
                        <input
                            v-model="categoryForm.name"
                            type="text"
                            required
                            placeholder="VD: Trí tuệ nhân tạo, Điện toán Đám mây..."
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500"
                        />
                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold rounded-xl cursor-pointer">
                            + Tạo chuyên ngành
                        </button>
                    </form>
                </div>

                <div class="md:col-span-7 bg-slate-900/80 border border-slate-800 rounded-2xl p-6">
                    <h3 class="text-base font-bold text-white mb-4">Danh sách Chuyên ngành hiện tại</h3>
                    <ul class="divide-y divide-slate-800">
                        <li v-for="cat in categories" :key="cat.id" class="py-3.5 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-white">{{ cat.name }}</span>
                                <span class="text-xs text-slate-400 ml-2">({{ cat.courses_count }} khóa học)</span>
                            </div>
                            <button
                                @click="router.delete(route('admin.categories.destroy', cat.id))"
                                class="text-xs text-rose-400 hover:underline cursor-pointer"
                            >
                                Xóa
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- =========================================================================
             MODAL 1: TẠO / CẬP NHẬT KHÓA HỌC
        ========================================================================== -->
        <div v-if="showCourseModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-xl w-full p-6 space-y-5 shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-lg font-bold text-white">
                        {{ editingCourse ? 'Cập nhật Khóa học' : 'Tạo Khóa học Mới' }}
                    </h3>
                    <button @click="showCourseModal = false" class="text-slate-400 hover:text-white text-base cursor-pointer">✕</button>
                </div>

                <form @submit.prevent="submitCourse" class="space-y-4 text-xs sm:text-sm">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Tên khóa học <span class="text-rose-400">*</span></label>
                        <input v-model="courseForm.title" type="text" required placeholder="VD: Kiến trúc Máy tính & Quản trị Mạng" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Chuyên ngành <span class="text-rose-400">*</span></label>
                            <select v-model="courseForm.category_id" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white">
                                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Cấp độ</label>
                            <select v-model="courseForm.level" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white">
                                <option value="beginner">Cơ bản (Beginner)</option>
                                <option value="intermediate">Trung cấp (Intermediate)</option>
                                <option value="advanced">Chuyên sâu (Advanced)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Học phí (VNĐ - Điền 0 nếu Miễn phí)</label>
                            <input v-model="courseForm.price" type="number" min="0" step="1000" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-emerald-400 font-bold" />
                        </div>
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Link ảnh Thumbnail</label>
                            <input v-model="courseForm.thumbnail" type="text" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Mô tả tóm tắt</label>
                        <textarea v-model="courseForm.description" rows="3" placeholder="Tóm tắt mục tiêu và kiến thức đạt được sau khóa học..." class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white"></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                            <input type="checkbox" v-model="courseForm.is_published" class="rounded bg-slate-950 border-slate-800 text-blue-600 focus:ring-0" />
                            <span>Công khai khóa học lên trang chủ</span>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-800">
                        <button type="button" @click="showCourseModal = false" class="px-4 py-2.5 bg-slate-800 rounded-xl text-slate-300 cursor-pointer">Hủy</button>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl cursor-pointer">
                            {{ editingCourse ? 'Lưu cập nhật' : 'Tạo khóa học' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- =========================================================================
             MODAL 2: CHỈNH SỬA BÀI HỌC ĐA ĐỊNH DẠNG (EDIT LESSON MODAL)
        ========================================================================== -->
        <div v-if="showEditLessonModal" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-3xl w-full p-6 space-y-4 shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div>
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <span>✏️</span>
                            <span>Chỉnh Sửa Bài Học</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Tùy biến video, hình ảnh minh họa, tài liệu đính kèm và nội dung bài viết bằng CKEditor.</p>
                    </div>
                    <button @click="showEditLessonModal = false" class="text-slate-400 hover:text-white text-base cursor-pointer">✕</button>
                </div>

                <form @submit.prevent="submitEditLesson" class="space-y-4 text-xs sm:text-sm">
                    <!-- Tiêu đề + Thời lượng -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-slate-300 font-semibold mb-1">Tiêu đề bài học <span class="text-rose-400">*</span></label>
                            <input v-model="editLessonForm.title" type="text" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white" />
                        </div>
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Thời lượng (phút) <span class="text-rose-400">*</span></label>
                            <input v-model="editLessonForm.duration" type="number" min="1" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white" />
                        </div>
                    </div>

                    <!-- Video bài giảng -->
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1 flex items-center gap-1.5">
                            <span>🎥 Link Video bài giảng</span>
                            <span class="text-[11px] text-slate-500 font-normal">(YouTube Embed, Vimeo, MP4 direct)</span>
                        </label>
                        <input
                            v-model="editLessonForm.video_url"
                            type="text"
                            placeholder="VD: https://www.youtube.com/embed/ExxFxD4OSZ0"
                            class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white"
                        />
                    </div>

                    <!-- Hình ảnh minh họa / Sơ đồ kiến trúc -->
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <span>🖼️ Link Hình ảnh minh họa / Sơ đồ kiến trúc</span>
                                <span class="text-[11px] text-slate-500 font-normal">(URL ảnh trực quan)</span>
                            </span>
                            <span v-if="editLessonForm.image_url" class="text-[11px] text-emerald-400 font-medium">✓ Đã có ảnh</span>
                        </label>
                        <input
                            v-model="editLessonForm.image_url"
                            type="text"
                            placeholder="VD: https://images.unsplash.com/... hoặc link ảnh sơ đồ"
                            class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white"
                        />
                        <!-- Preview ảnh -->
                        <div v-if="editLessonForm.image_url" class="mt-2 p-2 bg-slate-950 rounded-xl border border-slate-800 max-w-xs">
                            <p class="text-[10px] text-slate-400 mb-1">Xem trước hình ảnh:</p>
                            <img :src="editLessonForm.image_url" class="max-h-36 rounded object-contain mx-auto" @error="$event.target.style.display='none'" />
                        </div>
                    </div>

                    <!-- File tài liệu đính kèm -->
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1 flex items-center gap-1.5">
                            <span>📎 Link Tài liệu / Tệp tin đính kèm tải về</span>
                            <span class="text-[11px] text-slate-500 font-normal">(PDF, Slide, Google Drive, Zip...)</span>
                        </label>
                        <input
                            v-model="editLessonForm.attachment_url"
                            type="text"
                            placeholder="VD: https://drive.google.com/... hoặc link file tài liệu"
                            class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white"
                        />
                    </div>

                    <!-- Nội dung bài viết / Lý thuyết chi tiết (CKEditor) -->
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1 flex items-center justify-between">
                            <span>📝 Nội dung bài học & Lý thuyết chuyên sâu (CKEditor)</span>
                            <span class="text-[11px] text-slate-500 font-normal">(Soạn thảo lý thuyết, bảng biểu, code block...)</span>
                        </label>
                        <Ckeditor
                            v-model="editLessonForm.content"
                            placeholder="Chỉnh sửa nội dung bài giảng chi tiết, bảng so sánh..."
                            min-height="260px"
                        />
                    </div>

                    <!-- PHẦN BÀI TẬP RIÊNG BIỆT (KHÔNG DÙNG CKEDITOR) -->
                    <div class="p-4 rounded-xl border border-blue-500/30 bg-blue-950/20 space-y-3.5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2.5 border-b border-slate-800">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">🎯</span>
                                <div>
                                    <h4 class="text-sm font-bold text-blue-400 uppercase tracking-wider">
                                        Phần Bài Tập & Câu Hỏi Củng Cố Riêng Biệt
                                    </h4>
                                    <p class="text-xs text-slate-400">
                                        Thêm câu hỏi trắc nghiệm hoặc bài tập thực hành độc lập, không cần nhập lẫn vào CKEditor.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button
                                    type="button"
                                    @click="addQuizQuestion(editLessonForm)"
                                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-xs font-semibold flex items-center gap-1 cursor-pointer transition shadow-sm"
                                >
                                    <span>+ Trắc nghiệm</span>
                                </button>
                                <button
                                    type="button"
                                    @click="addPracticalTask(editLessonForm)"
                                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-semibold flex items-center gap-1 cursor-pointer transition shadow-sm"
                                >
                                    <span>+ Thực hành</span>
                                </button>
                            </div>
                        </div>

                        <!-- Danh sách câu hỏi đã tạo -->
                        <div v-if="editLessonForm.exercises && editLessonForm.exercises.length > 0" class="space-y-3 pt-1">
                            <div
                                v-for="(ex, exIdx) in editLessonForm.exercises"
                                :key="ex.id || exIdx"
                                class="p-4 rounded-xl border border-slate-800 bg-slate-900/90 space-y-3"
                            >
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-xs font-bold px-2.5 py-0.5 rounded"
                                        :class="ex.type === 'quiz' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30'"
                                    >
                                        {{ ex.type === 'quiz' ? `Câu ${exIdx + 1}: Trắc nghiệm` : `Bài ${exIdx + 1}: Thực hành / Tình huống` }}
                                    </span>
                                    <button
                                        type="button"
                                        @click="removeExercise(editLessonForm, exIdx)"
                                        class="text-xs text-rose-400 hover:text-rose-300 font-semibold cursor-pointer"
                                    >
                                        ✕ Xóa bài tập này
                                    </button>
                                </div>

                                <!-- Form trắc nghiệm -->
                                <template v-if="ex.type === 'quiz'">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-300 mb-1">Nội dung câu hỏi:</label>
                                        <input
                                            v-model="ex.question"
                                            type="text"
                                            placeholder="VD: Thiết bị nào sau đây thuộc công đoạn Input trong mô hình IPOS?"
                                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-slate-300">Các phương án (Tích chọn phương án đúng):</label>
                                        <div
                                            v-for="(opt, optIdx) in ex.options"
                                            :key="optIdx"
                                            class="flex items-center gap-2"
                                        >
                                            <input
                                                type="radio"
                                                :name="`edit_correct_${ex.id || exIdx}`"
                                                :value="optIdx"
                                                v-model="ex.correct_index"
                                                class="text-emerald-500 focus:ring-0 cursor-pointer"
                                                title="Chọn làm đáp án đúng"
                                            />
                                            <span class="text-xs font-bold text-slate-400 w-5">
                                                {{ String.fromCharCode(65 + optIdx) }}.
                                            </span>
                                            <input
                                                v-model="ex.options[optIdx]"
                                                type="text"
                                                :placeholder="`Phương án ${String.fromCharCode(65 + optIdx)}...`"
                                                class="flex-1 px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white"
                                            />
                                            <button
                                                v-if="ex.options.length > 2"
                                                type="button"
                                                @click="removeOption(ex, optIdx)"
                                                class="text-slate-500 hover:text-rose-400 text-xs px-1"
                                            >
                                                ✕
                                            </button>
                                        </div>
                                        <button
                                            v-if="ex.options.length < 6"
                                            type="button"
                                            @click="addOption(ex)"
                                            class="text-xs text-blue-400 hover:underline pt-1"
                                        >
                                            + Thêm phương án khác
                                        </button>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-slate-300 mb-1">Giải thích đáp án / Gợi ý kiến thức (Tùy chọn):</label>
                                        <input
                                            v-model="ex.explanation"
                                            type="text"
                                            placeholder="VD: Bàn phím là thiết bị Input nhận tín hiệu gõ phím từ người dùng..."
                                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white"
                                        />
                                    </div>
                                </template>

                                <!-- Form thực hành -->
                                <template v-else>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-300 mb-1">Tiêu đề bài thực hành / Tình huống:</label>
                                        <input
                                            v-model="ex.title"
                                            type="text"
                                            placeholder="VD: Lắp ráp bộ PC đồ họa 15 triệu đồng"
                                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-300 mb-1">Mô tả đề bài:</label>
                                        <textarea
                                            v-model="ex.description"
                                            rows="2"
                                            placeholder="Nội dung đề bài hoặc tình huống thực tế..."
                                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white"
                                        ></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-300 mb-1">Các bước yêu cầu (Mỗi dòng một bước):</label>
                                        <textarea
                                            v-model="ex.requirements"
                                            rows="2"
                                            placeholder="- Bước 1: Chuẩn bị linh kiện...&#10;- Bước 2: Lắp CPU và RAM..."
                                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white"
                                        ></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-300 mb-1">Hướng dẫn giải / Đáp án mẫu:</label>
                                        <textarea
                                            v-model="ex.solution"
                                            rows="2"
                                            placeholder="Gợi ý cấu hình tối ưu và lưu ý kỹ thuật..."
                                            class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white"
                                        ></textarea>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div v-else class="text-center py-4 text-xs text-slate-500">
                            Bài học này chưa có bài tập riêng biệt. Bấm <strong>+ Trắc nghiệm</strong> hoặc <strong>+ Thực hành</strong> để thêm.
                        </div>
                    </div>

                    <!-- Cho phép học thử -->
                    <div>
                        <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                            <input type="checkbox" v-model="editLessonForm.is_free_preview" class="rounded bg-slate-950 border-slate-800 text-blue-600 focus:ring-0" />
                            <span>Cho phép học thử miễn phí (Học viên chưa đăng ký/mua vẫn có thể xem bài này)</span>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-800">
                        <button type="button" @click="showEditLessonModal = false" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 rounded-xl text-slate-300 cursor-pointer">
                            Hủy
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl cursor-pointer flex items-center gap-1.5">
                            <span>✓</span>
                            <span>Lưu thay đổi bài học</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- =========================================================================
             MODAL 3: CỘNG / TRỪ TIỀN VÍ HỌC VIÊN
        ========================================================================== -->
        <div v-if="balanceModalUser" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-6 space-y-4">
                <h3 class="text-lg font-bold text-white">Điều chỉnh số dư: {{ balanceModalUser.name }}</h3>
                <p class="text-xs text-slate-400">Nhập số dương để cộng tiền (VD: 200000) hoặc số âm để trừ tiền (VD: -50000).</p>

                <form @submit.prevent="submitBalanceAdjust" class="space-y-4">
                    <div>
                        <label class="block text-xs text-slate-300 font-bold mb-1">Số tiền điều chỉnh (VNĐ)</label>
                        <input v-model="balanceForm.amount" type="number" step="10000" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-emerald-400 font-bold" />
                    </div>
                    <div>
                        <label class="block text-xs text-slate-300 font-bold mb-1">Ghi chú giao dịch</label>
                        <input v-model="balanceForm.note" type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white" />
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="balanceModalUser = null" class="px-4 py-2 bg-slate-800 rounded-xl text-xs text-slate-300 cursor-pointer">Đóng</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl cursor-pointer">Xác nhận</button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
