import { computed, watchEffect } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

export function useDarkMode() {
    const page = usePage();
    const isDark = computed(() => page.props.darkMode ?? true);

    watchEffect(() => {
        if (isDark.value) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    });

    const toggle = () => {
        router.post('/admin/toggle-dark-mode', {}, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return { isDark, toggle };
}
