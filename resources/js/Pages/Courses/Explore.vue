<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    categories: Array,
    courses: Array,
    initialFilters: Object
});

// Trạng thái bộ lọc
const searchQuery = ref(props.initialFilters?.search || '');
const selectedCategory = ref(props.initialFilters?.category || 'all');
const selectedLevel = ref('all');
const selectedPrice = ref('all'); // all | free | paid
const sortBy = ref('newest'); // newest | price_asc | price_desc | duration_desc
const viewMode = ref('grid'); // grid | list

// Hàm hỗ trợ tính toán thông số khóa học
const getTotalLessons = (course) => {
    if (!course.sections) return 0;
    return course.sections.reduce((total, sec) => total + (sec.lessons ? sec.lessons.length : 0), 0);
};

const getTotalDuration = (course) => {
    if (!course.sections) return 0;
    return course.sections.reduce((total, sec) => {
        return total + (sec.lessons ? sec.lessons.reduce((sum, l) => sum + (l.duration || 0), 0) : 0);
    }, 0);
};

const getFreePreviewCount = (course) => {
    if (!course.sections) return 0;
    return course.sections.reduce((total, sec) => {
        return total + (sec.lessons ? sec.lessons.filter(l => l.is_free_preview).length : 0);
    }, 0);
};

const getFirstLessonSlug = (course) => {
    return course.sections?.[0]?.lessons?.[0]?.slug || null;
};

const getLevelBadge = (level) => {
    const map = {
        beginner: { text: 'Cơ bản', class: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' },
        intermediate: { text: 'Trung cấp', class: 'bg-amber-500/15 text-amber-400 border-amber-500/30' },
        advanced: { text: 'Chuyên sâu', class: 'bg-rose-500/15 text-rose-400 border-rose-500/30' }
    };
    return map[level] || map.beginner;
};

// Đếm số lượng khóa học theo từng danh mục
const getCategoryCount = (catId) => {
    return props.courses.filter(c => c.category_id === catId).length;
};

// Lọc và sắp xếp danh sách khóa học
const filteredAndSortedCourses = computed(() => {
    let result = props.courses.filter(course => {
        // 1. Lọc theo từ khóa
        const q = searchQuery.value.trim().toLowerCase();
        const matchSearch = !q ||
            course.title.toLowerCase().includes(q) ||
            (course.description && course.description.toLowerCase().includes(q));

        // 2. Lọc theo danh mục
        const matchCategory = selectedCategory.value === 'all' ||
            String(course.category_id) === String(selectedCategory.value);

        // 3. Lọc theo cấp độ
        const matchLevel = selectedLevel.value === 'all' || course.level === selectedLevel.value;

        // 4. Lọc theo mức học phí
        const price = Number(course.price);
        const matchPrice = selectedPrice.value === 'all' ||
            (selectedPrice.value === 'free' && price === 0) ||
            (selectedPrice.value === 'paid' && price > 0);

        return matchSearch && matchCategory && matchLevel && matchPrice;
    });

    // 5. Sắp xếp
    result = [...result].sort((a, b) => {
        if (sortBy.value === 'price_asc') return Number(a.price) - Number(b.price);
        if (sortBy.value === 'price_desc') return Number(b.price) - Number(a.price);
        if (sortBy.value === 'duration_desc') return getTotalDuration(b) - getTotalDuration(a);
        return b.id - a.id; // newest
    });

    return result;
});

// Kiểm tra xem có bộ lọc nào đang kích hoạt không
const hasActiveFilters = computed(() => {
    return searchQuery.value !== '' ||
        selectedCategory.value !== 'all' ||
        selectedLevel.value !== 'all' ||
        selectedPrice.value !== 'all';
});

const resetFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = 'all';
    selectedLevel.value = 'all';
    selectedPrice.value = 'all';
    sortBy.value = 'newest';
};
</script>

