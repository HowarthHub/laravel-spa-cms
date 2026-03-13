<script setup>
const props = defineProps({
    modelValue: { type: Object, required: true },
});

const emit = defineEmits(['update:modelValue']);

function updateFeature(index, key, value) {
    const features = props.modelValue.features.map((f, i) =>
        i === index ? { ...f, [key]: value } : f
    );
    emit('update:modelValue', { ...props.modelValue, features });
}

function addFeature() {
    const features = [...props.modelValue.features, { icon: '', title: '', description: '' }];
    emit('update:modelValue', { ...props.modelValue, features });
}

function removeFeature(index) {
    const features = props.modelValue.features.filter((_, i) => i !== index);
    emit('update:modelValue', { ...props.modelValue, features });
}
</script>

<template>
    <div class="space-y-4">
        <div
            v-for="(feature, index) in modelValue.features"
            :key="index"
            class="rounded-md border border-gray-200 dark:border-gray-700 p-3 space-y-3"
        >
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Feature {{ index + 1 }}</span>
                <button
                    type="button"
                    @click="removeFeature(index)"
                    class="text-sm text-red-600 hover:text-red-700 dark:text-red-400"
                >
                    Remove
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Icon (emoji or text)</label>
                    <input
                        :value="feature.icon"
                        @input="updateFeature(index, 'icon', $event.target.value)"
                        type="text"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                    />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Title</label>
                    <input
                        :value="feature.title"
                        @input="updateFeature(index, 'title', $event.target.value)"
                        type="text"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                    />
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Description</label>
                <textarea
                    :value="feature.description"
                    @input="updateFeature(index, 'description', $event.target.value)"
                    rows="2"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                />
            </div>
        </div>

        <button
            type="button"
            @click="addFeature"
            class="inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700"
        >
            + Add Feature
        </button>
    </div>
</template>
