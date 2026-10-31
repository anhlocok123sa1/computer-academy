<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    categories: Array,
    courses: Array
});

const selectedCategory = ref('all');
const searchQuery = ref('');

const filteredCourses = computed(() => {
    return props.courses.filter(course => {
        const matchCat = selectedCategory.value === 'all' || course.category_id === selectedCategory.value;
        const matchSearch = course.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                            (course.description && course.description.toLowerCase().includes(searchQuery.value.toLowerCase()));
        return matchCat && matchSearch;
    });
});

const getLevelLabel = (level) => {
    const map = {
        beginner: { text: 'Cơ bản', color: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' },
        intermediate: { text: 'Trung cấp', color: 'bg-amber-500/15 text-amber-400 border-amber-500/30' },
        advanced: { text: 'Chuyên sâu', color: 'bg-rose-500/15 text-rose-400 border-rose-500/30' }
    };
    return map[level] || map.beginner;
};

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
</script>

<template>
    <Head title="Computer Academy - Hệ thống đào tạo Máy tính & CNTT thực chiến" />

    <AppLayout>
        <!-- HERO SECTION -->
        <section class="relative overflow-hidden border-b border-slate-800/80 py-16 lg:py-24">
            <!-- Hiệu ứng ánh sáng nền -->
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute top-20 right-0 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    <!-- Cột trái: Nội dung Hero -->
                    <div class="lg:col-span-7 space-y-6">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/25 text-blue-400 text-xs font-semibold tracking-wide uppercase">
                            <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                            Nền tảng E-Learning CNTT Thực Chiến
                        </div>

                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.15] text-white">
                            Làm chủ công nghệ từ
                            <span class="bg-gradient-to-r from-blue-400 via-cyan-300 to-indigo-400 bg-clip-text text-transparent">
                                Phần cứng, Server
                            </span>
                            đến Phần mềm.
                        </h1>

                        <p class="text-slate-400 text-lg leading-relaxed max-w-2xl">
                            Hệ thống bài giảng trực quan từ lắp ráp PC, thiết kế hạ tầng mạng LAN/Wi-Fi, quản trị Linux Server đến lập trình Web Fullstack và kiểm thử tự động.
                        </p>

                        <!-- Thanh tìm kiếm nhanh -->
                        <div class="pt-2 max-w-xl">
                            <div class="relative flex items-center">
                                <svg class="w-5 h-5 text-slate-400 absolute left-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Tìm kiếm khóa học: Linux, Lắp ráp PC, Mạng, Laravel, MySQL..."
                                    class="w-full pl-12 pr-32 py-4 bg-slate-900/90 border border-slate-700/80 rounded-2xl text-sm text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition"
                                />
                                <a href="#danh-sach-khoa-hoc" class="absolute right-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition">
                                    Tìm kiếm
                                </a>
                            </div>
                        </div>

                        <!-- Thống kê -->
                        <div class="pt-4 grid grid-cols-3 gap-6 max-w-lg border-t border-slate-800/80">
                            <div>
                                <div class="text-2xl font-extrabold text-white">{{ courses.length }}+</div>
                                <div class="text-xs text-slate-400 mt-0.5">Khóa học chuyên sâu</div>
                            </div>
                            <div>
                                <div class="text-2xl font-extrabold text-blue-400">100%</div>
                                <div class="text-xs text-slate-400 mt-0.5">Thực hành thực tế</div>
                            </div>
                            <div>
                                <div class="text-2xl font-extrabold text-emerald-400">24/7</div>
                                <div class="text-xs text-slate-400 mt-0.5">Truy cập trọn đời</div>
                            </div>
                        </div>
                    </div>

                    <!-- Cột phải: Terminal / Tech Card Minh họa -->
                    <div class="lg:col-span-5">
                        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl shadow-2xl overflow-hidden">
                            <div class="bg-slate-800/80 px-4 py-3 flex items-center justify-between border-b border-slate-700/70">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                </div>
                                <span class="text-xs font-mono text-slate-400">root@computer-academy:~#</span>
                            </div>
                            <div class="p-6 font-mono text-xs sm:text-sm space-y-3 text-slate-300">
                                <div><span class="text-emerald-400">➜</span> <span class="text-cyan-400">~</span> ./check-skills.sh</div>
                                <div class="text-slate-400">[✓] Kiến trúc Phần cứng PC & Mạng nội bộ</div>
                                <div class="text-slate-400">[✓] Quản trị Ubuntu Server, Nginx & SSL</div>
                                <div class="text-slate-400">[✓] Lập trình Fullstack Laravel + Vue 3</div>
                                <div class="text-slate-400">[✓] Tối ưu hóa MySQL & Kiểm thử QA/QC</div>
                                <div class="pt-2"><span class="text-emerald-400">➜</span> <span class="text-cyan-400">~</span> status: <span class="bg-emerald-500/20 text-emerald-300 px-2 py-0.5 rounded">Sẵn sàng học tập!</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- DANH SÁCH KHÓA HỌC & BỘ LỌC -->
        <section id="danh-sach-khoa-hoc" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Danh mục Khóa học</h2>
                    <p class="text-slate-400 text-sm mt-1">Lựa chọn chuyên ngành bạn muốn chinh phục hôm nay</p>
                </div>

                <!-- Nút lọc Category -->
                <div class="flex flex-wrap gap-2">
                    <button
                        @click="selectedCategory = 'all'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition cursor-pointer"
                        :class="selectedCategory === 'all'
                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/25'
                            : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'"
                    >
                        Tất cả ({{ courses.length }})
                    </button>
                    <button
                        v-for="cat in categories"
                        :key="cat.id"
                        @click="selectedCategory = cat.id"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition cursor-pointer"
                        :class="selectedCategory === cat.id
                            ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/25'
                            : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800'"
                    >
                        {{ cat.name }}
                    </button>
                </div>
            </div>

            <!-- Grid Khóa học -->
            <div v-if="filteredCourses.length === 0" class="bg-slate-900/60 border border-slate-800 rounded-2xl p-12 text-center">
                <p class="text-slate-400 text-base">Không tìm thấy khóa học nào phù hợp với từ khóa của bạn.</p>
                <button @click="selectedCategory = 'all'; searchQuery = ''" class="mt-4 text-sm text-blue-400 hover:underline">
                    Xóa bộ lọc để xem tất cả
                </button>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div
                    v-for="course in filteredCourses"
                    :key="course.id"
                    class="group bg-slate-900/75 border border-slate-800/90 rounded-2xl overflow-hidden hover:border-blue-500/60 hover:-translate-y-1 transition-all duration-300 flex flex-col shadow-lg"
                >
                    <!-- Ảnh Thumbnail cố định tỷ lệ 16:9 -->
                    <Link :href="route('courses.show', course.slug)" class="relative block h-52 w-full overflow-hidden bg-slate-800">
                        <img
                            :src="course.thumbnail"
                            :alt="course.title"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>

                        <!-- Badge Cấp độ -->
                        <span
                            class="absolute top-3 left-3 text-[11px] font-semibold px-2.5 py-1 rounded-lg border backdrop-blur-md"
                            :class="getLevelLabel(course.level).color"
                        >
                            {{ getLevelLabel(course.level).text }}
                        </span>

                        <!-- Badge Giá miễn phí -->
                        <span
                            v-if="Number(course.price) === 0"
                            class="absolute top-3 right-3 bg-emerald-500 text-slate-950 text-[11px] font-extrabold px-2.5 py-1 rounded-lg shadow"
                        >
                            MIỄN PHÍ
                        </span>
                    </Link>

                    <!-- Nội dung thẻ -->
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between text-xs text-blue-400 font-semibold mb-2">
                                <span>{{ course.category?.name || 'Công nghệ thông tin' }}</span>
                                <span class="text-slate-400 font-normal">
                                    {{ getTotalLessons(course) }} bài giảng • {{ getTotalDuration(course) }} phút
                                </span>
                            </div>

                            <Link :href="route('courses.show', course.slug)">
                                <h3 class="text-lg font-bold text-white group-hover:text-blue-400 transition line-clamp-2 mb-2.5">
                                    {{ course.title }}
                                </h3>
                            </Link>

                            <p class="text-slate-400 text-sm line-clamp-2 leading-relaxed mb-5">
                                {{ course.description }}
                            </p>
                        </div>

                        <!-- Footer Card -->
                        <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-slate-400 block">Học phí</span>
                                <span class="text-lg font-extrabold" :class="Number(course.price) === 0 ? 'text-emerald-400' : 'text-white'">
                                    {{ Number(course.price) === 0 ? 'Miễn phí' : Number(course.price).toLocaleString('vi-VN') + ' đ' }}
                                </span>
                            </div>

                            <Link
                                :href="route('courses.show', course.slug)"
                                class="inline-flex items-center gap-1.5 bg-slate-800 group-hover:bg-blue-600 text-white text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-xl transition"
                            >
                                <span>Xem chi tiết</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- LỘ TRÌNH HỌC TẬP (LEARNING PATHS) -->
        <section id="lo-trinh" class="border-t border-slate-800/80 bg-slate-900/30 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Lộ trình Đào tạo Chuẩn Kỹ sư CNTT</h2>
                    <p class="text-slate-400 text-sm mt-2">Được thiết kế bài bản giúp bạn đi từ nền tảng phần cứng đến triển khai phần mềm thực tế</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-slate-900/80 border border-slate-800 p-6 rounded-2xl">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/15 text-blue-400 flex items-center justify-center font-bold mb-4">01</div>
                        <h3 class="text-lg font-bold text-white mb-2">Hạ tầng Phần cứng & Mạng</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Hiểu rõ cấu trúc máy tính, lắp ráp PC tối ưu, bấm cáp mạng, chia IP Subnet và cấu hình hệ thống Router, Access Point, Wi-Fi Mesh.
                        </p>
                    </div>
                    <div class="bg-slate-900/80 border border-slate-800 p-6 rounded-2xl">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/15 text-indigo-400 flex items-center justify-center font-bold mb-4">02</div>
                        <h3 class="text-lg font-bold text-white mb-2">Phát triển Phần mềm & CSDL</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Xây dựng ứng dụng Web hoàn chỉnh với PHP Laravel, Vue.js, thiết kế cơ sở dữ liệu MySQL chuẩn hóa và kiểm thử chất lượng phần mềm (QA/QC).
                        </p>
                    </div>
                    <div class="bg-slate-900/80 border border-slate-800 p-6 rounded-2xl">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center font-bold mb-4">03</div>
                        <h3 class="text-lg font-bold text-white mb-2">Quản trị Server & Triển khai</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Làm chủ máy chủ Linux Ubuntu, cấu hình Web Server Nginx, Plesk/cPanel, Docker và bảo mật ứng dụng trước các cuộc tấn công mạng.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
