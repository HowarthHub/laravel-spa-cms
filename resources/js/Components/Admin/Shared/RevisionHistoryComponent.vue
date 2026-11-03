<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    type: { type: String, required: true },
    id: { type: [Number, String], required: true },
});

const isOpen = ref(false);
const loading = ref(false);
const revisions = ref([]);
const confirmingRestore = ref(null);
const restoring = ref(false);

const fetchRevisions = async () => {
    if (!isOpen.value) {
        isOpen.value = true;
        loading.value = true;

        try {
            const response = await fetch(`/admin/${props.type}/${props.id}/revisions`);
            revisions.value = await response.json();
        } catch {
            revisions.value = [];
        } finally {
            loading.value = false;
        }

        return;
    }

    isOpen.value = false;
};

const confirmRestore = (revision) => {
    confirmingRestore.value = revision.id;
};

const cancelRestore = () => {
    confirmingRestore.value = null;
};

const restoreRevision = (revision) => {
    restoring.value = true;

    router.post(`/admin/revisions/${revision.id}/restore`, {}, {
        onFinish: () => {
            restoring.value = false;
            confirmingRestore.value = null;
        },
    });
};

const formatDate = (dateString) => {
    const date = new Date(dateString);

    return date.toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <button
            type="button"
            class="flex w-full items-center justify-between p-4 text-left"
            @click="fetchRevisions"
        >
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Revision History</span>
            <svg
                class="h-4 w-4 text-gray-400 transition-transform duration-200"
                :class="{ 'rotate-180': isOpen }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div v-if="isOpen" class="border-t border-gray-200 dark:border-gray-700 px-4 pb-4">
            <div v-if="loading" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                Loading revisions...
            </div>

            <div v-else-if="revisions.length === 0" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                No revisions yet.
            </div>

            <ul v-else class="divide-y divide-gray-100 dark:divide-gray-700">
                <li
                    v-for="revision in revisions"
                    :key="revision.id"
                    class="flex items-center justify-between gap-3 py-3"
                >
                    <div class="min-w-0">
                        <p class="text-sm text-gray-700 dark:text-gray-300 truncate">
                            {{ revision.user?.name ?? 'Unknown' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ formatDate(revision.created_at) }}
                        </p>
                    </div>

                    <div v-if="confirmingRestore === revision.id" class="flex items-center gap-1.5">
                        <button
                            type="button"
                            class="rounded bg-red-600 px-2 py-1 text-xs font-medium text-white hover:bg-red-700 disabled:opacity-50"
                            :disabled="restoring"
                            @click="restoreRevision(revision)"
                        >
                            {{ restoring ? 'Restoring...' : 'Confirm' }}
                        </button>
                        <button
                            type="button"
                            class="rounded border border-gray-300 dark:border-gray-600 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700"
                            @click="cancelRestore"
                        >
                            Cancel
                        </button>
                    </div>
                    <button
                        v-else
                        type="button"
                        class="shrink-0 rounded border border-gray-300 dark:border-gray-600 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700"
                        @click="confirmRestore(revision)"
                    >
                        Restore
                    </button>
                </li>
            </ul>
        </div>
    </div>
</template>
