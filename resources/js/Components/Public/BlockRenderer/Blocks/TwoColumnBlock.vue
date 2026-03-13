<script setup>
import TiptapRendererComponent from '@/Components/Public/TiptapRendererComponent.vue';

defineProps({
    data: { type: Object, required: true },
});
</script>

<template>
    <div v-scroll-animate class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
            <div>
                <TiptapRendererComponent v-if="data.left" :content="data.left" />
            </div>
            <div>
                <TiptapRendererComponent v-if="data.right" :content="data.right" />
            </div>
        </div>
    </div>
</template>
