<script setup>
const props = defineProps({
    modelValue: { type: Object, required: true },
});

const emit = defineEmits(['update:modelValue']);

function update(key, value) {
    emit('update:modelValue', { ...props.modelValue, [key]: value });
}

function updateItem(index, value) {
    const items = [...(props.modelValue.items || [])];
    items[index] = value;
    update('items', items);
}

function addItem() {
    update('items', [...(props.modelValue.items || []), '']);
}

function removeItem(index) {
    update('items', (props.modelValue.items || []).filter((_, i) => i !== index));
}

function moveItem(index, direction) {
    const items = [...(props.modelValue.items || [])];
    const newIndex = index + direction;
    if (newIndex < 0 || newIndex >= items.length) return;
    const temp = items[index];
    items[index] = items[newIndex];
    items[newIndex] = temp;
    update('items', items);
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
                placeholder="e.g. What's Included"
                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
            />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Items</label>
            <div class="space-y-2">
                <div v-for="(item, index) in (modelValue.items || [])" :key="index" class="flex items-center gap-2">
                    <svg class="h-5 w-5 shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                    <input
                        :value="item"
                        @input="updateItem(index, $event.target.value)"
                        type="text"
                        placeholder="Enter a checklist item..."
                        class="flex-1 rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                    />
                    <button type="button" @click="moveItem(index, -1)" :disabled="index === 0"
                        class="rounded p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 disabled:opacity-30 disabled:cursor-not-allowed"
                        title="Move up">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                    </button>
                    <button type="button" @click="moveItem(index, 1)" :disabled="index === (modelValue.items || []).length - 1"
                        class="rounded p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 disabled:opacity-30 disabled:cursor-not-allowed"
                        title="Move down">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <button type="button" @click="removeItem(index)"
                        class="rounded p-1 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                        title="Remove item">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
            <button type="button" @click="addItem"
                class="mt-2 inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                Add Item
            </button>
        </div>
    </div>
</template>
