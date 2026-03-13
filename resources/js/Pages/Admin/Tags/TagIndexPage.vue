<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import EmptyStateComponent from '@/Components/Admin/Shared/EmptyStateComponent.vue';
import PaginationComponent from '@/Components/Admin/Shared/PaginationComponent.vue';
import ConfirmModalComponent from '@/Components/Admin/Shared/ConfirmModalComponent.vue';
import { usePermissions } from '@/Composables/usePermissions.js';

const { can } = usePermissions();

const props = defineProps({
    tags: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const sortBy = ref('name');
const sortDir = ref('asc');

const applyFilters = () => {
    router.get('/admin/tags', {
        search: search.value || undefined,
        sort: sortBy.value !== 'name' ? sortBy.value : undefined,
        direction: sortDir.value !== 'asc' ? sortDir.value : undefined,
    }, { preserveState: true, replace: true });
};

let searchTimeout = null;
watch(search, () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(applyFilters, 300); });

const toggleSort = (field) => {
    if (sortBy.value === field) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = field;
        sortDir.value = 'asc';
    }
    applyFilters();
};

const sortIcon = (field) => {
    if (sortBy.value !== field) return '';
    return sortDir.value === 'asc' ? ' ↑' : ' ↓';
};

// Confirm modal
const confirmModal = ref(false);
const confirmAction = ref(null);
const confirmTitle = ref('');
const confirmMessage = ref('');

const showConfirm = (title, message, action) => {
    confirmTitle.value = title;
    confirmMessage.value = message;
    confirmAction.value = action;
    confirmModal.value = true;
};

const onConfirm = () => {
    confirmModal.value = false;
    if (confirmAction.value) confirmAction.value();
};

const deleteTag = (id) => {
    showConfirm('Delete Tag', 'Are you sure you want to delete this tag?', () => {
        router.delete(`/admin/tags/${id}`);
    });
};
</script>

<template>
    <Head title="Tags" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[{ label: 'Dashboard', href: '/admin' }, { label: 'Tags' }]" />
        </template>

        <div class="space-y-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Tags</h1>
                <Link
                    v-if="can('manage tags')"
                    href="/admin/tags/create"
                    class="inline-flex items-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700"
                >
                    Add Tag
                </Link>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <input v-model="search" type="text" placeholder="Search tags..."
                    class="w-full sm:w-64 rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100" />
            </div>

            <div class="overflow-x-auto rounded-lg bg-white dark:bg-gray-800 shadow">
                <table v-if="tags.data.length" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/60">
                        <tr>
                            <th @click="toggleSort('name')" class="cursor-pointer px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 select-none">
                                Name{{ sortIcon('name') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Slug</th>
                            <th @click="toggleSort('posts_count')" class="cursor-pointer px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 select-none">
                                Posts{{ sortIcon('posts_count') }}
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="tag in tags.data" :key="tag.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3">
                                <Link :href="`/admin/tags/${tag.id}/edit`" class="text-sm font-medium text-cyan-600 hover:text-cyan-500">
                                    {{ tag.name }}
                                </Link>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ tag.slug }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ tag.posts_count }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <Link v-if="can('manage tags')" :href="`/admin/tags/${tag.id}/edit`"
                                    class="text-sm text-cyan-600 hover:text-cyan-500 mr-3">Edit</Link>
                                <button v-if="can('manage tags')" @click="deleteTag(tag.id)"
                                    class="text-sm text-red-600 hover:text-red-500">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <EmptyStateComponent v-else title="No tags found" description="Get started by creating a new tag.">
                    <template #action>
                        <Link v-if="can('manage tags')" href="/admin/tags/create"
                            class="inline-flex items-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700">
                            Add Tag
                        </Link>
                    </template>
                </EmptyStateComponent>
            </div>

            <PaginationComponent :links="tags.links" :last-page="tags.last_page" />
        </div>

        <ConfirmModalComponent
            :show="confirmModal"
            :title="confirmTitle"
            :message="confirmMessage"
            confirm-text="Delete"
            @confirm="onConfirm"
            @cancel="confirmModal = false"
        />
    </AdminLayout>
</template>
