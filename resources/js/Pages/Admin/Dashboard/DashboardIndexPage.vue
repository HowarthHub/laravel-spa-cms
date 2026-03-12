<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';

const props = defineProps({
    stats: Object,
    recentPosts: Array,
    recentEnquiries: Array,
});

const statCards = [
    { label: 'Total Pages', key: 'totalPages', color: 'bg-blue-500' },
    { label: 'Published Posts', key: 'publishedPosts', color: 'bg-green-500' },
    { label: 'New Enquiries', key: 'newEnquiries', color: 'bg-yellow-500' },
    { label: 'Media Files', key: 'totalMedia', color: 'bg-purple-500' },
];
</script>

<template>
    <Head title="Dashboard" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[{ label: 'Dashboard' }]" />
        </template>

        <div class="space-y-6">
            <!-- Stat Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="card in statCards"
                    :key="card.key"
                    class="overflow-hidden rounded-lg bg-white shadow"
                >
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div :class="[card.color, 'rounded-md p-3']">
                                    <div class="h-5 w-5 text-white" />
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">{{ card.label }}</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ stats[card.key] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex gap-3">
                <Link
                    href="/admin/pages/create"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                >
                    New Page
                </Link>
                <Link
                    href="/admin/posts/create"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                >
                    New Post
                </Link>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Recent Posts -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-medium text-gray-900">Recent Posts</h3>
                    </div>
                    <div v-if="recentPosts.length" class="divide-y divide-gray-200">
                        <div v-for="post in recentPosts" :key="post.id" class="px-6 py-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-gray-900">{{ post.title }}</span>
                                <span class="text-gray-500">{{ post.published_at }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="px-6 py-8 text-center text-sm text-gray-500">
                        No posts yet.
                    </div>
                </div>

                <!-- Recent Enquiries -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-medium text-gray-900">Recent Enquiries</h3>
                    </div>
                    <div v-if="recentEnquiries.length" class="divide-y divide-gray-200">
                        <div v-for="enquiry in recentEnquiries" :key="enquiry.id" class="px-6 py-3 text-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-medium text-gray-900">{{ enquiry.name }}</span>
                                    <span class="ml-2 text-gray-500">{{ enquiry.subject }}</span>
                                </div>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="{
                                        'bg-green-100 text-green-800': enquiry.status === 'new',
                                        'bg-gray-100 text-gray-800': enquiry.status !== 'new',
                                    }"
                                >
                                    {{ enquiry.status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="px-6 py-8 text-center text-sm text-gray-500">
                        No enquiries yet.
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
