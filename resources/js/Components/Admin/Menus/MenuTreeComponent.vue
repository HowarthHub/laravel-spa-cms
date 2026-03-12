<script setup>
import draggable from 'vuedraggable';

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    depth: { type: Number, default: 0 },
});

const emit = defineEmits(['update:modelValue', 'edit', 'remove']);

const onDragUpdate = () => {
    emit('update:modelValue', [...props.modelValue]);
};

const updateChildren = (index, children) => {
    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], children };
    emit('update:modelValue', updated);
};

const removeItem = (index) => {
    const updated = [...props.modelValue];
    updated.splice(index, 1);
    emit('update:modelValue', updated);
};

const editItem = (index) => {
    emit('edit', index, props.depth);
};

const bubbleEdit = (childIndex, childDepth) => {
    emit('edit', childIndex, childDepth);
};
</script>

<template>
    <draggable
        :list="modelValue"
        item-key="id"
        group="menu-items"
        handle=".drag-handle"
        ghost-class="opacity-30"
        @change="onDragUpdate"
        :class="depth > 0 ? 'ml-6 border-l border-gray-200 pl-3' : ''"
    >
        <template #item="{ element, index }">
            <div class="mb-2">
                <div class="flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-2 shadow-sm">
                    <button type="button" class="drag-handle cursor-grab text-gray-400 hover:text-gray-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                        </svg>
                    </button>

                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-900">{{ element.label }}</span>
                        <span class="ml-2 text-xs text-gray-400">
                            {{ element.type === 'custom' ? element.url : element.type }}
                        </span>
                        <span v-if="element.target === '_blank'" class="ml-1 text-xs text-gray-400">(new tab)</span>
                    </div>

                    <button type="button" @click="editItem(index)"
                        class="text-xs text-indigo-600 hover:text-indigo-500">Edit</button>
                    <button type="button" @click="removeItem(index)"
                        class="text-xs text-red-600 hover:text-red-500">Remove</button>
                </div>

                <!-- Nested children -->
                <MenuTreeComponent
                    v-if="element.children?.length || depth < 2"
                    :modelValue="element.children || []"
                    @update:modelValue="updateChildren(index, $event)"
                    @edit="bubbleEdit"
                    @remove="$emit('remove')"
                    :depth="depth + 1"
                />
            </div>
        </template>
    </draggable>
</template>
