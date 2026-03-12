<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    items: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <nav class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
        <template v-for="(item, index) in items" :key="index">
            <span v-if="index > 0" class="text-gray-300 dark:text-gray-600">/</span>
            <Link
                v-if="item.href"
                :href="item.href"
                class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
            >
                {{ item.label }}
            </Link>
            <span v-else class="text-gray-900 dark:text-white font-medium">{{ item.label }}</span>
        </template>
    </nav>
</template>
