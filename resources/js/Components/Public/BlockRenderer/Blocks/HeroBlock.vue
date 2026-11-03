<script setup>
defineProps({
    data: { type: Object, required: true },
});
</script>

<template>
    <div class="relative flex min-h-[400px] items-center justify-center overflow-hidden bg-gray-900">
        <img
            v-if="data.backgroundImage"
            :src="data.backgroundImage"
            alt=""
            class="absolute inset-0 h-full w-full object-cover opacity-50"
        />
        <div v-scroll-animate class="relative z-10 px-4 text-center">
            <h1 v-if="data.heading" class="text-4xl font-bold text-white sm:text-5xl lg:text-6xl">{{ data.heading }}</h1>
            <p v-if="data.subheading" class="mx-auto mt-4 max-w-2xl text-lg text-gray-200 sm:text-xl">{{ data.subheading }}</p>
            <a
                v-if="data.ctaText && data.ctaLink"
                :href="data.ctaLink"
                class="mt-8 inline-block rounded-lg bg-blue-600 px-8 py-3 text-lg font-semibold text-white shadow-lg transition-colors hover:bg-blue-700"
            >
                {{ data.ctaText }}
            </a>
        </div>
    </div>
</template>
