<script setup>
import MediaPickerComponent from '@/Components/Admin/Forms/MediaPickerComponent.vue';

const props = defineProps({
    modelValue: { type: Object, required: true },
});

const emit = defineEmits(['update:modelValue']);

function update(key, value) {
    emit('update:modelValue', { ...props.modelValue, [key]: value });
}
</script>

<template>
    <div class="space-y-4">
        <MediaPickerComponent :modelValue="modelValue.src" @update:modelValue="update('src', $event)" label="Image" />

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alt Text</label>
            <input
                :value="modelValue.alt"
                @input="update('alt', $event.target.value)"
                type="text"
                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
            />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Caption</label>
            <input
                :value="modelValue.caption"
                @input="update('caption', $event.target.value)"
                type="text"
                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
            />
        </div>
    </div>
</template>
