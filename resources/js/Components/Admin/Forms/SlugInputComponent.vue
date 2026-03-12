<script setup>
import { ref, watch, toRef } from 'vue';
import { useSlug } from '@/Composables/useSlug.js';

const props = defineProps({
    modelValue: { type: String, default: '' },
    title: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const titleRef = toRef(props, 'title');
const { slug, manuallyEdited, setManual, resetAutoGenerate } = useSlug(titleRef, props.modelValue);

const editing = ref(false);

watch(slug, (val) => {
    emit('update:modelValue', val);
});

const handleManualInput = (e) => {
    setManual(e.target.value);
};
</script>

<template>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
        <div class="flex items-center gap-2">
            <input
                v-if="editing"
                type="text"
                :value="slug"
                @input="handleManualInput"
                @blur="editing = false"
                class="flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
            />
            <div v-else class="flex-1 flex items-center gap-2">
                <span class="text-sm text-gray-500">/{{ slug || '...' }}</span>
                <button
                    type="button"
                    @click="editing = true"
                    class="text-xs text-indigo-600 hover:text-indigo-500"
                >
                    Edit
                </button>
                <button
                    v-if="manuallyEdited"
                    type="button"
                    @click="resetAutoGenerate"
                    class="text-xs text-gray-400 hover:text-gray-600"
                >
                    Auto
                </button>
            </div>
        </div>
    </div>
</template>
