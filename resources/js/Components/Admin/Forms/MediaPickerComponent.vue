<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    label: { type: String, default: 'Featured Image' },
});

const emit = defineEmits(['update:modelValue']);

const showModal = ref(false);
const items = ref([]);
const loading = ref(false);
const uploading = ref(false);
const search = ref('');
const page = ref(1);
const lastPage = ref(1);
const dragOver = ref(false);

const fetchMedia = async () => {
    loading.value = true;
    try {
        const params = new URLSearchParams();
        if (search.value) params.append('search', search.value);
        params.append('page', page.value);

        const res = await fetch(`/admin/media/browse?${params}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        const json = await res.json();
        items.value = json.data;
        lastPage.value = json.last_page;
    } finally {
        loading.value = false;
    }
};

const openPicker = () => {
    showModal.value = true;
    page.value = 1;
    search.value = '';
    fetchMedia();
};

let searchTimeout = null;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        page.value = 1;
        fetchMedia();
    }, 300);
});

const selectItem = (item) => {
    const url = item.media?.[0]?.original_url || item.url;
    emit('update:modelValue', url);
    showModal.value = false;
};

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
        await fetchMedia();
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
    dragOver.value = false;
    if (e.dataTransfer.files.length) {
        uploadFiles(e.dataTransfer.files);
    }
};

const changePage = (p) => {
    page.value = p;
    fetchMedia();
};

const formatSize = (bytes) => {
    if (!bytes) return '';
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
};
</script>

<template>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ label }}</label>
        <div class="flex items-center gap-3">
            <div v-if="modelValue" class="relative">
                <img :src="modelValue" class="h-20 w-20 rounded-md object-cover border border-gray-200" />
                <button
                    type="button"
                    @click="$emit('update:modelValue', '')"
                    class="absolute -right-1 -top-1 rounded-full bg-red-500 p-0.5 text-white hover:bg-red-600"
                >
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <button
                type="button"
                @click="openPicker"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
            >
                {{ modelValue ? 'Change Image' : 'Select Image' }}
            </button>
        </div>

        <!-- Modal -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>

                <div class="relative z-10 w-full max-w-4xl rounded-lg bg-white shadow-xl max-h-[85vh] flex flex-col">
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b px-5 py-3">
                        <h2 class="text-lg font-semibold text-gray-900">Media Library</h2>
                        <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Toolbar -->
                    <div class="flex items-center gap-3 border-b px-5 py-3">
                        <input v-model="search" type="text" placeholder="Search media..."
                            class="w-64 rounded-md border border-gray-300 px-3 py-1.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />

                        <label class="ml-auto inline-flex cursor-pointer items-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                            <span v-if="uploading">Uploading...</span>
                            <span v-else>Upload</span>
                            <input type="file" multiple accept="image/*,.pdf" class="hidden" @change="onFileInput" :disabled="uploading" />
                        </label>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-5">
                        <!-- Drop zone -->
                        <div
                            v-if="!items.length && !loading"
                            class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed p-12 text-center transition-colors"
                            :class="dragOver ? 'border-indigo-400 bg-indigo-50' : 'border-gray-300'"
                            @dragover.prevent="dragOver = true"
                            @dragleave="dragOver = false"
                            @drop.prevent="onDrop"
                        >
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">Drag and drop files here, or click Upload</p>
                        </div>

                        <!-- Loading -->
                        <div v-else-if="loading" class="flex items-center justify-center py-12">
                            <svg class="h-8 w-8 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>

                        <!-- Grid -->
                        <div
                            v-else
                            class="grid grid-cols-4 gap-3 sm:grid-cols-5 md:grid-cols-6"
                            @dragover.prevent="dragOver = true"
                            @dragleave="dragOver = false"
                            @drop.prevent="onDrop"
                        >
                            <button
                                v-for="item in items"
                                :key="item.id"
                                type="button"
                                @click="selectItem(item)"
                                class="group relative aspect-square overflow-hidden rounded-md border border-gray-200 hover:ring-2 hover:ring-indigo-500 focus:ring-2 focus:ring-indigo-500 outline-none"
                            >
                                <img
                                    v-if="item.media?.[0]?.mime_type?.startsWith('image/')"
                                    :src="item.media[0].original_url"
                                    :alt="item.title"
                                    class="h-full w-full object-cover"
                                />
                                <div v-else class="flex h-full w-full items-center justify-center bg-gray-100">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent p-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <p class="truncate text-xs text-white">{{ item.title }}</p>
                                    <p v-if="item.media?.[0]?.size" class="text-[10px] text-gray-300">{{ formatSize(item.media[0].size) }}</p>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="lastPage > 1" class="flex items-center justify-center gap-1 border-t px-5 py-3">
                        <button
                            v-for="p in lastPage"
                            :key="p"
                            type="button"
                            @click="changePage(p)"
                            class="rounded-md px-3 py-1 text-sm"
                            :class="p === page ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                        >
                            {{ p }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>
