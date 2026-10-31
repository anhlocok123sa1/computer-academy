<script setup>
import { Link } from "@inertiajs/vue3";
import ThemeToggle from "@/Components/ThemeToggle.vue";
</script>

<template>
    <div
        class="min-h-screen bg-[#060913] text-slate-100 flex flex-col selection:bg-blue-500 selection:text-white"
    >
        <!-- Top Navigation Bar -->
        <header
            class="sticky top-0 z-50 border-b border-slate-800/80 bg-[#060913]/85 backdrop-blur-xl"
        >
            <div
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4"
            >
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-3 group">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/25 group-hover:scale-105 transition"
                    >
                        <svg
                            class="w-6 h-6 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                    <div>
                        <span
                            class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-white via-slate-100 to-blue-400 bg-clip-text text-transparent"
                        >
                            ComputerAcademy
                        </span>
                        <span
                            class="block text-[11px] text-slate-400 font-medium -mt-1"
                            >Học viện Công nghệ & Máy tính</span
                        >
                    </div>
                </Link>

                <!-- Main Menu -->
                <nav
                    class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300"
                >
                    <Link
                        :href="route('courses.index')"
                        class="hover:text-blue-400 transition"
                        >Trang chủ</Link
                    >
                    <Link
                        :href="route('courses.explore')"
                        class="hover:text-blue-400 transition"
                        >Khám phá khóa học</Link
                    >
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="route('courses.my')"
                        class="text-blue-400 font-semibold hover:text-blue-300 transition flex items-center gap-1.5"
                    >
                        <span>📚 Khóa học của tôi</span>
                    </Link>
                </nav>

                <!-- Auth Buttons & Theme Toggle -->
                <div class="flex items-center gap-2.5">
                    <!-- Theme Toggle -->
                    <ThemeToggle />

                    <template v-if="$page.props.auth?.user">
                        <!-- Nút vào trang Admin (Chỉ hiện khi tài khoản là Admin) -->
                        <Link
                            v-if="$page.props.auth.user.role === 'admin'"
                            :href="route('admin.dashboard')"
                            class="px-3.5 py-2 rounded-xl bg-amber-500/15 hover:bg-amber-500/25 border border-amber-500/30 text-amber-300 text-xs font-extrabold transition flex items-center gap-1.5"
                        >
                            <span>⚡ Quản trị</span>
                        </Link>

                        <!-- Hiển thị Số dư Ví & Nút Nạp tiền (/profile) -->
                        <Link
                            :href="route('profile.edit')"
                            class="hidden sm:inline-flex items-center gap-2 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/25 px-3.5 py-2 rounded-xl text-xs font-bold text-emerald-400 transition"
                            title="Bấm để nạp tiền vào ví"
                        >
                            <span>💳 {{ Number($page.props.auth.user.balance || 0).toLocaleString('vi-VN') }} đ</span>
                        </Link>

                        <!-- Nút vào Hồ sơ cá nhân (/profile) -->
                        <Link
                            :href="route('profile.edit')"
                            class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 px-3.5 py-2 rounded-xl text-xs font-medium text-slate-200 transition"
                        >
                            <span class="w-6 h-6 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white">
                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                            </span>
                            <span class="hidden lg:inline">{{ $page.props.auth.user.name }}</span>
                        </Link>

                        <!-- Nút Đăng xuất -->
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="px-3 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/25 text-rose-400 text-xs font-semibold transition cursor-pointer"
                        >
                            Đăng xuất
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="px-4 py-2.5 text-sm font-medium text-slate-300 hover:text-white transition">
                            Đăng nhập
                        </Link>
                        <Link :href="route('register')" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-blue-600/25 transition">
                            Đăng ký miễn phí
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-800/80 bg-[#04060d] mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-bold"
                            >
                                💻
                            </div>
                            <span class="text-lg font-bold text-white"
                                >ComputerAcademy</span
                            >
                        </div>
                        <p
                            class="text-slate-400 text-sm max-w-md leading-relaxed"
                        >
                            Nền tảng đào tạo thực chiến về Kiến trúc Máy tính,
                            Quản trị Mạng, Linux Server, Kiểm thử phần mềm và
                            Phát triển Web hiện đại.
                        </p>
                    </div>
                    <div>
                        <h4
                            class="text-sm font-semibold text-white uppercase tracking-wider mb-4"
                        >
                            Chuyên ngành đào tạo
                        </h4>
                        <ul class="space-y-2.5 text-sm text-slate-400">
                            <li>Phần cứng PC & Hạ tầng Mạng</li>
                            <li>Quản trị Linux Server & Docker</li>
                            <li>Lập trình Fullstack Laravel & Vue</li>
                            <li>Kiểm thử QA/QC & An ninh mạng</li>
                        </ul>
                    </div>
                    <div>
                        <h4
                            class="text-sm font-semibold text-white uppercase tracking-wider mb-4"
                        >
                            Hỗ trợ học viên
                        </h4>
                        <ul class="space-y-2.5 text-sm text-slate-400">
                            <li>Tài liệu thực hành Lab 24/7</li>
                            <li>Cấp chứng chỉ hoàn thành</li>
                            <li>Cộng đồng hỏi đáp kỹ thuật</li>
                            <li>Cập nhật bài giảng trọn đời</li>
                        </ul>
                    </div>
                </div>
                <div
                    class="pt-8 border-t border-slate-800/60 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500"
                >
                    <p>
                        © 2026 ComputerAcademy. Xây dựng trên nền tảng Laravel,
                        Vue 3 & Inertia.js.
                    </p>
                    <p class="mt-2 sm:mt-0">
                        Thiết kế tối ưu cho trải nghiệm học tập CNTT
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
