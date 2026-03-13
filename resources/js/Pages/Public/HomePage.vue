<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import TiptapRendererComponent from '@/Components/Public/TiptapRendererComponent.vue';

const props = defineProps({
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
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
            <template v-if="page">
                <h1 class="text-4xl font-bold text-gray-900 mb-8">{{ page.title }}</h1>
                <TiptapRendererComponent v-if="page.content" :content="page.content" />
            </template>
            <template v-else>
                <div class="text-center py-20">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">Welcome</h1>
                    <p class="text-lg text-gray-600">Set a homepage in admin settings to customise this page.</p>
                </div>
            </template>
        </div>
    </PublicLayout>
</template>
