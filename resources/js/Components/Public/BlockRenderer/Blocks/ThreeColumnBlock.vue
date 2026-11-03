<script setup>
import TiptapRendererComponent from '@/Components/Public/TiptapRendererComponent.vue';

defineProps({
    data: { type: Object, required: true },
});
</script>

<template>
    <div v-scroll-animate class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            <div v-for="(col, index) in data.columns" :key="index">
                <TiptapRendererComponent v-if="col" :content="col" />
            </div>
        </div>
    </div>
</template>
