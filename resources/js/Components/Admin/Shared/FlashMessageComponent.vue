<script setup>
import { ref, watch } from 'vue';
import { useFlash } from '@/Composables/useFlash.js';

const { success, error, warning, hasFlash } = useFlash();
const visible = ref(false);

watch(hasFlash, (val) => {
    if (val) {
        visible.value = true;
        setTimeout(() => { visible.value = false; }, 5000);
    }
}, { immediate: true });
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
    >
        <div v-if="visible && hasFlash" class="mb-4">
            <div
                v-if="success"
                class="rounded-md bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 p-4 text-sm text-green-700 dark:text-green-300"
            >
                {{ success }}
            </div>
            <div
                v-else-if="error"
                class="rounded-md bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 p-4 text-sm text-red-700 dark:text-red-300"
            >
                {{ error }}
            </div>
            <div
                v-else-if="warning"
                class="rounded-md bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 p-4 text-sm text-yellow-700 dark:text-yellow-300"
            >
                {{ warning }}
            </div>
        </div>
    </Transition>
</template>
