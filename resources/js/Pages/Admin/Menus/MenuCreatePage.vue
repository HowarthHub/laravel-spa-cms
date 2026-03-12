<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';

const form = useForm({
    name: '',
    handle: '',
});

const submit = () => {
    form.post('/admin/menus');
};
</script>

<template>
    <Head title="Create Menu" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[
                { label: 'Dashboard', href: '/admin' },
                { label: 'Menus', href: '/admin/menus' },
                { label: 'Create' },
            ]" />
        </template>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">Create Menu</h1>
            </div>

            <div class="max-w-2xl space-y-5">
                <div class="rounded-md border border-gray-200 bg-white p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input v-model="form.name" type="text" required autofocus
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
                            :class="{ 'border-red-500': form.errors.name }" />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Handle</label>
                        <input v-model="form.handle" type="text" placeholder="Auto-generated from name if left blank"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none font-mono"
                            :class="{ 'border-red-500': form.errors.handle }" />
                        <p v-if="form.errors.handle" class="mt-1 text-sm text-red-600">{{ form.errors.handle }}</p>
                        <p class="mt-1 text-xs text-gray-400">Used to reference this menu in templates</p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" :disabled="form.processing"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50">
                        Create Menu
                    </button>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
