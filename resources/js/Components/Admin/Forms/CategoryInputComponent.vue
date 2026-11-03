<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    availableCategories: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:modelValue']);

const query = ref('');

const filteredCategories = computed(() => {
    if (!query.value) return [];
    const q = query.value.toLowerCase();
    return props.availableCategories
        .filter(c => c.name.toLowerCase().includes(q) && !props.modelValue.includes(c.id))
        .slice(0, 8);
});

const selectedCategories = computed(() => {
    return props.availableCategories.filter(c => props.modelValue.includes(c.id));
});

const addCategory = (categoryId) => {
    if (!props.modelValue.includes(categoryId)) {
        emit('update:modelValue', [...props.modelValue, categoryId]);
    }
    query.value = '';
};

const removeCategory = (categoryId) => {
    emit('update:modelValue', props.modelValue.filter(id => id !== categoryId));
};

const showDropdown = ref(false);
</script>

<template>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>

        <!-- Selected categories -->
        <div v-if="selectedCategories.length" class="mb-2 flex flex-wrap gap-1.5">
            <span
                v-for="category in selectedCategories"
                :key="category.id"
                class="inline-flex items-center gap-1 rounded-full bg-cyan-100 dark:bg-cyan-900/50 px-2.5 py-0.5 text-xs font-medium text-cyan-700 dark:text-cyan-300"
            >
                {{ category.name }}
                <button type="button" @click="removeCategory(category.id)" class="hover:text-cyan-900 dark:hover:text-cyan-100">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </span>
        </div>

        <!-- Search input -->
        <div class="relative">
            <input
                v-model="query"
                type="text"
                placeholder="Search categories..."
                @focus="showDropdown = true"
                @blur="setTimeout(() => showDropdown = false, 200)"
                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
            />

            <ul
                v-if="showDropdown && filteredCategories.length"
                class="absolute z-10 mt-1 w-full rounded-md bg-white dark:bg-gray-800 py-1 shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-white/10 max-h-40 overflow-y-auto"
            >
                <li
                    v-for="category in filteredCategories"
                    :key="category.id"
                    @mousedown.prevent="addCategory(category.id)"
                    class="cursor-pointer px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-cyan-50 dark:hover:bg-cyan-900/30"
                >
                    {{ category.name }}
                </li>
            </ul>
        </div>
    </div>
</template>
