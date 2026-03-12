<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import TiptapEditorComponent from '@/Components/Admin/Forms/TiptapEditorComponent.vue';
import SlugInputComponent from '@/Components/Admin/Forms/SlugInputComponent.vue';
import SeoPanelComponent from '@/Components/Admin/Forms/SeoPanelComponent.vue';
import PublishPanelComponent from '@/Components/Admin/Forms/PublishPanelComponent.vue';
import MediaPickerComponent from '@/Components/Admin/Forms/MediaPickerComponent.vue';
import TagInputComponent from '@/Components/Admin/Forms/TagInputComponent.vue';

const props = defineProps({
    categories: Array,
    tags: Array,
});

const form = useForm({
    title: '',
    content: null,
    excerpt: '',
    status: 'draft',
    published_at: '',
    featured_image: '',
    category_ids: [],
    tag_ids: [],
    meta_title: '',
    meta_description: '',
    og_image: '',
});

const submit = () => {
    form.post('/admin/posts');
};
</script>

<template>
    <Head title="Create Post" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[
                { label: 'Dashboard', href: '/admin' },
                { label: 'Posts', href: '/admin/posts' },
                { label: 'Create' },
            ]" />
        </template>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">Create Post</h1>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input v-model="form.title" type="text" required autofocus
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
                            :class="{ 'border-red-500': form.errors.title }" />
                        <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                    </div>

                    <SlugInputComponent v-model="form.slug" :title="form.title" />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                        <TiptapEditorComponent v-model="form.content" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Excerpt</label>
                        <textarea v-model="form.excerpt" rows="3" maxlength="500"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                    </div>

                    <SeoPanelComponent
                        v-model:meta-title="form.meta_title"
                        v-model:meta-description="form.meta_description"
                        v-model:og-image="form.og_image"
                    />
                </div>

                <div class="space-y-5">
                    <PublishPanelComponent
                        v-model:status="form.status"
                        v-model:published-at="form.published_at"
                        :processing="form.processing"
                        publish-permission="publish posts"
                    />

                    <div class="rounded-md border border-gray-200 bg-white p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select v-model="form.category_ids" multiple
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none h-28">
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>

                        <TagInputComponent v-model="form.tag_ids" :available-tags="tags" />
                    </div>

                    <MediaPickerComponent v-model="form.featured_image" />
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
