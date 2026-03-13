<script setup>
import TiptapEditorComponent from '@/Components/Admin/Forms/TiptapEditorComponent.vue';

const props = defineProps({
    modelValue: { type: Object, required: true },
});

const emit = defineEmits(['update:modelValue']);

function updateContent(content) {
    emit('update:modelValue', { ...props.modelValue, content });
}
</script>

<template>
    <div>
        <TiptapEditorComponent :modelValue="modelValue.content" @update:modelValue="updateContent" />
    </div>
</template>
