<script setup>
import TiptapRendererComponent from '@/Components/Public/TiptapRendererComponent.vue';

defineProps({
    data: { type: Object, required: true },
});
</script>

<template>
    <div v-scroll-animate class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <TiptapRendererComponent v-if="data.content" :content="data.content" />
    </div>
</template>
