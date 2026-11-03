<script setup>
import { computed } from 'vue';

const props = defineProps({
    data: { type: Object, required: true },
});

const spacerClass = computed(() => {
    switch (props.data.size) {
        case 'small': return 'h-8';
        case 'large': return 'h-24';
        default: return 'h-16';
    }
});
</script>

<template>
    <div :class="spacerClass" aria-hidden="true"></div>
</template>
