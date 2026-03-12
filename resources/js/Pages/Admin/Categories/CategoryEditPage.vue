<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';

const props = defineProps({
    category: Object,
    parentCategories: Array,
});

const form = useForm({
    name: props.category.name,
    description: props.category.description || '',
    parent_id: props.category.parent_id,
    sort_order: props.category.sort_order || 0,
});

const submit = () => {
    form.put(`/admin/categories/${props.category.id}`);
};
</script>

<template>
    <Head :title="`Edit: ${category.name}`" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[
                { label: 'Dashboard', href: '/admin' },
                { label: 'Categories', href: '/admin/categories' },
                { label: category.name },
            ]" />
        </template>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">Edit Category</h1>
                <span class="text-sm text-gray-500">{{ category.posts_count }} post(s)</span>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea v-model="form.description" rows="3" maxlength="1000"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Parent Category</label>
                            <select v-model="form.parent_id"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                                <option :value="null">None (Top Level)</option>
                                <option v-for="cat in parentCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                            <input v-model.number="form.sort_order" type="number" min="0"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" :disabled="form.processing"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50">
                        Update Category
                    </button>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
