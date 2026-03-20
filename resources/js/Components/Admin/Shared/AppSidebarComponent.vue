<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { usePermissions } from '@/Composables/usePermissions.js';

const { can, hasRole } = usePermissions();
const page = usePage();

defineProps({
    open: { type: Boolean, default: false },
});

const emit = defineEmits(['close']);

const currentUrl = computed(() => page.url);

const isActive = (path) => currentUrl.value.startsWith(path);

// Track which collapsible groups are open
const openGroups = ref({
    posts: isActive('/admin/posts') || isActive('/admin/categories') || isActive('/admin/tags'),
    settings: isActive('/admin/settings') || isActive('/admin/menus') || isActive('/admin/forms') || isActive('/admin/redirects'),
});

const toggleGroup = (key) => {
    openGroups.value[key] = !openGroups.value[key];
};

const isGroupActive = (children) => {
    return children.some((child) => isActive(child.href));
};

const navItems = computed(() => {
    const items = [];

    if (can('view dashboard')) {
        items.push({ label: 'Dashboard', href: '/admin', exact: true });
    }
    if (can('view pages')) {
        items.push({ label: 'Pages', href: '/admin/pages' });
    }
    if (can('view posts')) {
        const children = [];
        children.push({ label: 'All Posts', href: '/admin/posts' });
        if (can('manage categories')) {
            children.push({ label: 'Categories', href: '/admin/categories' });
        }
        if (can('manage tags')) {
            children.push({ label: 'Tags', href: '/admin/tags' });
        }
        items.push({ label: 'Posts', group: 'posts', children });
    }
    if (can('view enquiries')) {
        items.push({ label: 'Enquiries', href: '/admin/enquiries', badge: page.props.enquiryCount ?? null });
    }
    if (can('manage media')) {
        items.push({ label: 'Media', href: '/admin/media' });
    }

    // Settings group — menus, forms, redirects, site settings
    if (can('manage settings')) {
        const children = [];
        children.push({ label: 'General', href: '/admin/settings' });
        if (can('manage menus')) {
            children.push({ label: 'Menus', href: '/admin/menus' });
        }
        if (can('manage forms')) {
            children.push({ label: 'Forms', href: '/admin/forms' });
        }
        if (can('manage redirects')) {
            children.push({ label: 'Redirects', href: '/admin/redirects' });
        }
        items.push({ label: 'Settings', group: 'settings', children });
    }

    if (can('manage users')) {
        items.push({ label: 'Users', href: '/admin/users' });
    }

    return items;
});

const isItemActive = (item) => {
    if (item.exact) return currentUrl.value === item.href;
    if (item.children) return false;
    return isActive(item.href);
};

const navigate = () => {
    emit('close');
};
</script>

<template>
    <aside
        class="fixed inset-y-0 left-0 z-40 w-64 bg-gray-950 text-white flex flex-col transition-transform duration-200 ease-in-out lg:translate-x-0"
        :class="open ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex h-16 items-center justify-between px-6 border-b border-gray-800/50">
            <Link href="/admin" class="text-lg font-bold tracking-tight" @click="navigate">
                {{ page.props.site?.name || 'CMS' }}
            </Link>
            <button @click="$emit('close')" class="lg:hidden rounded-md p-1 text-gray-400 hover:text-white">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
            <template v-for="item in navItems" :key="item.label">
                <!-- Collapsible group -->
                <div v-if="item.children">
                    <button
                        type="button"
                        @click="toggleGroup(item.group)"
                        class="flex w-full items-center justify-between rounded-md px-3 py-2 text-sm font-medium transition-colors"
                        :class="isGroupActive(item.children)
                            ? 'bg-gray-800/60 text-white'
                            : 'text-gray-400 hover:bg-gray-800/40 hover:text-white'"
                    >
                        <span>{{ item.label }}</span>
                        <svg
                            class="h-4 w-4 transition-transform"
                            :class="{ 'rotate-90': openGroups[item.group] }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div v-show="openGroups[item.group]" class="mt-0.5 space-y-0.5 pl-4">
                        <Link
                            v-for="child in item.children"
                            :key="child.href"
                            :href="child.href"
                            class="flex items-center rounded-md px-3 py-1.5 text-sm font-medium transition-colors"
                            :class="isActive(child.href)
                                ? 'bg-gray-800/60 text-white'
                                : 'text-gray-500 hover:bg-gray-800/40 hover:text-white'"
                            @click="navigate"
                        >
                            {{ child.label }}
                        </Link>
                    </div>
                </div>

                <!-- Regular nav item -->
                <Link
                    v-else
                    :href="item.href"
                    class="flex items-center justify-between rounded-md px-3 py-2 text-sm font-medium transition-colors"
                    :class="isItemActive(item)
                        ? 'bg-gray-800/60 text-white'
                        : 'text-gray-400 hover:bg-gray-800/40 hover:text-white'"
                    @click="navigate"
                >
                    <span>{{ item.label }}</span>
                    <span
                        v-if="item.badge"
                        class="ml-auto inline-flex items-center justify-center rounded-full bg-red-500 px-2 py-0.5 text-xs font-medium text-white"
                    >
                        {{ item.badge }}
                    </span>
                </Link>
            </template>
        </nav>

        <div class="border-t border-gray-800/50 px-3 py-4 space-y-1">
            <a
                href="/"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-400 hover:bg-gray-800/40 hover:text-white transition-colors"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                    <polyline points="15 3 21 3 21 9" />
                    <line x1="10" y1="14" x2="21" y2="3" />
                </svg>
                View Website
            </a>
            <Link
                href="/admin/profile"
                class="flex items-center rounded-md px-3 py-2 text-sm font-medium text-gray-400 hover:bg-gray-800/40 hover:text-white transition-colors"
                :class="isActive('/admin/profile') ? 'bg-gray-800/60 text-white' : ''"
                @click="navigate"
            >
                Profile
            </Link>
        </div>
    </aside>
</template>