<template>
    <Head title="Khám phá Khóa học CNTT - Computer Academy" />

    <AppLayout>
        <!-- HEADER BANNER -->
        <section class="relative bg-gradient-to-b from-slate-900 via-[#0a1020] to-[#060913] border-b border-slate-800/80 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-2 text-xs text-slate-400 mb-3">
                            <Link href="/" class="hover:text-blue-400 transition">Trang chủ</Link>
                            <span>/</span>
                            <span class="text-blue-400 font-medium">Khám phá khóa học</span>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                            Thư viện Khóa học Máy tính & CNTT
                        </h1>
                        <p class="text-slate-400 text-sm sm:text-base mt-2 max-w-2xl">
                            Tìm kiếm và lọc khóa học theo chuyên ngành, cấp độ kỹ năng hoặc thời lượng để bắt đầu lộ trình nâng cấp kiến thức của bạn.
                        </p>
                    </div>

                    <!-- Thống kê nhanh -->
                    <div class="flex items-center gap-4 bg-slate-900/90 border border-slate-800 px-5 py-4 rounded-2xl shrink-0">
                        <div class="text-center px-3">
                            <span class="block text-2xl font-extrabold text-blue-400">{{ courses.length }}</span>
                            <span class="text-[11px] text-slate-400 uppercase tracking-wider">Tổng khóa học</span>
                        </div>
                        <div class="h-8 w-px bg-slate-800"></div>
                        <div class="text-center px-3">
                            <span class="block text-2xl font-extrabold text-emerald-400">
                                {{ courses.filter(c => Number(c.price) === 0).length }}
                            </span>
                            <span class="text-[11px] text-slate-400 uppercase tracking-wider">Khóa miễn phí</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- MAIN CONTENT: SIDEBAR + COURSE CATALOG -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- SIDEBAR BỘ LỌC (3 CỘT) -->
                <aside class="lg:col-span-3 space-y-6 lg:sticky lg:top-24 bg-slate-900/60 border border-slate-800/90 rounded-2xl p-5">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <span>Bộ lọc tìm kiếm</span>
                        </h2>
                        <button
                            v-if="hasActiveFilters"
                            @click="resetFilters"
                            class="text-xs text-rose-400 hover:underline font-medium cursor-pointer"
                        >
                            Đặt lại
                        </button>
                    </div>

                    <!-- 1. Ô tìm kiếm từ khóa -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                            Từ khóa
                        </label>
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="VD: Linux, PC, Laravel, SQL..."
                                class="w-full pl-9 pr-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition"
                            />
                            <svg class="w-4 h-4 text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- 2. Lọc Chuyên ngành -->
                    <div class="pt-4 border-t border-slate-800/80">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
                            Chuyên ngành đào tạo
                        </label>
                        <div class="space-y-1.5">
                            <button
                                @click="selectedCategory = 'all'"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition cursor-pointer"
                                :class="selectedCategory === 'all' ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800/70'"
                            >
                                <span>Tất cả chuyên ngành</span>
                                <span class="text-xs px-2 py-0.5 rounded-md" :class="selectedCategory === 'all' ? 'bg-blue-700 text-white' : 'bg-slate-800 text-slate-400'">
                                    {{ courses.length }}
                                </span>
                            </button>

                            <button
                                v-for="cat in categories"
                                :key="cat.id"
                                @click="selectedCategory = cat.id"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition cursor-pointer text-left"
                                :class="String(selectedCategory) === String(cat.id) ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800/70'"
                            >
                                <span class="truncate pr-2">{{ cat.name }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-md shrink-0" :class="String(selectedCategory) === String(cat.id) ? 'bg-blue-700 text-white' : 'bg-slate-800 text-slate-400'">
                                    {{ getCategoryCount(cat.id) }}
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- 3. Lọc Cấp độ -->
                    <div class="pt-4 border-t border-slate-800/80">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
                            Cấp độ kỹ năng
                        </label>
                        <div class="space-y-2 text-xs sm:text-sm">
                            <label class="flex items-center gap-2.5 cursor-pointer text-slate-300 hover:text-white">
                                <input type="radio" value="all" v-model="selectedLevel" class="text-blue-600 bg-slate-900 border-slate-700 focus:ring-blue-500" />
                                <span>Tất cả cấp độ</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer text-slate-300 hover:text-white">
                                <input type="radio" value="beginner" v-model="selectedLevel" class="text-blue-600 bg-slate-900 border-slate-700 focus:ring-blue-500" />
                                <span>🌱 Cơ bản (Beginner)</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer text-slate-300 hover:text-white">
                                <input type="radio" value="intermediate" v-model="selectedLevel" class="text-blue-600 bg-slate-900 border-slate-700 focus:ring-blue-500" />
                                <span>🚀 Trung cấp (Intermediate)</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer text-slate-300 hover:text-white">
                                <input type="radio" value="advanced" v-model="selectedLevel" class="text-blue-600 bg-slate-900 border-slate-700 focus:ring-blue-500" />
                                <span>🔥 Chuyên sâu (Advanced)</span>
                            </label>
                        </div>
                    </div>

                    <!-- 4. Lọc Học phí -->
                    <div class="pt-4 border-t border-slate-800/80">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
                            Học phí
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <button
                                @click="selectedPrice = 'all'"
                                class="py-2 text-xs font-semibold rounded-xl border transition cursor-pointer"
                                :class="selectedPrice === 'all' ? 'bg-blue-600/20 border-blue-500 text-blue-400' : 'bg-slate-950 border-slate-800 text-slate-400 hover:text-white'"
                            >
                                Tất cả
                            </button>
                            <button
                                @click="selectedPrice = 'free'"
                                class="py-2 text-xs font-semibold rounded-xl border transition cursor-pointer"
                                :class="selectedPrice === 'free' ? 'bg-emerald-500/20 border-emerald-500 text-emerald-400' : 'bg-slate-950 border-slate-800 text-slate-400 hover:text-white'"
                            >
                                Miễn phí
                            </button>
                            <button
                                @click="selectedPrice = 'paid'"
                                class="py-2 text-xs font-semibold rounded-xl border transition cursor-pointer"
                                :class="selectedPrice === 'paid' ? 'bg-indigo-500/20 border-indigo-500 text-indigo-400' : 'bg-slate-950 border-slate-800 text-slate-400 hover:text-white'"
                            >
                                Có phí
                            </button>
                        </div>
                    </div>
                </aside>

                <!-- KHU VỰC HIỂN THỊ KHÓA HỌC (9 CỘT) -->
                <div class="lg:col-span-9 space-y-6">

                    <!-- Thanh công cụ (Số kết quả, Sắp xếp, Chế độ xem) -->
                    <div class="bg-slate-900/60 border border-slate-800/90 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="text-sm text-slate-300">
                            Hiển thị <span class="font-bold text-white">{{ filteredAndSortedCourses.length }}</span> trên tổng số <span class="font-bold text-white">{{ courses.length }}</span> khóa học
                        </div>

                        <div class="flex items-center gap-3 self-end sm:self-auto">
                            <!-- Dropdown Sắp xếp -->
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-slate-400 hidden sm:inline">Sắp xếp:</span>
                                <select
                                    v-model="sortBy"
                                    class="bg-slate-950 border border-slate-800 text-slate-200 text-xs sm:text-sm rounded-xl px-3 py-2 focus:outline-none focus:border-blue-500"
                                >
                                    <option value="newest">Mới cập nhật</option>
                                    <option value="price_asc">Học phí: Thấp đến Cao</option>
                                    <option value="price_desc">Học phí: Cao đến Thấp</option>
                                    <option value="duration_desc">Thời lượng nhiều nhất</option>
                                </select>
                            </div>

                            <!-- Nút chuyển đổi Grid / List -->
                            <div class="flex items-center bg-slate-950 border border-slate-800 rounded-xl p-1">
                                <button
                                    @click="viewMode = 'grid'"
                                    class="p-1.5 rounded-lg transition cursor-pointer"
                                    :class="viewMode === 'grid' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white'"
                                    title="Chế độ Lưới"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                </button>
                                <button
                                    @click="viewMode = 'list'"
                                    class="p-1.5 rounded-lg transition cursor-pointer"
                                    :class="viewMode === 'list' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white'"
                                    title="Chế độ Danh sách"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Trạng thái Không tìm thấy kết quả -->
                    <div
                        v-if="filteredAndSortedCourses.length === 0"
                        class="bg-slate-900/50 border border-slate-800 rounded-2xl p-12 text-center space-y-4"
                    >
                        <div class="w-14 h-14 rounded-2xl bg-slate-800/80 text-slate-400 flex items-center justify-center mx-auto text-2xl">
                            🔍
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-lg font-bold text-white">Không tìm thấy khóa học phù hợp</h3>
                            <p class="text-sm text-slate-400">Hãy thử thay đổi từ khóa hoặc xóa bớt các tiêu chí lọc bên trái.</p>
                        </div>
                        <button
                            @click="resetFilters"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-xl transition cursor-pointer"
                        >
                            Xóa toàn bộ bộ lọc
                        </button>
                    </div>

                    <!-- CHẾ ĐỘ XEM 1: GRID VIEW (2 CỘT LỚN) -->
                    <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div
                            v-for="course in filteredAndSortedCourses"
                            :key="course.id"
                            class="group bg-slate-900/75 border border-slate-800/90 rounded-2xl overflow-hidden hover:border-blue-500/60 transition-all duration-300 flex flex-col shadow-lg"
                        >
                            <Link :href="route('courses.show', course.slug)" class="relative block h-48 w-full overflow-hidden bg-slate-800">
                                <img
                                    :src="course.thumbnail"
                                    :alt="course.title"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-transparent to-transparent"></div>

                                <span
                                    class="absolute top-3 left-3 text-[11px] font-semibold px-2.5 py-1 rounded-lg border backdrop-blur-md"
                                    :class="getLevelBadge(course.level).class"
                                >
                                    {{ getLevelBadge(course.level).text }}
                                </span>

                                <span
                                    v-if="getFreePreviewCount(course) > 0"
                                    class="absolute bottom-3 left-3 bg-slate-900/90 border border-slate-700 text-emerald-400 text-[11px] font-medium px-2.5 py-1 rounded-lg"
                                >
                                    ▶ {{ getFreePreviewCount(course) }} bài học thử
                                </span>
                            </Link>

                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between text-xs text-blue-400 font-semibold mb-2">
                                        <span>{{ course.category?.name }}</span>
                                        <span class="text-slate-400 font-normal">
                                            {{ getTotalLessons(course) }} bài • {{ getTotalDuration(course) }} phút
                                        </span>
                                    </div>

                                    <Link :href="route('courses.show', course.slug)">
                                        <h3 class="text-base sm:text-lg font-bold text-white group-hover:text-blue-400 transition line-clamp-2 mb-2">
                                            {{ course.title }}
                                        </h3>
                                    </Link>

                                    <p class="text-slate-400 text-xs sm:text-sm line-clamp-2 leading-relaxed mb-4">
                                        {{ course.description }}
                                    </p>
                                </div>

                                <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between gap-2">
                                    <div>
                                        <span class="text-[11px] text-slate-400 block">Học phí</span>
                                        <span class="text-base sm:text-lg font-extrabold" :class="Number(course.price) === 0 ? 'text-emerald-400' : 'text-white'">
                                            {{ Number(course.price) === 0 ? 'Miễn phí' : Number(course.price).toLocaleString('vi-VN') + ' đ' }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <Link
                                            v-if="getFirstLessonSlug(course)"
                                            :href="route('courses.learn', [course.slug, getFirstLessonSlug(course)])"
                                            class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl transition"
                                            title="Vào phòng học ngay"
                                        >
                                            Học thử
                                        </Link>
                                        <Link
                                            :href="route('courses.show', course.slug)"
                                            class="px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-xl transition"
                                        >
                                            Chi tiết &rarr;
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CHẾ ĐỘ XEM 2: LIST VIEW (DANH SÁCH NGANG CHI TIẾT) -->
                    <div v-else class="space-y-4">
                        <div
                            v-for="course in filteredAndSortedCourses"
                            :key="course.id"
                            class="group bg-slate-900/75 border border-slate-800/90 rounded-2xl overflow-hidden hover:border-blue-500/60 transition flex flex-col sm:flex-row shadow-lg"
                        >
                            <Link :href="route('courses.show', course.slug)" class="relative sm:w-72 h-48 sm:h-auto shrink-0 overflow-hidden bg-slate-800">
                                <img
                                    :src="course.thumbnail"
                                    :alt="course.title"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                />
                                <span
                                    class="absolute top-3 left-3 text-[11px] font-semibold px-2.5 py-1 rounded-lg border backdrop-blur-md"
                                    :class="getLevelBadge(course.level).class"
                                >
                                    {{ getLevelBadge(course.level).text }}
                                </span>
                            </Link>

                            <div class="p-6 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-3 text-xs mb-2">
                                        <span class="text-blue-400 font-semibold">{{ course.category?.name }}</span>
                                        <span class="text-slate-600">•</span>
                                        <span class="text-slate-400">{{ course.sections?.length || 0 }} chương</span>
                                        <span class="text-slate-600">•</span>
                                        <span class="text-slate-400">{{ getTotalLessons(course) }} bài giảng</span>
                                        <span class="text-slate-600">•</span>
                                        <span class="text-slate-400">⏱ {{ getTotalDuration(course) }} phút</span>
                                    </div>

                                    <Link :href="route('courses.show', course.slug)">
                                        <h3 class="text-lg font-bold text-white group-hover:text-blue-400 transition mb-2">
                                            {{ course.title }}
                                        </h3>
                                    </Link>

                                    <p class="text-slate-400 text-sm leading-relaxed mb-4">
                                        {{ course.description }}
                                    </p>
                                </div>

                                <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                                    <span class="text-lg font-extrabold" :class="Number(course.price) === 0 ? 'text-emerald-400' : 'text-white'">
                                        {{ Number(course.price) === 0 ? 'Miễn phí 100%' : Number(course.price).toLocaleString('vi-VN') + ' đ' }}
                                    </span>

                                    <div class="flex items-center gap-2.5">
                                        <Link
                                            v-if="getFirstLessonSlug(course)"
                                            :href="route('courses.learn', [course.slug, getFirstLessonSlug(course)])"
                                            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl transition"
                                        >
                                            ▶ Học thử ngay
                                        </Link>
                                        <Link
                                            :href="route('courses.show', course.slug)"
                                            class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-xl transition"
                                        >
                                            Xem đề cương &rarr;
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </AppLayout>
</template>
