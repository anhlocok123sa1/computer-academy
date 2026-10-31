<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Đăng nhập - Computer Academy" />

        <!-- Tiêu đề Form -->
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                Chào mừng trở lại! 👋
            </h1>
            <p class="text-slate-400 text-sm mt-2">
                Đăng nhập vào tài khoản học viên để tiếp tục bài giảng của bạn.
            </p>
        </div>

        <!-- Thông báo trạng thái (nếu có) -->
        <div v-if="status" class="mb-6 p-4 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-xs sm:text-sm font-medium text-emerald-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
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
                        autofocus
                        autocomplete="username"
                        placeholder="nhap-email@example.com"
                        class="w-full pl-10 pr-4 py-3 bg-slate-950/90 border rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none transition"
                        :class="form.errors.email ? 'border-rose-500 focus:border-rose-500' : 'border-slate-800 focus:border-blue-500'"
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
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                        Mật khẩu
                    </label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs font-medium text-blue-400 hover:text-blue-300 transition"
                    >
                        Quên mật khẩu?
                    </Link>
                </div>

                <div class="relative">
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full pl-10 pr-11 py-3 bg-slate-950/90 border rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none transition"
                        :class="form.errors.password ? 'border-rose-500 focus:border-rose-500' : 'border-slate-800 focus:border-blue-500'"
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

            <!-- Ghi nhớ đăng nhập -->
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        v-model="form.remember"
                        class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-blue-600 focus:ring-blue-500"
                    />
                    <span class="text-xs sm:text-sm text-slate-300">Duy trì đăng nhập</span>
                </label>
            </div>

            <!-- Nút Submit -->
            <button
                type="submit"
                :disabled="form.processing"
                class="w-full py-3.5 px-6 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 disabled:opacity-50 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-600/25 transition cursor-pointer flex items-center justify-center gap-2"
            >
                <span v-if="form.processing">Đang xác thực...</span>
                <span v-else>Đăng nhập vào hệ thống &rarr;</span>
            </button>

            <!-- Chuyển sang Đăng ký -->
            <div class="pt-5 border-t border-slate-800/80 text-center text-xs sm:text-sm text-slate-400">
                Chưa có tài khoản học viên?
                <Link :href="route('register')" class="text-blue-400 hover:text-blue-300 font-bold ml-1 hover:underline">
                    Đăng ký tài khoản mới
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
