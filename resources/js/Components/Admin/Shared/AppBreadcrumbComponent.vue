<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    items: {
        type: Array,
        required: true,
        // Each item: { label: String, href?: String }
    },
});
</script>

<template>
    <nav class="flex items-center gap-1 text-sm text-gray-500">
        <template v-for="(item, index) in items" :key="index">
            <span v-if="index > 0" class="text-gray-300">/</span>
            <Link
                v-if="item.href"
                :href="item.href"
                class="hover:text-gray-700 transition-colors"
            >
                {{ item.label }}
            </Link>
            <span v-else class="text-gray-900 font-medium">{{ item.label }}</span>
        </template>
    </nav>
</template>
