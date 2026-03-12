<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePermissions } from '@/Composables/usePermissions.js';

const { can, hasRole } = usePermissions();
const page = usePage();

const currentUrl = computed(() => page.url);

const isActive = (path) => currentUrl.value.startsWith(path);

const navItems = computed(() => {
    const items = [];

    if (can('view dashboard')) {
        items.push({ label: 'Dashboard', href: '/admin', icon: 'squares', exact: true });
    }
    if (can('view pages')) {
        items.push({ label: 'Pages', href: '/admin/pages', icon: 'document' });
    }
    if (can('view posts')) {
        items.push({ label: 'Posts', href: '/admin/posts', icon: 'pencil' });
    }
    if (can('manage categories')) {
        items.push({ label: 'Categories', href: '/admin/categories', icon: 'folder' });
    }
    if (can('manage tags')) {
        items.push({ label: 'Tags', href: '/admin/tags', icon: 'tag' });
    }
    if (can('manage menus')) {
        items.push({ label: 'Menus', href: '/admin/menus', icon: 'bars' });
    }
    if (can('view enquiries')) {
        items.push({ label: 'Enquiries', href: '/admin/enquiries', icon: 'envelope', badge: page.props.enquiryCount ?? null });
    }
    if (can('manage media')) {
        items.push({ label: 'Media', href: '/admin/media', icon: 'photo' });
    }
    if (can('manage settings')) {
        items.push({ label: 'Settings', href: '/admin/settings', icon: 'cog' });
    }
    if (can('manage users')) {
        items.push({ label: 'Users', href: '/admin/users', icon: 'users' });
    }

    return items;
});

const isItemActive = (item) => {
    if (item.exact) return currentUrl.value === item.href;
    return isActive(item.href);
};
</script>

<template>
    <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900 text-white flex flex-col">
        <div class="flex h-16 items-center px-6 border-b border-gray-800">
            <Link href="/admin" class="text-lg font-bold tracking-tight">
                CMS
            </Link>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <Link
                v-for="item in navItems"
                :key="item.href"
                :href="item.href"
                class="flex items-center justify-between rounded-md px-3 py-2 text-sm font-medium transition-colors"
                :class="isItemActive(item)
                    ? 'bg-gray-800 text-white'
                    : 'text-gray-300 hover:bg-gray-800 hover:text-white'"
            >
                <span>{{ item.label }}</span>
                <span
                    v-if="item.badge"
                    class="ml-auto inline-flex items-center justify-center rounded-full bg-red-500 px-2 py-0.5 text-xs font-medium text-white"
                >
                    {{ item.badge }}
                </span>
            </Link>
        </nav>

        <div class="border-t border-gray-800 px-3 py-4 space-y-1">
            <Link
                href="/admin/profile"
                class="flex items-center rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white transition-colors"
                :class="isActive('/admin/profile') ? 'bg-gray-800 text-white' : ''"
            >
                Profile
            </Link>
        </div>
    </aside>
</template>
