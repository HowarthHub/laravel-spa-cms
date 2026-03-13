<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import EmptyStateComponent from '@/Components/Admin/Shared/EmptyStateComponent.vue';
import PaginationComponent from '@/Components/Admin/Shared/PaginationComponent.vue';
import StatusBadgeComponent from '@/Components/Admin/Shared/StatusBadgeComponent.vue';
import ConfirmModalComponent from '@/Components/Admin/Shared/ConfirmModalComponent.vue';
import { usePermissions } from '@/Composables/usePermissions.js';

const { can } = usePermissions();

const props = defineProps({
    pages: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const selected = ref([]);
const sortBy = ref('updated_at');
const sortDir = ref('desc');

const applyFilters = () => {
    router.get('/admin/pages', {
        search: search.value || undefined,
        status: status.value || undefined,
        sort: sortBy.value !== 'updated_at' ? sortBy.value : undefined,
        direction: sortDir.value !== 'desc' ? sortDir.value : undefined,
    }, { preserveState: true, replace: true });
};

let searchTimeout = null;
watch(search, () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(applyFilters, 300); });
watch(status, applyFilters);

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
    return sortDir.value === 'asc' ? ' \u2191' : ' \u2193';
};

const toggleAll = (e) => {
    selected.value = e.target.checked ? props.pages.data.map(p => p.id) : [];
};

// Confirm modal
const confirmModal = ref(false);
const confirmAction = ref(null);
const confirmTitle = ref('');
const confirmMessage = ref('');
const confirmButtonText = ref('Confirm');

const showConfirm = (title, message, action, buttonText = 'Confirm') => {
    confirmTitle.value = title;
    confirmMessage.value = message;
    confirmAction.value = action;
    confirmButtonText.value = buttonText;
    confirmModal.value = true;
};

const onConfirm = () => {
    confirmModal.value = false;
    if (confirmAction.value) confirmAction.value();
};

const bulkDelete = () => {
    showConfirm(
        'Delete Pages',
        `Are you sure you want to delete ${selected.value.length} page(s)? This cannot be undone.`,
        () => router.post('/admin/pages/bulk-destroy', { ids: selected.value }, {
            onSuccess: () => { selected.value = []; },
        }),
        'Delete'
    );
};

const bulkDraft = () => {
    showConfirm(
        'Set to Draft',
        `Are you sure you want to set ${selected.value.length} page(s) to draft?`,
        () => router.post('/admin/pages/bulk-draft', { ids: selected.value }, {
            onSuccess: () => { selected.value = []; },
        })
    );
};

const bulkPublish = () => {
    showConfirm(
        'Publish Pages',
        `Are you sure you want to publish ${selected.value.length} page(s)?`,
        () => router.post('/admin/pages/bulk-publish', { ids: selected.value }, {
            onSuccess: () => { selected.value = []; },
        })
    );
};

const deletePage = (id) => {
    showConfirm('Delete Page', 'Are you sure you want to delete this page?', () => {
        router.delete(`/admin/pages/${id}`);
    });
};
</script>

<template>
    <Head title="Pages" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[{ label: 'Dashboard', href: '/admin' }, { label: 'Pages' }]" />
        </template>

        <div class="space-y-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Pages</h1>
                <Link
                    v-if="can('create pages')"
                    href="/admin/pages/create"
                    class="inline-flex items-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700"
                >
                    Add Page
                </Link>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <input v-model="search" type="text" placeholder="Search pages..."
                    class="w-full sm:w-64 rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100" />
                <select v-model="status"
                    class="rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100">
                    <option value="">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="scheduled">Scheduled</option>
                </select>
                <button v-if="selected.length && can('edit pages')" @click="bulkDraft"
                    class="rounded-md bg-gray-500 px-3 py-2 text-sm font-medium text-white hover:bg-gray-600">
                    Draft ({{ selected.length }})
                </button>
                <button v-if="selected.length && can('publish pages')" @click="bulkPublish"
                    class="rounded-md bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-700">
                    Publish ({{ selected.length }})
                </button>
                <button v-if="selected.length && can('delete pages')" @click="bulkDelete"
                    class="rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700">
                    Delete ({{ selected.length }})
                </button>
            </div>

            <div class="overflow-x-auto rounded-lg bg-white dark:bg-gray-800 shadow">
                <table v-if="pages.data.length" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/60">
                        <tr>
                            <th v-if="can('delete pages')" class="w-10 px-4 py-3">
                                <input type="checkbox" @change="toggleAll" class="rounded border-gray-300 dark:border-gray-600" />
                            </th>
                            <th @click="toggleSort('title')" class="cursor-pointer px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 select-none">
                                Title{{ sortIcon('title') }}
                            </th>
                            <th @click="toggleSort('status')" class="cursor-pointer px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 select-none">
                                Status{{ sortIcon('status') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Template</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Author</th>
                            <th @click="toggleSort('updated_at')" class="cursor-pointer px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 select-none">
                                Last Updated{{ sortIcon('updated_at') }}
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="page in pages.data" :key="page.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td v-if="can('delete pages')" class="whitespace-nowrap px-4 py-3">
                                <input type="checkbox" :value="page.id" v-model="selected" class="rounded border-gray-300 dark:border-gray-600" />
                            </td>
                            <td class="px-4 py-3">
                                <Link :href="`/admin/pages/${page.id}/edit`" class="text-sm font-medium text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">
                                    {{ page.title }}
                                </Link>
                                <span v-if="page.missing_alt_text" title="Images missing alt text" class="ml-1.5 inline-flex items-center text-amber-500 dark:text-amber-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 6a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 6zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <StatusBadgeComponent :status="page.status" />
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400 capitalize">{{ page.template }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ page.author?.name }}</td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ new Date(page.updated_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) }}</div>
                                <div class="text-xs text-gray-400 dark:text-gray-500">{{ page.editor?.name || page.author?.name }}</div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <a v-if="page.status === 'published' && page.slug" :href="`/${page.slug}`" target="_blank"
                                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-3">View</a>
                                <Link v-if="can('edit pages')" :href="`/admin/pages/${page.id}/edit`"
                                    class="text-sm text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300 mr-3">Edit</Link>
                                <button v-if="can('create pages')" @click="router.post(`/admin/pages/${page.id}/duplicate`)"
                                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-3" title="Duplicate">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                <button v-if="can('delete pages')" @click="deletePage(page.id)"
                                    class="text-sm text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <EmptyStateComponent v-else title="No pages found" description="Get started by creating a new page.">
                    <template #action>
                        <Link v-if="can('create pages')" href="/admin/pages/create"
                            class="inline-flex items-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700">
                            Add Page
                        </Link>
                    </template>
                </EmptyStateComponent>
            </div>

            <PaginationComponent :links="pages.links" :last-page="pages.last_page" />
        </div>

        <ConfirmModalComponent
            :show="confirmModal"
            :title="confirmTitle"
            :message="confirmMessage"
            :confirm-text="confirmButtonText"
            @confirm="onConfirm"
            @cancel="confirmModal = false"
        />
    </AdminLayout>
</template>
