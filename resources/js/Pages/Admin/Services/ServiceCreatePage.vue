<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import SlugInputComponent from '@/Components/Admin/Forms/SlugInputComponent.vue';
import SeoPanelComponent from '@/Components/Admin/Forms/SeoPanelComponent.vue';
import PublishPanelComponent from '@/Components/Admin/Forms/PublishPanelComponent.vue';
import MediaPickerComponent from '@/Components/Admin/Forms/MediaPickerComponent.vue';
import PageBuilderComponent from '@/Components/Admin/PageBuilder/PageBuilderComponent.vue';

const props = defineProps({
    blockTypes: Object,
});

const form = useForm({
    title: '',
    slug: '',
    short_description: '',
    content: [],
    icon: '',
    featured_image: '',
    cta_text: '',
    cta_link: '',
    sort_order: 0,
    status: 'draft',
    published_at: '',
    meta_title: '',
    meta_description: '',
    og_image: '',
});

const shortDescriptionCount = computed(() => (form.short_description || '').length);

const submit = () => {
    form.post('/admin/services');
};
</script>

<template>
    <Head title="Create Service" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[
                { label: 'Dashboard', href: '/admin' },
                { label: 'Services', href: '/admin/services' },
                { label: 'Create' },
            ]" />
        </template>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Create Service</h1>
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

                    <SlugInputComponent v-model="form.slug" :title="form.title" prefix="/services" />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Short Description</label>
                        <textarea
                            v-model="form.short_description"
                            rows="3"
                            maxlength="500"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                            :class="{ 'border-red-500': form.errors.short_description }"
                        />
                        <div class="mt-1 flex justify-between">
                            <p v-if="form.errors.short_description" class="text-sm text-red-600">{{ form.errors.short_description }}</p>
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-auto">{{ shortDescriptionCount }}/500</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content</label>
                        <PageBuilderComponent v-model="form.content" :block-types="blockTypes" />
                        <p v-if="form.errors.content" class="mt-1 text-sm text-red-600">{{ form.errors.content }}</p>
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
                        publish-permission="manage services"
                    />

                    <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Icon</label>
                            <input
                                v-model="form.icon"
                                type="text"
                                placeholder="e.g. briefcase, code, chart-bar"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                            />
                            <p v-if="form.errors.icon" class="mt-1 text-sm text-red-600">{{ form.errors.icon }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
                            <input
                                v-model.number="form.sort_order"
                                type="number"
                                min="0"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                            />
                            <p v-if="form.errors.sort_order" class="mt-1 text-sm text-red-600">{{ form.errors.sort_order }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CTA Text</label>
                            <input
                                v-model="form.cta_text"
                                type="text"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                            />
                            <p v-if="form.errors.cta_text" class="mt-1 text-sm text-red-600">{{ form.errors.cta_text }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CTA Link</label>
                            <input
                                v-model="form.cta_link"
                                type="text"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                            />
                            <p v-if="form.errors.cta_link" class="mt-1 text-sm text-red-600">{{ form.errors.cta_link }}</p>
                        </div>
                    </div>

                    <MediaPickerComponent v-model="form.featured_image" />
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
