<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import { usePermissions } from '@/Composables/usePermissions.js';

const { can } = usePermissions();

const props = defineProps({
    stats: Object,
    recentPosts: Array,
    recentEnquiries: Array,
    recentPages: Array,
});

const statCards = [
    { label: 'Total Pages', key: 'totalPages', color: 'bg-blue-500', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', href: '/admin/pages' },
    { label: 'Published Posts', key: 'publishedPosts', color: 'bg-green-500', icon: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z', href: '/admin/posts' },
    { label: 'New Enquiries', key: 'newEnquiries', color: 'bg-yellow-500', icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', href: '/admin/enquiries' },
    { label: 'Media Files', key: 'totalMedia', color: 'bg-purple-500', icon: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', href: '/admin/media' },
    { label: 'Draft Posts', key: 'draftPosts', color: 'bg-orange-500', icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', href: '/admin/posts?status=draft' },
    { label: 'Total Users', key: 'totalUsers', color: 'bg-cyan-500', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', href: '/admin/users' },
];

const statusBadge = (s) => ({
    draft: 'bg-gray-500 text-white',
    published: 'bg-emerald-500 text-white',
    scheduled: 'bg-amber-500 text-white',
    new: 'bg-cyan-500 text-white',
    read: 'bg-gray-500 text-white',
    replied: 'bg-emerald-500 text-white',
    archived: 'bg-amber-500 text-white',
})[s] || 'bg-gray-500 text-white';
</script>

<template>
    <Head title="Dashboard" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[{ label: 'Dashboard' }]" />
        </template>

        <div class="space-y-6">
            <!-- Stat Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="card in statCards"
                    :key="card.key"
                    :href="card.href"
                    class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow hover:shadow-md transition-shadow"
                >
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div :class="[card.color, 'rounded-md p-3']">
                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="card.icon" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">{{ card.label }}</dt>
                                    <dd class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats[card.key] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Quick Actions -->
            <div class="flex flex-wrap gap-3">
                <Link v-if="can('create pages')" href="/admin/pages/create"
                    class="inline-flex items-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700">
                    New Page
                </Link>
                <Link v-if="can('create posts')" href="/admin/posts/create"
                    class="inline-flex items-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700">
                    New Post
                </Link>
                <Link v-if="can('manage media')" href="/admin/media"
                    class="inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    Media Library
                </Link>
                <Link v-if="can('view enquiries')" href="/admin/enquiries?status=new"
                    class="inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    View New Enquiries
                </Link>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Recent Posts -->
                <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Recent Posts</h3>
                        <Link href="/admin/posts" class="text-xs text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">View all</Link>
                    </div>
                    <div v-if="recentPosts.length" class="divide-y divide-gray-200 dark:divide-gray-700">
                        <Link v-for="post in recentPosts" :key="post.id" :href="`/admin/posts/${post.id}/edit`"
                            class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ post.title }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">by {{ post.author?.name || 'Unknown' }}</p>
                            </div>
                            <div class="ml-4 flex items-center gap-2">
                                <span :class="statusBadge(post.status)" class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium capitalize">
                                    {{ post.status }}
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ new Date(post.updated_at).toLocaleDateString() }}</span>
                            </div>
                        </Link>
                    </div>
                    <div v-else class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No posts yet.</div>
                </div>

                <!-- Recent Enquiries -->
                <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Recent Enquiries</h3>
                        <Link href="/admin/enquiries" class="text-xs text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">View all</Link>
                    </div>
                    <div v-if="recentEnquiries.length" class="divide-y divide-gray-200 dark:divide-gray-700">
                        <Link v-for="enquiry in recentEnquiries" :key="enquiry.id" :href="`/admin/enquiries/${enquiry.id}`"
                            class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700"
                            :class="{ 'bg-blue-50/50 dark:bg-blue-900/20': enquiry.status === 'new' }">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100" :class="{ 'font-semibold': enquiry.status === 'new' }">
                                    {{ enquiry.name }}
                                </p>
                                <p class="truncate text-xs text-gray-400 dark:text-gray-500">{{ enquiry.subject || enquiry.email }}</p>
                            </div>
                            <div class="ml-4 flex items-center gap-2">
                                <span :class="statusBadge(enquiry.status)" class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium capitalize">
                                    {{ enquiry.status }}
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ new Date(enquiry.created_at).toLocaleDateString() }}</span>
                            </div>
                        </Link>
                    </div>
                    <div v-else class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No enquiries yet.</div>
                </div>

                <!-- Recent Pages -->
                <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow lg:col-span-2">
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Recent Pages</h3>
                        <Link href="/admin/pages" class="text-xs text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">View all</Link>
                    </div>
                    <div v-if="recentPages.length" class="divide-y divide-gray-200 dark:divide-gray-700">
                        <Link v-for="page in recentPages" :key="page.id" :href="`/admin/pages/${page.id}/edit`"
                            class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ page.title }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">by {{ page.author?.name || 'Unknown' }}</p>
                            </div>
                            <div class="ml-4 flex items-center gap-2">
                                <span :class="statusBadge(page.status)" class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium capitalize">
                                    {{ page.status }}
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ new Date(page.updated_at).toLocaleDateString() }}</span>
                            </div>
                        </Link>
                    </div>
                    <div v-else class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No pages yet.</div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
