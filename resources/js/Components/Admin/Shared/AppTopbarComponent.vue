<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const showDropdown = ref(false);

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-6">
        <div>
            <slot name="breadcrumb" />
        </div>

        <div class="relative">
            <button
                @click="showDropdown = !showDropdown"
                class="flex items-center gap-2 rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
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
                class="absolute right-0 z-20 mt-1 w-48 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5"
            >
                <Link
                    href="/admin/profile"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="showDropdown = false"
                >
                    Profile
                </Link>
                <button
                    @click="logout"
                    class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
                >
                    Logout
                </button>
            </div>
        </div>
    </header>
</template>
