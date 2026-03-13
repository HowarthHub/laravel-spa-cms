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
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quote</label>
            <textarea
                :value="modelValue.quote"
                @input="update('quote', $event.target.value)"
                rows="3"
                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
            />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Author</label>
                <input
                    :value="modelValue.author"
                    @input="update('author', $event.target.value)"
                    type="text"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                <input
                    :value="modelValue.role"
                    @input="update('role', $event.target.value)"
                    type="text"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                />
            </div>
        </div>

        <MediaPickerComponent :modelValue="modelValue.avatar" @update:modelValue="update('avatar', $event)" label="Avatar" />
    </div>
</template>
