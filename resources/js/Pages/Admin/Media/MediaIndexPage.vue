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
    media: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const uploading = ref(false);

let searchTimeout = null;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/media', {
            search: search.value || undefined,
        }, { preserveState: true, replace: true });
    }, 300);
});

const uploadFiles = async (files) => {
    uploading.value = true;
    try {
        for (const file of files) {
            const formData = new FormData();
            formData.append('file', file);

            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            await fetch('/admin/media', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(token ? { 'X-CSRF-TOKEN': token } : {}),
                },
                body: formData,
            });
        }
        router.reload({ only: ['media'] });
    } finally {
        uploading.value = false;
    }
};

const onFileInput = (e) => {
    if (e.target.files.length) {
        uploadFiles(e.target.files);
        e.target.value = '';
    }
};

const onDrop = (e) => {
    if (e.dataTransfer.files.length) {
        uploadFiles(e.dataTransfer.files);
    }
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

const deleteMedia = (id) => {
    showConfirm('Delete Media', 'Are you sure you want to delete this media item?', () => {
        router.delete(`/admin/media/${id}`);
    });
};

const formatSize = (bytes) => {
    if (!bytes) return '';
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
};
</script>

<template>
    <Head title="Media Library" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[{ label: 'Dashboard', href: '/admin' }, { label: 'Media' }]" />
        </template>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Media Library</h1>
                <label v-if="can('manage media')"
                    class="inline-flex cursor-pointer items-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700">
                    <span v-if="uploading">Uploading...</span>
                    <span v-else>Upload Files</span>
                    <input type="file" multiple accept="image/*,.pdf" class="hidden" @change="onFileInput" :disabled="uploading" />
                </label>
            </div>

            <div class="flex items-center gap-3">
                <input v-model="search" type="text" placeholder="Search media..."
                    class="w-64 rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100" />
            </div>

            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow p-5">
                <div v-if="media.data.length"
                    class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6"
                    @dragover.prevent
                    @drop.prevent="onDrop"
                >
                    <div
                        v-for="item in media.data"
                        :key="item.id"
                        class="group relative"
                    >
                        <div class="aspect-square overflow-hidden rounded-md border border-gray-200 dark:border-gray-700">
                            <img
                                v-if="item.media?.[0]?.mime_type?.startsWith('image/')"
                                :src="item.media[0].original_url"
                                :alt="item.title"
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center bg-gray-100 dark:bg-gray-700">
                                <svg class="h-10 w-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 truncate text-xs text-gray-600 dark:text-gray-300">{{ item.title }}</p>
                        <p v-if="item.media?.[0]?.size" class="text-[10px] text-gray-400 dark:text-gray-500">{{ formatSize(item.media[0].size) }}</p>

                        <button
                            v-if="can('manage media')"
                            type="button"
                            @click="deleteMedia(item.id)"
                            class="absolute right-1 top-1 rounded-full bg-red-500 p-1 text-white opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600"
                        >
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <EmptyStateComponent
                    v-else
                    title="No media found"
                    description="Upload files by clicking the button above or drag and drop."
                />
            </div>

            <PaginationComponent :links="media.links" :last-page="media.last_page" />
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
