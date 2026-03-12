<script setup>
import { ref } from 'vue';

defineProps({
    metaTitle: { type: String, default: '' },
    metaDescription: { type: String, default: '' },
    ogImage: { type: String, default: '' },
});

defineEmits(['update:metaTitle', 'update:metaDescription', 'update:ogImage']);

const open = ref(false);
</script>

<template>
    <div class="rounded-md border border-gray-200 dark:border-gray-700">
        <button
            type="button"
            @click="open = !open"
            class="flex w-full items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
        >
            <span>SEO Settings</span>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div v-if="open" class="border-t border-gray-200 dark:border-gray-700 px-4 py-4 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meta Title</label>
                <input
                    type="text"
                    :value="metaTitle"
                    @input="$emit('update:metaTitle', $event.target.value)"
                    maxlength="255"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm dark:text-gray-100 shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none"
                />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meta Description</label>
                <textarea
                    :value="metaDescription"
                    @input="$emit('update:metaDescription', $event.target.value)"
                    maxlength="500"
                    rows="3"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm dark:text-gray-100 shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none"
                />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">OG Image URL</label>
                <input
                    type="text"
                    :value="ogImage"
                    @input="$emit('update:ogImage', $event.target.value)"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm dark:text-gray-100 shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none"
                />
            </div>
        </div>
    </div>
</template>
