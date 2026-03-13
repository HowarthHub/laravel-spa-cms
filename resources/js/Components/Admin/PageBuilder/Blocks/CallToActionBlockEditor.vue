<script setup>
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
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Text</label>
            <textarea
                :value="modelValue.text"
                @input="update('text', $event.target.value)"
                rows="3"
                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
            />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Button Text</label>
                <input
                    :value="modelValue.buttonText"
                    @input="update('buttonText', $event.target.value)"
                    type="text"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Button Link</label>
                <input
                    :value="modelValue.buttonLink"
                    @input="update('buttonLink', $event.target.value)"
                    type="text"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                />
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Variant</label>
            <div class="flex gap-2">
                <button
                    v-for="variant in ['blue', 'green', 'dark']"
                    :key="variant"
                    type="button"
                    @click="update('variant', variant)"
                    class="rounded-md px-3 py-1.5 text-sm font-medium capitalize transition-colors"
                    :class="modelValue.variant === variant
                        ? 'bg-cyan-600 text-white'
                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                >
                    {{ variant }}
                </button>
            </div>
        </div>
    </div>
</template>
