<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import EmptyStateComponent from '@/Components/Admin/Shared/EmptyStateComponent.vue';
import { usePermissions } from '@/Composables/usePermissions.js';

const { can } = usePermissions();

const props = defineProps({
    pages: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const selected = ref([]);

let searchTimeout = null;
watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/pages', { search: val || undefined, status: status.value || undefined }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

watch(status, (val) => {
    router.get('/admin/pages', { search: search.value || undefined, status: val || undefined }, {
        preserveState: true,
        replace: true,
    });
});

const toggleAll = (e) => {
    selected.value = e.target.checked ? props.pages.data.map(p => p.id) : [];
};

const bulkDelete = () => {
    if (!confirm(`Delete ${selected.value.length} page(s)?`)) return;
    router.post('/admin/pages/bulk-destroy', { ids: selected.value }, {
        onSuccess: () => { selected.value = []; },
    });
};

const deletePage = (id) => {
    if (!confirm('Delete this page?')) return;
    router.delete(`/admin/pages/${id}`);
};

const statusBadge = (s) => {
    const map = {
        draft: 'bg-gray-100 text-gray-700',
        published: 'bg-green-100 text-green-700',
        scheduled: 'bg-yellow-100 text-yellow-700',
    };
    return map[s] || 'bg-gray-100 text-gray-700';
};
</script>

<template>
    <Head title="Pages" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[{ label: 'Dashboard', href: '/admin' }, { label: 'Pages' }]" />
        </template>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">Pages</h1>
                <Link
                    v-if="can('create pages')"
                    href="/admin/pages/create"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                >
                    Add Page
                </Link>
            </div>

            <!-- Filters -->
            <div class="flex items-center gap-3">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search pages..."
                    class="w-64 rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
                />
                <select
                    v-model="status"
                    class="rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
                >
                    <option value="">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="scheduled">Scheduled</option>
                </select>
                <button
                    v-if="selected.length && can('delete pages')"
                    @click="bulkDelete"
                    class="rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700"
                >
                    Delete ({{ selected.length }})
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <table v-if="pages.data.length" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th v-if="can('delete pages')" class="w-10 px-4 py-3">
                                <input type="checkbox" @change="toggleAll" class="rounded border-gray-300" />
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Template</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Author</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Updated</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="page in pages.data" :key="page.id" class="hover:bg-gray-50">
                            <td v-if="can('delete pages')" class="px-4 py-3">
                                <input type="checkbox" :value="page.id" v-model="selected" class="rounded border-gray-300" />
                            </td>
                            <td class="px-4 py-3">
                                <Link :href="`/admin/pages/${page.id}/edit`" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    {{ page.title }}
                                </Link>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="statusBadge(page.status)" class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium capitalize">
                                    {{ page.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 capitalize">{{ page.template }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ page.author?.name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ new Date(page.updated_at).toLocaleDateString() }}</td>
                            <td class="px-4 py-3 text-right">
                                <Link
                                    v-if="can('edit pages')"
                                    :href="`/admin/pages/${page.id}/edit`"
                                    class="text-sm text-indigo-600 hover:text-indigo-500 mr-3"
                                >
                                    Edit
                                </Link>
                                <button
                                    v-if="can('delete pages')"
                                    @click="deletePage(page.id)"
                                    class="text-sm text-red-600 hover:text-red-500"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <EmptyStateComponent
                    v-else
                    title="No pages found"
                    description="Get started by creating a new page."
                >
                    <template #action>
                        <Link
                            v-if="can('create pages')"
                            href="/admin/pages/create"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                        >
                            Add Page
                        </Link>
                    </template>
                </EmptyStateComponent>
            </div>

            <!-- Pagination -->
            <div v-if="pages.last_page > 1" class="flex justify-center gap-1">
                <Link
                    v-for="link in pages.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    v-html="link.label"
                    class="rounded-md px-3 py-1 text-sm"
                    :class="link.active
                        ? 'bg-indigo-600 text-white'
                        : link.url
                            ? 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                            : 'bg-white text-gray-300 border border-gray-200 cursor-default'"
                />
            </div>
        </div>
    </AdminLayout>
</template>
