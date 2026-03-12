<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import EmptyStateComponent from '@/Components/Admin/Shared/EmptyStateComponent.vue';
import { usePermissions } from '@/Composables/usePermissions.js';

const { can } = usePermissions();

const props = defineProps({
    tags: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

let searchTimeout = null;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/tags', {
            search: search.value || undefined,
        }, { preserveState: true, replace: true });
    }, 300);
});

const deleteTag = (id) => {
    if (!confirm('Delete this tag?')) return;
    router.delete(`/admin/tags/${id}`);
};
</script>

<template>
    <Head title="Tags" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[{ label: 'Dashboard', href: '/admin' }, { label: 'Tags' }]" />
        </template>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">Tags</h1>
                <Link
                    v-if="can('manage tags')"
                    href="/admin/tags/create"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                >
                    Add Tag
                </Link>
            </div>

            <div class="flex items-center gap-3">
                <input v-model="search" type="text" placeholder="Search tags..."
                    class="w-64 rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow">
                <table v-if="tags.data.length" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Slug</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Posts</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="tag in tags.data" :key="tag.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <Link :href="`/admin/tags/${tag.id}/edit`" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    {{ tag.name }}
                                </Link>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ tag.slug }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ tag.posts_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <Link v-if="can('manage tags')" :href="`/admin/tags/${tag.id}/edit`"
                                    class="text-sm text-indigo-600 hover:text-indigo-500 mr-3">Edit</Link>
                                <button v-if="can('manage tags')" @click="deleteTag(tag.id)"
                                    class="text-sm text-red-600 hover:text-red-500">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <EmptyStateComponent v-else title="No tags found" description="Get started by creating a new tag.">
                    <template #action>
                        <Link v-if="can('manage tags')" href="/admin/tags/create"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                            Add Tag
                        </Link>
                    </template>
                </EmptyStateComponent>
            </div>

            <div v-if="tags.last_page > 1" class="flex justify-center gap-1">
                <Link v-for="link in tags.links" :key="link.label" :href="link.url || '#'" v-html="link.label"
                    class="rounded-md px-3 py-1 text-sm"
                    :class="link.active ? 'bg-indigo-600 text-white' : link.url ? 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' : 'bg-white text-gray-300 border border-gray-200 cursor-default'" />
            </div>
        </div>
    </AdminLayout>
</template>
