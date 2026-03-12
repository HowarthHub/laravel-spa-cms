<script setup>
const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: 'Are you sure?' },
    message: { type: String, default: 'This action cannot be undone.' },
    confirmText: { type: String, default: 'Confirm' },
    cancelText: { type: String, default: 'Cancel' },
    variant: { type: String, default: 'danger' },
});

const emit = defineEmits(['confirm', 'cancel']);

const buttonClass = {
    danger: 'bg-red-600 hover:bg-red-700',
    warning: 'bg-yellow-600 hover:bg-yellow-700',
    info: 'bg-cyan-600 hover:bg-cyan-700',
};
</script>

<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 transition-opacity" @click="$emit('cancel')"></div>
            <div class="relative z-10 w-full max-w-sm rounded-lg bg-white dark:bg-gray-800 p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ title }}</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ message }}</p>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" @click="$emit('cancel')"
                        class="rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        {{ cancelText }}
                    </button>
                    <button type="button" @click="$emit('confirm')"
                        :class="buttonClass[variant]"
                        class="rounded-md px-3 py-2 text-sm font-medium text-white">
                        {{ confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
