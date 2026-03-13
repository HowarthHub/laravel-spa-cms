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
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Heading</label>
            <input
                :value="modelValue.heading"
                @input="update('heading', $event.target.value)"
                type="text"
                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
            />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subheading</label>
            <input
                :value="modelValue.subheading"
                @input="update('subheading', $event.target.value)"
                type="text"
                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
            />
        </div>

        <MediaPickerComponent :modelValue="modelValue.backgroundImage" @update:modelValue="update('backgroundImage', $event)" label="Background Image" />

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CTA Text</label>
                <input
                    :value="modelValue.ctaText"
                    @input="update('ctaText', $event.target.value)"
                    type="text"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CTA Link</label>
                <input
                    :value="modelValue.ctaLink"
                    @input="update('ctaLink', $event.target.value)"
                    type="text"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                />
            </div>
        </div>
    </div>
</template>
