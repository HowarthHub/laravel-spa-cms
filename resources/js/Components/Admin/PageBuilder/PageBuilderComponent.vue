<script setup>
import { ref, computed } from 'vue';
import draggable from 'vuedraggable';
import BlockItemComponent from './BlockItemComponent.vue';
import BlockTypePickerComponent from './BlockTypePickerComponent.vue';

const props = defineProps({
    modelValue: { default: () => [] },
    blockTypes: { type: Object, required: true },
});

const emit = defineEmits(['update:modelValue']);

const blocks = computed({
    get: () => Array.isArray(props.modelValue) ? props.modelValue : [],
    set: (value) => emit('update:modelValue', value),
});

const showPicker = ref(false);

function generateId() {
    return 'blk_' + Math.random().toString(36).substr(2, 9);
}

const defaultData = {
    hero: () => ({ heading: '', subheading: '', backgroundImage: '', ctaText: '', ctaLink: '' }),
    richText: () => ({ content: null }),
    image: () => ({ src: '', alt: '', caption: '' }),
    twoColumn: () => ({ left: null, right: null }),
    threeColumn: () => ({ columns: [null, null, null] }),
    callToAction: () => ({ heading: '', text: '', buttonText: '', buttonLink: '', variant: 'blue' }),
    featureGrid: () => ({ features: [{ icon: '', title: '', description: '' }] }),
    testimonial: () => ({ quote: '', author: '', role: '', avatar: '' }),
    spacer: () => ({ size: 'medium' }),
    checklist: () => ({ heading: '', items: [''] }),
};

function addBlock(type) {
    emit('update:modelValue', [
        ...blocks.value,
        { id: generateId(), type, data: defaultData[type]() },
    ]);
}

function updateBlockData(index, data) {
    const updated = blocks.value.map((block, i) =>
        i === index ? { ...block, data } : block
    );
    emit('update:modelValue', updated);
}

function duplicateBlock(index) {
    const source = blocks.value[index];
    const duplicate = {
        id: generateId(),
        type: source.type,
        data: JSON.parse(JSON.stringify(source.data)),
    };
    const updated = [...blocks.value];
    updated.splice(index + 1, 0, duplicate);
    emit('update:modelValue', updated);
}

function removeBlock(index) {
    emit('update:modelValue', blocks.value.filter((_, i) => i !== index));
}

function extractTextFromValue(value) {
    if (!value) {
        return '';
    }
    if (typeof value === 'string') {
        return value.replace(/<[^>]*>/g, ' ');
    }
    if (Array.isArray(value)) {
        return value.map(extractTextFromValue).join(' ');
    }
    if (typeof value === 'object') {
        if (value.type && value.content) {
            return extractTextFromValue(value.content);
        }
        if (value.text) {
            return value.text;
        }
        return Object.values(value).map(extractTextFromValue).join(' ');
    }
    return '';
}

const wordCount = computed(() => {
    if (!blocks.value.length) {
        return 0;
    }

    const text = blocks.value
        .map((block) => extractTextFromValue(block.data))
        .join(' ');

    return text.split(/\s+/).filter(Boolean).length;
});
</script>

<template>
    <div class="space-y-3">
        <draggable
            v-model="blocks"
            item-key="id"
            handle=".block-drag-handle"
            ghost-class="opacity-30"
            class="space-y-3"
        >
            <template #item="{ element, index }">
                <BlockItemComponent
                    :block="element"
                    :label="blockTypes[element.type]?.label || element.type"
                    @update:data="updateBlockData(index, $event)"
                    @duplicate="duplicateBlock(index)"
                    @remove="removeBlock(index)"
                />
            </template>
        </draggable>

        <div v-if="!blocks.length" class="rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 p-8 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">No blocks yet. Add your first block to start building the page.</p>
        </div>

        <button
            type="button"
            @click="showPicker = true"
            class="w-full rounded-md border-2 border-dashed border-gray-300 dark:border-gray-600 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 transition-colors hover:border-cyan-500 hover:text-cyan-600 dark:hover:text-cyan-400"
        >
            + Add Block
        </button>

        <p v-if="blocks.length" class="text-xs text-gray-400 dark:text-gray-500 text-right">
            ~{{ wordCount.toLocaleString() }} words
        </p>

        <BlockTypePickerComponent
            :block-types="blockTypes"
            :show="showPicker"
            @select="addBlock"
            @close="showPicker = false"
        />
    </div>
</template>
