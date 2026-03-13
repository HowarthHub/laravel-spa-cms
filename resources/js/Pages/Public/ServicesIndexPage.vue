<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ServiceCardComponent from '@/Components/Public/ServiceCardComponent.vue';
import TiptapRendererComponent from '@/Components/Public/TiptapRendererComponent.vue';

defineProps({
    services: {
        type: Array,
        required: true,
    },
    page: {
        type: [Object, null],
        default: null,
    },
    meta: {
        type: Object,
        default: () => ({}),
    },
});
</script>

<template>
    <PublicLayout :meta="meta">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ page?.title || 'Our Services' }}</h1>

            <div v-if="page?.excerpt" class="mb-8 text-lg text-gray-600">
                {{ page.excerpt }}
            </div>

            <div v-if="page?.content" class="mb-12">
                <TiptapRendererComponent :content="page.content" />
            </div>

            <div v-if="services.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <ServiceCardComponent v-for="(service, i) in services" :key="service.id" :service="service" :index="i" />
            </div>

            <p v-else class="text-gray-600">No services available at the moment.</p>
        </div>
    </PublicLayout>
</template>
