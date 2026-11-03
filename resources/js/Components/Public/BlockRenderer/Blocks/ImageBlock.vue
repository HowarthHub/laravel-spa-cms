<script setup>
defineProps({
    data: { type: Object, required: true },
});
</script>

<template>
    <figure v-scroll-animate class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <img
            v-if="data.src"
            :src="data.src"
            :alt="data.alt || ''"
            class="w-full rounded-lg"
        />
        <figcaption v-if="data.caption" class="mt-3 text-center text-sm text-gray-500">{{ data.caption }}</figcaption>
    </figure>
</template>
