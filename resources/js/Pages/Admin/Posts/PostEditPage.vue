<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import SlugInputComponent from '@/Components/Admin/Forms/SlugInputComponent.vue';
import SeoPanelComponent from '@/Components/Admin/Forms/SeoPanelComponent.vue';
import PublishPanelComponent from '@/Components/Admin/Forms/PublishPanelComponent.vue';
import MediaPickerComponent from '@/Components/Admin/Forms/MediaPickerComponent.vue';
import TagInputComponent from '@/Components/Admin/Forms/TagInputComponent.vue';
import CategoryInputComponent from '@/Components/Admin/Forms/CategoryInputComponent.vue';
import PageBuilderComponent from '@/Components/Admin/PageBuilder/PageBuilderComponent.vue';
import RevisionHistoryComponent from '@/Components/Admin/Shared/RevisionHistoryComponent.vue';

const props = defineProps({
    post: Object,
    categories: Array,
    tags: Array,
    blockTypes: Object,
});

function toBlocks(content) {
    if (Array.isArray(content) && content.length && content[0]?.id && content[0]?.type) {
        return content;
    }
    if (content && typeof content === 'object' && content.type === 'doc') {
        return [{ id: 'blk_migrated', type: 'richText', data: { content } }];
    }
    return [];
}

const form = useForm({
    title: props.post.title,
    slug: props.post.slug || '',
    content: toBlocks(props.post.content),
    excerpt: props.post.excerpt || '',
    status: props.post.status,
    published_at: props.post.published_at ? props.post.published_at.slice(0, 16) : '',
    featured_image: props.post.featured_image || '',
    category_ids: props.post.categories?.map(c => c.id) || [],
    tag_ids: props.post.tags?.map(t => t.id) || [],
    meta_title: props.post.meta_title || '',
    meta_description: props.post.meta_description || '',
});

const submit = () => {
    form.put(`/admin/posts/${props.post.id}`);
};
</script>

<template>
    <Head :title="`Edit: ${post.title}`" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[
                { label: 'Dashboard', href: '/admin' },
                { label: 'Posts', href: '/admin/posts' },
                { label: post.title },
            ]" />
        </template>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Post</h1>
                <a v-if="post.status === 'published' && post.slug" :href="`/blog/${post.slug}`" target="_blank"
                    class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    View on site
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                        <input v-model="form.title" type="text" required autofocus
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                            :class="{ 'border-red-500': form.errors.title }" />
                        <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                    </div>

                    <SlugInputComponent v-model="form.slug" :title="form.title" prefix="/blog" />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content</label>
                        <PageBuilderComponent v-model="form.content" :block-types="blockTypes" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Excerpt</label>
                        <textarea v-model="form.excerpt" rows="3" maxlength="500"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100" />
                    </div>

                    <SeoPanelComponent
                        v-model:meta-title="form.meta_title"
                        v-model:meta-description="form.meta_description"
                    />
                </div>

                <div class="space-y-5">
                    <PublishPanelComponent
                        v-model:status="form.status"
                        v-model:published-at="form.published_at"
                        :processing="form.processing"
                        publish-permission="publish posts"
                    />

                    <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 space-y-4">
                        <CategoryInputComponent v-model="form.category_ids" :available-categories="categories" />
                        <TagInputComponent v-model="form.tag_ids" :available-tags="tags" />
                    </div>

                    <MediaPickerComponent v-model="form.featured_image" />

                    <RevisionHistoryComponent type="posts" :id="post.id" />
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
