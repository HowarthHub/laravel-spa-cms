<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    service: {
        type: Object,
        required: true,
    },
    index: {
        type: Number,
        default: 0,
    },
});
</script>

<template>
    <article class="group overflow-hidden rounded-lg border border-gray-200 bg-white transition-shadow hover:shadow-lg animate-fade-in-up" :class="`stagger-${index + 1}`">
        <Link :href="`/services/${service.slug}`">
            <div v-if="service.featured_image" class="aspect-video overflow-hidden">
                <img :src="service.featured_image" :alt="service.title" class="h-full w-full object-cover transition-transform group-hover:scale-105" />
            </div>
        </Link>

        <div class="p-6">
            <div v-if="service.icon" class="mb-4 inline-flex items-center justify-center rounded-lg bg-blue-50 p-3">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path :d="service.icon" />
                </svg>
            </div>

            <Link :href="`/services/${service.slug}`">
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                    {{ service.title }}
                </h3>
            </Link>

            <p v-if="service.short_description" class="mt-2 text-sm text-gray-600 line-clamp-3">
                {{ service.short_description }}
            </p>

            <Link :href="`/services/${service.slug}`" class="mt-4 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                {{ service.cta_text || 'Learn more' }} &rarr;
            </Link>
        </div>
    </article>
</template>
