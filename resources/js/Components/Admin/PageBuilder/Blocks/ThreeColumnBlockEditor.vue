<script setup>
import TiptapEditorComponent from '@/Components/Admin/Forms/TiptapEditorComponent.vue';

const props = defineProps({
    modelValue: { type: Object, required: true },
});

const emit = defineEmits(['update:modelValue']);

function updateColumn(index, content) {
    const columns = [...props.modelValue.columns];
    columns[index] = content;
    emit('update:modelValue', { ...props.modelValue, columns });
}
</script>

<template>
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div v-for="(col, index) in modelValue.columns" :key="index">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Column {{ index + 1 }}</label>
            <TiptapEditorComponent :modelValue="col" @update:modelValue="updateColumn(index, $event)" />
        </div>
    </div>
</template>
