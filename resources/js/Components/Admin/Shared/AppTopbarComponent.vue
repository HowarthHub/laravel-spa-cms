<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useDarkMode } from '@/Composables/useDarkMode.js';

const page = usePage();
const user = computed(() => page.props.auth.user);
const showDropdown = ref(false);
const { isDark, toggle: toggleDarkMode } = useDarkMode();

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-gray-200 dark:border-gray-800/50 bg-white dark:bg-gray-950 px-6">
        <div>
            <slot name="breadcrumb" />
        </div>

        <div class="flex items-center gap-2">
            <!-- Dark mode toggle -->
            <button
                @click="toggleDarkMode"
                class="rounded-md p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                :title="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
            >
                <!-- Sun icon (shown in dark mode) -->
                <svg v-if="isDark" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <!-- Moon icon (shown in light mode) -->
                <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            <div class="relative">
                <button
                    @click="showDropdown = !showDropdown"
                    class="flex items-center gap-2 rounded-md px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                >
                    <span class="font-medium">{{ user?.name }}</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div
                    v-if="showDropdown"
                    @click="showDropdown = false"
                    class="fixed inset-0 z-10"
                />
                <div
                    v-if="showDropdown"
                    class="absolute right-0 z-20 mt-1 w-48 rounded-md bg-white dark:bg-gray-800 py-1 shadow-lg ring-1 ring-black/5 dark:ring-white/10"
                >
                    <Link
                        href="/admin/profile"
                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        @click="showDropdown = false"
                    >
                        Profile
                    </Link>
                    <button
                        @click="logout"
                        class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </header>
</template>
