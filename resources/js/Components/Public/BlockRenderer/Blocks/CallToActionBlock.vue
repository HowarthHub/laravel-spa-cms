<script setup>
import { computed } from 'vue';

const props = defineProps({
    data: { type: Object, required: true },
});

const variantClasses = computed(() => {
    switch (props.data.variant) {
        case 'green': return 'bg-green-600 text-white';
        case 'dark': return 'bg-gray-900 text-white';
        default: return 'bg-blue-600 text-white';
    }
});

const buttonClasses = computed(() => {
    switch (props.data.variant) {
        case 'green': return 'bg-white text-green-700 hover:bg-green-50';
        case 'dark': return 'bg-white text-gray-900 hover:bg-gray-100';
        default: return 'bg-white text-blue-700 hover:bg-blue-50';
    }
});
</script>

<template>
    <div :class="variantClasses" class="px-4 py-16 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <h2 v-if="data.heading" class="text-3xl font-bold sm:text-4xl">{{ data.heading }}</h2>
            <p v-if="data.text" class="mx-auto mt-4 max-w-xl text-lg opacity-90">{{ data.text }}</p>
            <a
                v-if="data.buttonText && data.buttonLink"
                :href="data.buttonLink"
                :class="buttonClasses"
                class="mt-8 inline-block rounded-lg px-8 py-3 text-lg font-semibold shadow-lg transition-colors"
            >
                {{ data.buttonText }}
            </a>
        </div>
    </div>
</template>
