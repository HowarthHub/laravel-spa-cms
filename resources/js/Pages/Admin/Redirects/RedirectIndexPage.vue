<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import EmptyStateComponent from '@/Components/Admin/Shared/EmptyStateComponent.vue';
import PaginationComponent from '@/Components/Admin/Shared/PaginationComponent.vue';
import ConfirmModalComponent from '@/Components/Admin/Shared/ConfirmModalComponent.vue';
import { usePermissions } from '@/Composables/usePermissions.js';

const { can } = usePermissions();

const props = defineProps({
    redirects: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

const applyFilters = () => {
    router.get('/admin/redirects', {
        search: search.value || undefined,
    }, { preserveState: true, replace: true });
};

let searchTimeout = null;
watch(search, () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(applyFilters, 300); });

// Create form
const showCreateForm = ref(false);
const createForm = useForm({
    source_path: '',
    destination_path: '',
    status_code: 301,
    is_active: true,
});

const submitCreate = () => {
    createForm.post('/admin/redirects', {
        preserveScroll: true,
        onSuccess: () => {
            createForm.reset();
            showCreateForm.value = false;
        },
    });
};

// Edit form
const editingId = ref(null);
const editForm = useForm({
    source_path: '',
    destination_path: '',
    status_code: 301,
    is_active: true,
});

const startEdit = (redirect) => {
    editingId.value = redirect.id;
    editForm.source_path = redirect.source_path;
    editForm.destination_path = redirect.destination_path;
    editForm.status_code = redirect.status_code;
    editForm.is_active = redirect.is_active;
};

const cancelEdit = () => {
    editingId.value = null;
    editForm.reset();
    editForm.clearErrors();
};

const submitEdit = (id) => {
    editForm.put(`/admin/redirects/${id}`, {
        preserveScroll: true,
        onSuccess: () => {
            editingId.value = null;
            editForm.reset();
        },
    });
};

// Toggle active
const toggleActive = (redirect) => {
    router.put(`/admin/redirects/${redirect.id}`, {
        source_path: redirect.source_path,
        destination_path: redirect.destination_path,
        status_code: redirect.status_code,
        is_active: !redirect.is_active,
    }, { preserveScroll: true });
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

const deleteRedirect = (id) => {
    showConfirm('Delete Redirect', 'Are you sure you want to delete this redirect?', () => {
        router.delete(`/admin/redirects/${id}`, { preserveScroll: true });
    });
};
</script>

<template>
    <Head title="Redirects" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[{ label: 'Dashboard', href: '/admin' }, { label: 'Redirects' }]" />
        </template>

        <div class="space-y-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Redirects</h1>
                <button
                    v-if="can('manage redirects')"
                    @click="showCreateForm = !showCreateForm"
                    class="inline-flex items-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700"
                >
                    {{ showCreateForm ? 'Cancel' : 'Add Redirect' }}
                </button>
            </div>

            <!-- Create Form -->
            <div v-if="showCreateForm" class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow p-4">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">New Redirect</h2>
                <form @submit.prevent="submitCreate" class="grid grid-cols-1 gap-3 sm:grid-cols-12">
                    <div class="sm:col-span-4">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Source Path</label>
                        <input v-model="createForm.source_path" type="text" placeholder="/old-page"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100" />
                        <p v-if="createForm.errors.source_path" class="mt-1 text-xs text-red-600">{{ createForm.errors.source_path }}</p>
                    </div>
                    <div class="sm:col-span-4">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Destination Path</label>
                        <input v-model="createForm.destination_path" type="text" placeholder="/new-page"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100" />
                        <p v-if="createForm.errors.destination_path" class="mt-1 text-xs text-red-600">{{ createForm.errors.destination_path }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status Code</label>
                        <select v-model="createForm.status_code"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100">
                            <option :value="301">301 (Permanent)</option>
                            <option :value="302">302 (Temporary)</option>
                        </select>
                        <p v-if="createForm.errors.status_code" class="mt-1 text-xs text-red-600">{{ createForm.errors.status_code }}</p>
                    </div>
                    <div class="sm:col-span-2 flex items-end">
                        <button type="submit" :disabled="createForm.processing"
                            class="w-full inline-flex justify-center items-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700 disabled:opacity-50">
                            {{ createForm.processing ? 'Saving...' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <input v-model="search" type="text" placeholder="Search redirects..."
                    class="w-full sm:w-64 rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100" />
            </div>

            <div class="overflow-x-auto rounded-lg bg-white dark:bg-gray-800 shadow">
                <table v-if="redirects.data.length" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Source</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Destination</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Code</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Active</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Hits</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template v-for="redirect in redirects.data" :key="redirect.id">
                            <!-- View row -->
                            <tr v-if="editingId !== redirect.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ redirect.source_path }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ redirect.destination_path }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                        :class="redirect.status_code === 301
                                            ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
                                            : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400'">
                                        {{ redirect.status_code }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <button v-if="can('manage redirects')" @click="toggleActive(redirect)"
                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                        :class="redirect.is_active ? 'bg-cyan-600' : 'bg-gray-300 dark:bg-gray-600'">
                                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            :class="redirect.is_active ? 'translate-x-4' : 'translate-x-0'" />
                                    </button>
                                    <span v-else class="text-sm text-gray-500 dark:text-gray-400">{{ redirect.is_active ? 'Yes' : 'No' }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ redirect.hit_count }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <button v-if="can('manage redirects')" @click="startEdit(redirect)"
                                        class="text-sm text-cyan-600 hover:text-cyan-500 mr-3">Edit</button>
                                    <button v-if="can('manage redirects')" @click="deleteRedirect(redirect.id)"
                                        class="text-sm text-red-600 hover:text-red-500">Delete</button>
                                </td>
                            </tr>

                            <!-- Edit row -->
                            <tr v-else class="bg-cyan-50/50 dark:bg-cyan-900/10">
                                <td class="px-4 py-3">
                                    <input v-model="editForm.source_path" type="text"
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100" />
                                    <p v-if="editForm.errors.source_path" class="mt-1 text-xs text-red-600">{{ editForm.errors.source_path }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <input v-model="editForm.destination_path" type="text"
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100" />
                                    <p v-if="editForm.errors.destination_path" class="mt-1 text-xs text-red-600">{{ editForm.errors.destination_path }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <select v-model="editForm.status_code"
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100">
                                        <option :value="301">301</option>
                                        <option :value="302">302</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <button @click="editForm.is_active = !editForm.is_active"
                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                        :class="editForm.is_active ? 'bg-cyan-600' : 'bg-gray-300 dark:bg-gray-600'">
                                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            :class="editForm.is_active ? 'translate-x-4' : 'translate-x-0'" />
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ redirect.hit_count }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <button @click="submitEdit(redirect.id)" :disabled="editForm.processing"
                                        class="text-sm text-cyan-600 hover:text-cyan-500">Save</button>
                                    <button @click="cancelEdit"
                                        class="text-sm text-gray-500 hover:text-gray-400">Cancel</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <EmptyStateComponent v-else title="No redirects found" description="Create a redirect to forward old URLs to new destinations.">
                    <template #action>
                        <button v-if="can('manage redirects')" @click="showCreateForm = true"
                            class="inline-flex items-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700">
                            Add Redirect
                        </button>
                    </template>
                </EmptyStateComponent>
            </div>

            <PaginationComponent :links="redirects.links" :last-page="redirects.last_page" />
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
