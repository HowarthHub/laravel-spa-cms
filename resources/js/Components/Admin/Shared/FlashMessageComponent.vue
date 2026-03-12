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
                class="rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-700"
            >
                {{ success }}
            </div>
            <div
                v-else-if="error"
                class="rounded-md bg-red-50 border border-red-200 p-4 text-sm text-red-700"
            >
                {{ error }}
            </div>
            <div
                v-else-if="warning"
                class="rounded-md bg-yellow-50 border border-yellow-200 p-4 text-sm text-yellow-700"
            >
                {{ warning }}
            </div>
        </div>
    </Transition>
</template>
