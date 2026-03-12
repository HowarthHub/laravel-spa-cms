<script setup>
defineProps({
    modelValue: { type: String, default: '' },
    label: { type: String, default: 'Featured Image' },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ label }}</label>
        <div class="flex items-center gap-3">
            <div v-if="modelValue" class="relative">
                <img :src="modelValue" class="h-20 w-20 rounded-md object-cover border border-gray-200" />
                <button
                    type="button"
                    @click="$emit('update:modelValue', '')"
                    class="absolute -right-1 -top-1 rounded-full bg-red-500 p-0.5 text-white hover:bg-red-600"
                >
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <input
                type="text"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                placeholder="Image URL (media picker coming in Phase 8)"
                class="flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
            />
        </div>
    </div>
</template>
