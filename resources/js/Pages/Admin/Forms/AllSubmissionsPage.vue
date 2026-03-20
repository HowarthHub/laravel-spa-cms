<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import EmptyStateComponent from '@/Components/Admin/Shared/EmptyStateComponent.vue';
import PaginationComponent from '@/Components/Admin/Shared/PaginationComponent.vue';
import ConfirmModalComponent from '@/Components/Admin/Shared/ConfirmModalComponent.vue';
import { usePermissions } from '@/Composables/usePermissions.js';

const { can } = usePermissions();

const props = defineProps({
    submissions: Object,
});

const confirmModal = ref(false);
const confirmAction = ref(null);

const showConfirm = (action) => {
    confirmAction.value = action;
    confirmModal.value = true;
};

const onConfirm = () => {
    confirmModal.value = false;
    if (confirmAction.value) confirmAction.value();
};

const deleteSubmission = (id) => {
    showConfirm(() => {
        router.delete(`/admin/forms/submissions/${id}`);
    });
};

const getSummary = (submission) => {
    if (!submission.data) return '';
    const values = Object.values(submission.data).filter(v => typeof v === 'string' && v.length > 0);
    return values.slice(0, 2).join(' — ') || '';
};
</script>

<template>
    <Head title="Enquiries" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[
                { label: 'Dashboard', href: '/admin' },
                { label: 'Enquiries' },
            ]" />
        </template>

        <div class="space-y-4">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Enquiries</h1>

            <div class="overflow-x-auto rounded-lg bg-white dark:bg-gray-800 shadow">
                <div v-if="submissions.data.length">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/60">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Form</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Summary</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Received</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="submission in submissions.data" :key="submission.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 text-sm whitespace-nowrap">
                                    <Link v-if="submission.form" :href="`/admin/forms/${submission.form.id}/submissions`"
                                        class="text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300">
                                        {{ submission.form.name }}
                                    </Link>
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 max-w-md truncate">
                                    {{ getSummary(submission) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ new Date(submission.created_at).toLocaleDateString() }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <button v-if="can('manage forms')" @click="deleteSubmission(submission.id)"
                                        class="text-sm text-red-600 hover:text-red-500">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <EmptyStateComponent v-else title="No enquiries yet" description="Form submissions will appear here when visitors fill out your forms." />
            </div>

            <PaginationComponent :links="submissions.links" :last-page="submissions.last_page" />
        </div>

        <ConfirmModalComponent
            :show="confirmModal"
            title="Delete Submission"
            message="Are you sure you want to delete this submission? This action cannot be undone."
            confirm-text="Delete"
            @confirm="onConfirm"
            @cancel="confirmModal = false"
        />
    </AdminLayout>
</template>
