<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import TiptapRendererComponent from '@/Components/Public/TiptapRendererComponent.vue';
import BlockRendererComponent from '@/Components/Public/BlockRenderer/BlockRendererComponent.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    service: {
        type: Object,
        required: true,
    },
    meta: {
        type: Object,
        default: () => ({}),
    },
});

const isBlocks = computed(() =>
    Array.isArray(props.service.content) && props.service.content[0]?.id && props.service.content[0]?.type
);
</script>

<template>
    <PublicLayout :meta="meta">
        <article>
            <!-- Featured Image -->
            <div v-if="service.featured_image" class="relative h-64 sm:h-80 lg:h-96 overflow-hidden bg-gray-900">
                <img :src="service.featured_image" :alt="service.title" class="h-full w-full object-cover opacity-70" />
            </div>

            <!-- Header -->
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 pt-12" :class="{ 'pb-12': !isBlocks }">
                <h1 class="text-4xl font-bold text-gray-900 mb-6">{{ service.title }}</h1>

                <p v-if="service.short_description" class="text-lg text-gray-600" :class="{ 'mb-8': !isBlocks }">
                    {{ service.short_description }}
                </p>
            </div>

            <!-- Blocks content (outside constrained wrapper — blocks handle their own widths) -->
            <BlockRendererComponent v-if="isBlocks" :blocks="service.content" />

            <!-- Legacy TipTap content (inside constrained wrapper) -->
            <div v-else-if="service.content" class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <TiptapRendererComponent :content="service.content" />

                <!-- Legacy features -->
                <div v-if="service.features?.length" class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Features</h2>
                    <ul class="space-y-3">
                        <li v-for="(feature, index) in service.features" :key="index" class="flex items-start gap-3">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-gray-700">{{ feature }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Footer (CTA + back link) -->
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 pb-12">
                <!-- CTA -->
                <div v-if="service.cta_text && service.cta_link" class="mt-12">
                    <a :href="service.cta_link" class="inline-flex items-center rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                        {{ service.cta_text }}
                    </a>
                </div>

                <!-- Back Link -->
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <Link href="/services" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                        &larr; Back to Services
                    </Link>
                </div>
            </div>
        </article>
    </PublicLayout>
</template>
