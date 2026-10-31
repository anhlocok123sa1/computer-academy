import { ref, computed } from 'vue';

// Shared reactive state
const theme = ref(
    typeof window !== 'undefined'
        ? (localStorage.getItem('theme') === 'light' ? 'light' : 'dark')
        : 'dark'
);

const isDark = computed(() => theme.value === 'dark');

export function useTheme() {
    const applyTheme = (val) => {
        theme.value = val;
        if (typeof window === 'undefined') return;

        try {
            localStorage.setItem('theme', val);
        } catch (e) {
            // ignore localStorage quota or privacy restrictions
        }

        if (val === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    };

    const toggleTheme = () => {
        applyTheme(theme.value === 'dark' ? 'light' : 'dark');
    };

    const setTheme = (val) => {
        if (val === 'dark' || val === 'light') {
            applyTheme(val);
        }
    };

    const initTheme = () => {
        if (typeof window === 'undefined') return;
        const saved = localStorage.getItem('theme');
        applyTheme(saved === 'light' ? 'light' : 'dark');
    };

    // Listen for storage changes across tabs
    if (typeof window !== 'undefined') {
        window.addEventListener('storage', (event) => {
            if (event.key === 'theme' && event.newValue) {
                applyTheme(event.newValue);
            }
        });
    }

    return {
        theme,
        isDark,
        toggleTheme,
        setTheme,
        initTheme,
    };
}
