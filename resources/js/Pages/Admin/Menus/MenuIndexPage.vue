<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import EmptyStateComponent from '@/Components/Admin/Shared/EmptyStateComponent.vue';
import { usePermissions } from '@/Composables/usePermissions.js';

const { can } = usePermissions();

const props = defineProps({
    menus: Array,
});

const deleteMenu = (id) => {
    if (!confirm('Delete this menu and all its items?')) return;
    router.delete(`/admin/menus/${id}`);
};
</script>

<template>
    <Head title="Menus" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[{ label: 'Dashboard', href: '/admin' }, { label: 'Menus' }]" />
        </template>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">Menus</h1>
                <Link
                    v-if="can('manage menus')"
                    href="/admin/menus/create"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                >
                    Add Menu
                </Link>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow">
                <table v-if="menus.length" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Handle</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Items</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="menu in menus" :key="menu.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <Link :href="`/admin/menus/${menu.id}/edit`" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    {{ menu.name }}
                                </Link>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ menu.handle }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ menu.items_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <Link v-if="can('manage menus')" :href="`/admin/menus/${menu.id}/edit`"
                                    class="text-sm text-indigo-600 hover:text-indigo-500 mr-3">Edit</Link>
                                <button v-if="can('manage menus')" @click="deleteMenu(menu.id)"
                                    class="text-sm text-red-600 hover:text-red-500">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <EmptyStateComponent v-else title="No menus found" description="Create a menu to manage your site navigation.">
                    <template #action>
                        <Link v-if="can('manage menus')" href="/admin/menus/create"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                            Add Menu
                        </Link>
                    </template>
                </EmptyStateComponent>
            </div>
        </div>
    </AdminLayout>
</template>
