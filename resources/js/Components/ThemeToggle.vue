<script setup>
import { useTheme } from '@/Composables/useTheme';

const { isDark, toggleTheme } = useTheme();

defineProps({
    compact: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <button
        type="button"
        @click="toggleTheme"
        :aria-label="isDark ? 'Chuyển sang chế độ sáng' : 'Chuyển sang chế độ tối'"
        :title="isDark ? 'Chuyển sang giao diện Sáng' : 'Chuyển sang giao diện Tối'"
        class="relative inline-flex items-center justify-center rounded-xl p-2.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500/50 cursor-pointer select-none theme-toggle-btn"
        :class="[
            isDark
                ? 'bg-slate-800/80 hover:bg-slate-700 text-amber-400 border border-slate-700/80 shadow-sm'
                : 'bg-white hover:bg-slate-100 text-amber-600 border border-slate-200 shadow-sm'
        ]"
    >
        <!-- Icon Mặt trời (Hiển thị khi đang ở chế độ tối -> bấm để sang sáng) -->
        <svg
            v-if="isDark"
            class="w-4 h-4 transition-transform duration-300 transform rotate-0 hover:rotate-45"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
            />
        </svg>

        <!-- Icon Mặt trăng (Hiển thị khi đang ở chế độ sáng -> bấm để sang tối) -->
        <svg
            v-else
            class="w-4 h-4 text-slate-700 transition-transform duration-300 transform -rotate-12 hover:rotate-0"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
            />
        </svg>

        <!-- Nhãn phụ nếu không phải compact -->
        <span v-if="!compact" class="sr-only">
            {{ isDark ? 'Giao diện Tối' : 'Giao diện Sáng' }}
        </span>
    </button>
</template>
