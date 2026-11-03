<script setup>
import TiptapEditorComponent from '@/Components/Admin/Forms/TiptapEditorComponent.vue';

const props = defineProps({
    modelValue: { type: Object, required: true },
});

const emit = defineEmits(['update:modelValue']);

function updateLeft(content) {
    emit('update:modelValue', { ...props.modelValue, left: content });
}

function updateRight(content) {
    emit('update:modelValue', { ...props.modelValue, right: content });
}
</script>

<template>
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Left Column</label>
            <TiptapEditorComponent :modelValue="modelValue.left" @update:modelValue="updateLeft" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Right Column</label>
            <TiptapEditorComponent :modelValue="modelValue.right" @update:modelValue="updateRight" />
        </div>
    </div>
</template>
