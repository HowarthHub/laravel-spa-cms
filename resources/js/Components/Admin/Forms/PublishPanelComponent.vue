<script setup>
import { usePermissions } from '@/Composables/usePermissions.js';

const { can } = usePermissions();

defineProps({
    status: { type: String, default: 'draft' },
    publishedAt: { type: String, default: '' },
    processing: { type: Boolean, default: false },
    publishPermission: { type: String, default: 'publish pages' },
});

defineEmits(['update:status', 'update:publishedAt']);
</script>

<template>
    <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 space-y-4">
        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Publishing</h3>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
            <select
                :value="status"
                @input="$emit('update:status', $event.target.value)"
                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
            >
                <option value="draft">Draft</option>
                <option v-if="can(publishPermission)" value="published">Published</option>
                <option v-if="can(publishPermission)" value="scheduled">Scheduled</option>
            </select>
        </div>

        <div v-if="status === 'scheduled'">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Publish Date</label>
            <input
                type="datetime-local"
                :value="publishedAt"
                @input="$emit('update:publishedAt', $event.target.value)"
                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
            />
        </div>

        <button
            type="submit"
            :disabled="processing"
            class="w-full rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span v-if="processing">Saving...</span>
            <span v-else-if="status === 'published'">Publish</span>
            <span v-else-if="status === 'scheduled'">Schedule</span>
            <span v-else>Save Draft</span>
        </button>
    </div>
</template>
