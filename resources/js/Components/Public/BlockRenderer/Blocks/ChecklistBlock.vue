<script setup>
defineProps({
    data: { type: Object, required: true },
});
</script>

<template>
    <div v-scroll-animate class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <h2 v-if="data.heading" class="text-2xl font-bold text-gray-900 mb-6">{{ data.heading }}</h2>
        <ul v-if="data.items?.length" class="space-y-3">
            <li v-for="(item, index) in data.items" :key="index" class="flex items-start gap-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-700">{{ item }}</span>
            </li>
        </ul>
    </div>
</template>
