<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import TiptapEditorComponent from '@/Components/Admin/Forms/TiptapEditorComponent.vue';
import SlugInputComponent from '@/Components/Admin/Forms/SlugInputComponent.vue';
import SeoPanelComponent from '@/Components/Admin/Forms/SeoPanelComponent.vue';
import PublishPanelComponent from '@/Components/Admin/Forms/PublishPanelComponent.vue';
import MediaPickerComponent from '@/Components/Admin/Forms/MediaPickerComponent.vue';
import PageBuilderComponent from '@/Components/Admin/PageBuilder/PageBuilderComponent.vue';
import RevisionHistoryComponent from '@/Components/Admin/Shared/RevisionHistoryComponent.vue';

const props = defineProps({
    page: Object,
    templates: Object,
    blockTypes: Object,
    pages: Array,
    forms: Array,
    homepageId: [String, Number],
});

const form = useForm({
    title: props.page.title,
    pre_heading: props.page.pre_heading || '',
    slug: props.page.slug || '',
    content: props.page.content,
    excerpt: props.page.excerpt || '',
    template: props.page.template || 'default',
    status: props.page.status,
    published_at: props.page.published_at ? props.page.published_at.slice(0, 16) : '',
    parent_id: props.page.parent_id || '',
    form_id: props.page.form_id || '',
    featured_image: props.page.featured_image || '',
    meta_title: props.page.meta_title || '',
    meta_description: props.page.meta_description || '',
});

const isHomepage = computed(() => String(props.homepageId) === String(props.page.id));

const slugPrefix = computed(() => {
    if (isHomepage.value) return '';
    if (!form.parent_id) return '';
    const parent = props.pages.find(p => p.id === form.parent_id);
    return parent ? `/${parent.slug}` : '';
});

const pageBuilderTemplates = ['page-builder'];
const usePageBuilder = computed(() => {
    if (pageBuilderTemplates.includes(form.template)) return true;
    return Array.isArray(form.content) && form.content.length > 0 && form.content[0]?.id && form.content[0]?.type;
});

const submit = () => {
    form.put(`/admin/pages/${props.page.id}`);
};
</script>

<template>
    <Head :title="`Edit: ${page.title}`" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[
                { label: 'Dashboard', href: '/admin' },
                { label: 'Pages', href: '/admin/pages' },
                { label: page.title },
            ]" />
        </template>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Page</h1>
                <a v-if="page.status === 'published' && page.slug" :href="`/${page.slug}`" target="_blank"
                    class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    View on site
                </a>
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

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pre-heading</label>
                        <input
                            v-model="form.pre_heading"
                            type="text"
                            placeholder="Defaults to site name if empty"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                            :class="{ 'border-red-500': form.errors.pre_heading }"
                        />
                        <p v-if="form.errors.pre_heading" class="mt-1 text-sm text-red-600">{{ form.errors.pre_heading }}</p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Gold text shown above the page title in the hero banner</p>
                    </div>

                    <div v-if="isHomepage">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug</label>
                        <span class="text-sm text-gray-500 dark:text-gray-400">/</span>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">This page is set as the homepage</p>
                    </div>
                    <SlugInputComponent v-else v-model="form.slug" :title="form.title" :prefix="slugPrefix" />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content</label>
                        <PageBuilderComponent
                            v-if="usePageBuilder"
                            v-model="form.content"
                            :block-types="blockTypes"
                        />
                        <TiptapEditorComponent v-else v-model="form.content" />
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
                                <option v-for="p in pages" :key="p.id" :value="p.id">{{ p.title }}</option>
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

                    <RevisionHistoryComponent type="pages" :id="page.id" />
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
