<script setup>
defineProps({
    data: { type: Object, required: true },
});
</script>

<template>
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <blockquote class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm text-center">
            <p v-if="data.quote" class="text-lg italic text-gray-700 leading-relaxed">"{{ data.quote }}"</p>
            <div class="mt-6 flex items-center justify-center gap-4">
                <img
                    v-if="data.avatar"
                    :src="data.avatar"
                    :alt="data.author || ''"
                    class="h-12 w-12 rounded-full object-cover"
                />
                <div class="text-left">
                    <div v-if="data.author" class="font-semibold text-gray-900">{{ data.author }}</div>
                    <div v-if="data.role" class="text-sm text-gray-500">{{ data.role }}</div>
                </div>
            </div>
        </blockquote>
    </div>
</template>
