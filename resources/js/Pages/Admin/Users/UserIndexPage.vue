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
    users: Object,
    filters: Object,
    roles: Array,
});

const search = ref(props.filters?.search || '');
const role = ref(props.filters?.role || '');
const sortBy = ref('name');
const sortDir = ref('asc');

const applyFilters = () => {
    router.get('/admin/users', {
        search: search.value || undefined,
        role: role.value || undefined,
        sort: sortBy.value !== 'name' ? sortBy.value : undefined,
        direction: sortDir.value !== 'asc' ? sortDir.value : undefined,
    }, { preserveState: true, replace: true });
};

let searchTimeout = null;
watch(search, () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(applyFilters, 300); });
watch(role, applyFilters);

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

const deleteUser = (id) => {
    showConfirm('Delete User', 'Are you sure you want to delete this user?', () => {
        router.delete(`/admin/users/${id}`);
    });
};

const roleBadge = (r) => ({
    'super-admin': 'bg-red-500 text-white',
    'admin': 'bg-violet-500 text-white',
    'editor': 'bg-cyan-500 text-white',
    'viewer': 'bg-gray-500 text-white',
})[r] || 'bg-gray-500 text-white';
</script>

<template>
    <Head title="Users" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[{ label: 'Dashboard', href: '/admin' }, { label: 'Users' }]" />
        </template>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Users</h1>
                <Link v-if="can('manage users')" href="/admin/users/create"
                    class="inline-flex items-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700">
                    Add User
                </Link>
            </div>

            <div class="flex items-center gap-3">
                <input v-model="search" type="text" placeholder="Search name or email..."
                    class="w-64 rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100" />
                <select v-model="role"
                    class="rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100">
                    <option value="">All Roles</option>
                    <option v-for="r in roles" :key="r" :value="r">{{ r }}</option>
                </select>
            </div>

            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
                <table v-if="users.data.length" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/60">
                        <tr>
                            <th @click="toggleSort('name')" class="cursor-pointer px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 select-none">
                                Name{{ sortIcon('name') }}
                            </th>
                            <th @click="toggleSort('email')" class="cursor-pointer px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 select-none">
                                Email{{ sortIcon('email') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                            <th @click="toggleSort('last_login_at')" class="cursor-pointer px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 select-none">
                                Last Login{{ sortIcon('last_login_at') }}
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3">
                                <Link :href="`/admin/users/${user.id}/edit`" class="text-sm font-medium text-cyan-600 hover:text-cyan-500">
                                    {{ user.name }}
                                </Link>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</td>
                            <td class="px-4 py-3">
                                <span v-for="r in user.roles" :key="r.id" :class="roleBadge(r.name)"
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium capitalize">
                                    {{ r.name }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="user.is_active ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'"
                                    class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold">
                                    {{ user.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ user.last_login_at ? new Date(user.last_login_at).toLocaleDateString() : 'Never' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <Link v-if="can('manage users')" :href="`/admin/users/${user.id}/edit`"
                                    class="text-sm text-cyan-600 hover:text-cyan-500 mr-3">Edit</Link>
                                <button v-if="can('manage users')" @click="deleteUser(user.id)"
                                    class="text-sm text-red-600 hover:text-red-500">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <EmptyStateComponent v-else title="No users found" description="Create a user to get started.">
                    <template #action>
                        <Link v-if="can('manage users')" href="/admin/users/create"
                            class="inline-flex items-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700">
                            Add User
                        </Link>
                    </template>
                </EmptyStateComponent>
            </div>

            <PaginationComponent :links="users.links" :last-page="users.last_page" />
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
