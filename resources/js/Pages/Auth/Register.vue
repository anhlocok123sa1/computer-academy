<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const showPassword = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Đăng ký tài khoản - Computer Academy" />

        <!-- Tiêu đề Form -->
        <div class="mb-7">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                Tạo tài khoản Học viên 🎓
            </h1>
            <p class="text-slate-400 text-sm mt-2">
                Điền thông tin bên dưới để kích hoạt tài khoản học tập miễn phí.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Họ và tên -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Họ và tên của bạn
                </label>
                <div class="relative">
                    <input
                        id="name"
                        type="text"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="VD: Nguyễn Văn A"
                        class="w-full pl-10 pr-4 py-3 bg-slate-950/90 border rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none transition"
                        :class="form.errors.name ? 'border-rose-500' : 'border-slate-800 focus:border-blue-500'"
                    />
                    <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <p v-if="form.errors.name" class="mt-1.5 text-xs text-rose-400 font-medium">
                    {{ form.errors.name }}
                </p>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Địa chỉ Email
                </label>
                <div class="relative">
                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        placeholder="email-cua-ban@example.com"
                        class="w-full pl-10 pr-4 py-3 bg-slate-950/90 border rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none transition"
                        :class="form.errors.email ? 'border-rose-500' : 'border-slate-800 focus:border-blue-500'"
                    />
                    <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <p v-if="form.errors.email" class="mt-1.5 text-xs text-rose-400 font-medium">
                    {{ form.errors.email }}
                </p>
            </div>

            <!-- Mật khẩu -->
            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Mật khẩu (Tối thiểu 8 ký tự)
                </label>
                <div class="relative">
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="Nhập mật khẩu..."
                        class="w-full pl-10 pr-11 py-3 bg-slate-950/90 border rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none transition"
                        :class="form.errors.password ? 'border-rose-500' : 'border-slate-800 focus:border-blue-500'"
                    />
                    <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute right-3 top-3 text-slate-400 hover:text-white text-xs px-1.5 py-0.5 rounded cursor-pointer"
                    >
                        {{ showPassword ? 'Ẩn' : 'Hiện' }}
                    </button>
                </div>
                <p v-if="form.errors.password" class="mt-1.5 text-xs text-rose-400 font-medium">
                    {{ form.errors.password }}
                </p>
            </div>

            <!-- Xác nhận mật khẩu -->
            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Nhập lại mật khẩu
                </label>
                <div class="relative">
                    <input
                        id="password_confirmation"
                        :type="showPassword ? 'text' : 'password'"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Nhập lại mật khẩu..."
                        class="w-full pl-10 pr-4 py-3 bg-slate-950/90 border border-slate-800 focus:border-blue-500 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none transition"
                    />
                    <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <p v-if="form.errors.password_confirmation" class="mt-1.5 text-xs text-rose-400 font-medium">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>

            <!-- Nút Submit -->
            <div class="pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-3.5 px-6 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 disabled:opacity-50 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-600/25 transition cursor-pointer flex items-center justify-center gap-2"
                >
                    <span v-if="form.processing">Đang khởi tạo tài khoản...</span>
                    <span v-else>Hoàn tất Đăng ký &rarr;</span>
                </button>
            </div>

            <!-- Chuyển sang Đăng nhập -->
            <div class="pt-5 border-t border-slate-800/80 text-center text-xs sm:text-sm text-slate-400">
                Bạn đã có tài khoản?
                <Link :href="route('login')" class="text-blue-400 hover:text-blue-300 font-bold ml-1 hover:underline">
                    Đăng nhập ngay
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
