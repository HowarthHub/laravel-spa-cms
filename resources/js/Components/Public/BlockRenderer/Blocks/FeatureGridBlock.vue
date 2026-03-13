<script setup>
defineProps({
    data: { type: Object, required: true },
});
</script>

<template>
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="(feature, index) in data.features"
                :key="index"
                class="rounded-xl border border-gray-200 bg-white p-6 text-center shadow-sm"
            >
                <div v-if="feature.icon" class="mb-4 text-4xl">{{ feature.icon }}</div>
                <h3 v-if="feature.title" class="text-lg font-semibold text-gray-900">{{ feature.title }}</h3>
                <p v-if="feature.description" class="mt-2 text-sm text-gray-600">{{ feature.description }}</p>
            </div>
        </div>
    </div>
</template>
