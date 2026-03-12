<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import TiptapEditorComponent from '@/Components/Admin/Forms/TiptapEditorComponent.vue';
import SlugInputComponent from '@/Components/Admin/Forms/SlugInputComponent.vue';
import SeoPanelComponent from '@/Components/Admin/Forms/SeoPanelComponent.vue';
import PublishPanelComponent from '@/Components/Admin/Forms/PublishPanelComponent.vue';
import MediaPickerComponent from '@/Components/Admin/Forms/MediaPickerComponent.vue';

const props = defineProps({
    templates: Object,
    pages: Array,
    forms: Array,
});

const form = useForm({
    title: '',
    content: null,
    excerpt: '',
    template: 'default',
    status: 'draft',
    published_at: '',
    parent_id: '',
    form_id: '',
    featured_image: '',
    meta_title: '',
    meta_description: '',
    og_image: '',
});

const submit = () => {
    form.post('/admin/pages');
};
</script>

<template>
    <Head title="Create Page" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[
                { label: 'Dashboard', href: '/admin' },
                { label: 'Pages', href: '/admin/pages' },
                { label: 'Create' },
            ]" />
        </template>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Create Page</h1>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                        <input
                            v-model="form.title"
                            type="text"
                            required
                            autofocus
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                            :class="{ 'border-red-500': form.errors.title }"
                        />
                        <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                    </div>

                    <SlugInputComponent v-model="form.slug" :title="form.title" prefix="/pages" />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content</label>
                        <TiptapEditorComponent v-model="form.content" />
                        <p v-if="form.errors.content" class="mt-1 text-sm text-red-600">{{ form.errors.content }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Excerpt</label>
                        <textarea
                            v-model="form.excerpt"
                            rows="3"
                            maxlength="500"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                        />
                    </div>

                    <SeoPanelComponent
                        v-model:meta-title="form.meta_title"
                        v-model:meta-description="form.meta_description"
                        v-model:og-image="form.og_image"
                    />
                </div>

                <!-- Sidebar -->
                <div class="space-y-5">
                    <PublishPanelComponent
                        v-model:status="form.status"
                        v-model:published-at="form.published_at"
                        :processing="form.processing"
                        publish-permission="publish pages"
                    />

                    <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Template</label>
                            <select
                                v-model="form.template"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                            >
                                <option v-for="(label, key) in templates" :key="key" :value="key">{{ label }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Parent Page</label>
                            <select
                                v-model="form.parent_id"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                            >
                                <option value="">None</option>
                                <option v-for="page in pages" :key="page.id" :value="page.id">{{ page.title }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Linked Form</label>
                            <select
                                v-model="form.form_id"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                            >
                                <option value="">None</option>
                                <option v-for="f in forms" :key="f.id" :value="f.id">{{ f.name }}</option>
                            </select>
                        </div>
                    </div>

                    <MediaPickerComponent v-model="form.featured_image" />
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
