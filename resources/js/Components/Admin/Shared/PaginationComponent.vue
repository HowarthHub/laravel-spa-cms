<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: { type: Array, required: true },
    lastPage: { type: Number, required: true },
});
</script>

<template>
    <div v-if="lastPage > 1" class="flex justify-center gap-1">
        <Link
            v-for="link in links"
            :key="link.label"
            :href="link.url || '#'"
            v-html="link.label"
            class="rounded-md px-3 py-1 text-sm"
            :class="link.active
                ? 'bg-cyan-600 text-white'
                : link.url
                    ? 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'
                    : 'bg-white dark:bg-gray-800 text-gray-300 dark:text-gray-600 border border-gray-200 dark:border-gray-700 cursor-default'"
        />
    </div>
</template>
