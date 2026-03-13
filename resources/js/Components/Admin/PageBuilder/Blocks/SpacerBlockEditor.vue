<script setup>
const props = defineProps({
    modelValue: { type: Object, required: true },
});

const emit = defineEmits(['update:modelValue']);

function setSize(size) {
    emit('update:modelValue', { ...props.modelValue, size });
}
</script>

<template>
    <div class="flex items-center gap-3">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Size</label>
        <div class="flex gap-2">
            <button
                v-for="size in ['small', 'medium', 'large']"
                :key="size"
                type="button"
                @click="setSize(size)"
                class="rounded-md px-3 py-1.5 text-sm font-medium capitalize transition-colors"
                :class="modelValue.size === size
                    ? 'bg-cyan-600 text-white'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
            >
                {{ size }}
            </button>
        </div>
    </div>
</template>
