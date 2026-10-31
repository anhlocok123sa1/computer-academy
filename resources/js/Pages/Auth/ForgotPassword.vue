<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Khôi phục mật khẩu - Computer Academy" />

        <div class="mb-6">
            <h1 class="text-2xl font-extrabold text-white">Khôi phục mật khẩu 🔐</h1>
            <p class="text-slate-400 text-sm mt-2 leading-relaxed">
                Nhập địa chỉ email đã đăng ký của bạn. Hệ thống sẽ gửi đường dẫn đặt lại mật khẩu mới ngay lập tức.
            </p>
        </div>

        <div v-if="status" class="mb-6 p-4 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-xs sm:text-sm font-medium text-emerald-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Địa chỉ Email
                </label>
                <input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
                    placeholder="nhap-email@example.com"
                    class="w-full px-4 py-3 bg-slate-950/90 border border-slate-800 focus:border-blue-500 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none transition"
                />
                <p v-if="form.errors.email" class="mt-1.5 text-xs text-rose-400 font-medium">
                    {{ form.errors.email }}
                </p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full py-3.5 px-6 bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-600/25 transition cursor-pointer"
            >
                Gửi liên kết đặt lại mật khẩu
            </button>

            <div class="pt-4 border-t border-slate-800/80 text-center">
                <Link :href="route('login')" class="text-xs sm:text-sm text-slate-400 hover:text-white transition">
                    &larr; Quay lại trang Đăng nhập
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
