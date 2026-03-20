<script setup>
import { ref } from 'vue';
import HeroBlockEditor from './Blocks/HeroBlockEditor.vue';
import RichTextBlockEditor from './Blocks/RichTextBlockEditor.vue';
import ImageBlockEditor from './Blocks/ImageBlockEditor.vue';
import TwoColumnBlockEditor from './Blocks/TwoColumnBlockEditor.vue';
import ThreeColumnBlockEditor from './Blocks/ThreeColumnBlockEditor.vue';
import CallToActionBlockEditor from './Blocks/CallToActionBlockEditor.vue';
import FeatureGridBlockEditor from './Blocks/FeatureGridBlockEditor.vue';
import TestimonialBlockEditor from './Blocks/TestimonialBlockEditor.vue';
import SpacerBlockEditor from './Blocks/SpacerBlockEditor.vue';
import ChecklistBlockEditor from './Blocks/ChecklistBlockEditor.vue';
import CardGridBlockEditor from './Blocks/CardGridBlockEditor.vue';

const props = defineProps({
    block: { type: Object, required: true },
    label: { type: String, required: true },
});

const emit = defineEmits(['update:data', 'duplicate', 'remove']);

const collapsed = ref(false);

const editorComponents = {
    hero: HeroBlockEditor,
    richText: RichTextBlockEditor,
    image: ImageBlockEditor,
    twoColumn: TwoColumnBlockEditor,
    threeColumn: ThreeColumnBlockEditor,
    callToAction: CallToActionBlockEditor,
    featureGrid: FeatureGridBlockEditor,
    testimonial: TestimonialBlockEditor,
    spacer: SpacerBlockEditor,
    checklist: ChecklistBlockEditor,
    cardGrid: CardGridBlockEditor,
};
</script>

<template>
    <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
        <!-- Header -->
        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80 px-3 py-2">
            <button type="button" class="block-drag-handle cursor-grab text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                </svg>
            </button>

            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ label }}</span>

            <div class="ml-auto flex items-center gap-1">
                <button
                    type="button"
                    @click="collapsed = !collapsed"
                    class="rounded p-1 text-gray-400 dark:text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-300"
                >
                    <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': !collapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <button
                    type="button"
                    @click="emit('duplicate')"
                    class="rounded p-1 text-gray-400 dark:text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-300"
                    title="Duplicate"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </button>

                <button
                    type="button"
                    @click="emit('remove')"
                    class="rounded p-1 text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600"
                    title="Remove"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div v-show="!collapsed" class="p-4">
            <component
                v-if="editorComponents[block.type]"
                :is="editorComponents[block.type]"
                :modelValue="block.data"
                @update:modelValue="emit('update:data', $event)"
            />
        </div>
    </div>
</template>
