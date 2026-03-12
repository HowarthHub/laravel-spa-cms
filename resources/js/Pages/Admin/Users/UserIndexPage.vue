<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import EmptyStateComponent from '@/Components/Admin/Shared/EmptyStateComponent.vue';
import { usePermissions } from '@/Composables/usePermissions.js';

const { can } = usePermissions();

const props = defineProps({
    users: Object,
    filters: Object,
    roles: Array,
});

const search = ref(props.filters?.search || '');
const role = ref(props.filters?.role || '');

const applyFilters = () => {
    router.get('/admin/users', {
        search: search.value || undefined,
        role: role.value || undefined,
    }, { preserveState: true, replace: true });
};

let searchTimeout = null;
watch(search, () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(applyFilters, 300); });
watch(role, applyFilters);

const deleteUser = (id) => {
    if (!confirm('Delete this user?')) return;
    router.delete(`/admin/users/${id}`);
};

const roleBadge = (r) => ({
    'super-admin': 'bg-red-100 text-red-700',
    'admin': 'bg-purple-100 text-purple-700',
    'editor': 'bg-blue-100 text-blue-700',
    'viewer': 'bg-gray-100 text-gray-700',
})[r] || 'bg-gray-100 text-gray-700';
</script>

<template>
    <Head title="Users" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[{ label: 'Dashboard', href: '/admin' }, { label: 'Users' }]" />
        </template>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">Users</h1>
                <Link v-if="can('manage users')" href="/admin/users/create"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                    Add User
                </Link>
            </div>

            <div class="flex items-center gap-3">
                <input v-model="search" type="text" placeholder="Search name or email..."
                    class="w-64 rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                <select v-model="role"
                    class="rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                    <option value="">All Roles</option>
                    <option v-for="r in roles" :key="r" :value="r">{{ r }}</option>
                </select>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow">
                <table v-if="users.data.length" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Last Login</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <Link :href="`/admin/users/${user.id}/edit`" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    {{ user.name }}
                                </Link>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ user.email }}</td>
                            <td class="px-4 py-3">
                                <span v-for="r in user.roles" :key="r.id" :class="roleBadge(r.name)"
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium capitalize">
                                    {{ r.name }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="user.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium">
                                    {{ user.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ user.last_login_at ? new Date(user.last_login_at).toLocaleDateString() : 'Never' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <Link v-if="can('manage users')" :href="`/admin/users/${user.id}/edit`"
                                    class="text-sm text-indigo-600 hover:text-indigo-500 mr-3">Edit</Link>
                                <button v-if="can('manage users')" @click="deleteUser(user.id)"
                                    class="text-sm text-red-600 hover:text-red-500">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <EmptyStateComponent v-else title="No users found" description="Create a user to get started.">
                    <template #action>
                        <Link v-if="can('manage users')" href="/admin/users/create"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                            Add User
                        </Link>
                    </template>
                </EmptyStateComponent>
            </div>

            <div v-if="users.last_page > 1" class="flex justify-center gap-1">
                <Link v-for="link in users.links" :key="link.label" :href="link.url || '#'" v-html="link.label"
                    class="rounded-md px-3 py-1 text-sm"
                    :class="link.active ? 'bg-indigo-600 text-white' : link.url ? 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' : 'bg-white text-gray-300 border border-gray-200 cursor-default'" />
            </div>
        </div>
    </AdminLayout>
</template>
