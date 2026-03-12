<script setup>
defineProps({
    status: { type: String, required: true },
});

const classes = {
    draft: 'bg-gray-500 text-white',
    published: 'bg-emerald-500 text-white',
    scheduled: 'bg-amber-500 text-white',
    new: 'bg-cyan-500 text-white',
    read: 'bg-gray-500 text-white',
    replied: 'bg-emerald-500 text-white',
    archived: 'bg-amber-500 text-white',
};
</script>

<template>
    <span
        :class="classes[status] || 'bg-gray-500 text-white'"
        class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize"
    >
        {{ status }}
    </span>
</template>
