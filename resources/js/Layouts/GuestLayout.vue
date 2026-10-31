<script setup>
import { Link } from '@inertiajs/vue3';
import ThemeToggle from '@/Components/ThemeToggle.vue';
</script>

<template>
    <div class="min-h-screen bg-[#060913] text-slate-100 flex selection:bg-blue-500 selection:text-white">
        <!-- CỘT TRÁI: BANNER THƯƠNG HIỆU & LỢI ÍCH HỌC VIÊN (Ẩn trên mobile) -->
        <div class="hidden lg:flex lg:w-5/12 relative bg-gradient-to-br from-slate-900 via-[#0a1229] to-[#060913] border-r border-slate-800/80 p-12 flex-col justify-between overflow-hidden">
            <!-- Hiệu ứng ánh sáng nền -->
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-10 right-10 w-80 h-80 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Logo -->
            <div class="relative z-10">
                <Link href="/" class="inline-flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/25 group-hover:scale-105 transition">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight text-white">ComputerAcademy</span>
                        <span class="block text-xs text-blue-400 font-medium">Hệ thống E-Learning CNTT</span>
                    </div>
                </Link>
            </div>

            <!-- Nội dung truyền cảm hứng -->
            <div class="relative z-10 space-y-6 my-auto py-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/25 text-blue-400 text-xs font-semibold">
                    <span>🚀 Nâng cấp kỹ năng công nghệ mỗi ngày</span>
                </div>

                <h2 class="text-3xl xl:text-4xl font-extrabold text-white leading-tight">
                    Khởi đầu hành trình làm chủ
                    <span class="bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent">
                        Khoa học Máy tính
                    </span>
                </h2>

                <p class="text-slate-400 text-sm leading-relaxed">
                    Tạo tài khoản miễn phí để lưu tiến độ bài giảng, thực hành qua hệ thống video trực quan và nhận tài liệu kỹ thuật trọn đời.
                </p>

                <div class="space-y-4 pt-2">
                    <div class="flex items-start gap-3.5">
                        <div class="w-7 h-7 rounded-lg bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center shrink-0 text-xs font-bold mt-0.5">✓</div>
                        <div>
                            <h4 class="text-sm font-bold text-white">Thực hành từ Phần cứng đến Phần mềm</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Lắp ráp PC, Quản trị mạng, Linux Server, Laravel, Vue.js & QA/QC.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3.5">
                        <div class="w-7 h-7 rounded-lg bg-blue-500/15 border border-blue-500/30 text-blue-400 flex items-center justify-center shrink-0 text-xs font-bold mt-0.5">✓</div>
                        <div>
                            <h4 class="text-sm font-bold text-white">Lưu trữ tiến độ & Ghi chú cá nhân</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Đồng bộ bài học đang xem dở trên mọi thiết bị của bạn.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3.5">
                        <div class="w-7 h-7 rounded-lg bg-indigo-500/15 border border-indigo-500/30 text-indigo-400 flex items-center justify-center shrink-0 text-xs font-bold mt-0.5">✓</div>
                        <div>
                            <h4 class="text-sm font-bold text-white">Truy cập 24/7 không giới hạn</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Học bất cứ lúc nào với video chất lượng cao và source code đính kèm.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chân trang Cột trái -->
            <div class="relative z-10 pt-6 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-500">
                <span>© 2026 ComputerAcademy</span>
                <span>Bảo mật xác thực bởi Laravel</span>
            </div>
        </div>

        <!-- CỘT PHẢI: KHU VỰC FORM ĐĂNG NHẬP / ĐĂNG KÝ -->
        <div class="w-full lg:w-7/12 flex flex-col justify-between p-6 sm:p-10 lg:p-14 relative overflow-y-auto">
            <!-- Nút quay lại trang chủ & Theme Switcher -->
            <div class="flex items-center justify-between gap-3">
                <Link href="/" class="lg:hidden flex items-center gap-2 font-bold text-white">
                    <span class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center">💻</span>
                    <span>ComputerAcademy</span>
                </Link>
                <div class="ml-auto flex items-center gap-2.5">
                    <ThemeToggle />
                    <Link
                        href="/"
                        class="inline-flex items-center gap-2 text-xs sm:text-sm font-medium text-slate-400 hover:text-white bg-slate-900 hover:bg-slate-800 border border-slate-800 px-4 py-2 rounded-xl transition"
                    >
                        <span>&larr; Quay lại trang chủ</span>
                    </Link>
                </div>
            </div>

            <!-- Box chứa Form -->
            <div class="w-full max-w-md mx-auto my-auto py-8">
                <div class="bg-slate-900/75 border border-slate-800/90 rounded-3xl p-6 sm:p-10 shadow-2xl backdrop-blur-xl">
                    <slot />
                </div>
            </div>

            <!-- Ghi chú hỗ trợ -->
            <div class="text-center text-xs text-slate-500">
                Bằng việc tiếp tục, bạn đồng ý với Điều khoản dịch vụ và Chính sách bảo mật của ComputerAcademy.
            </div>
        </div>
    </div>
</template>
