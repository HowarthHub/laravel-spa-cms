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
    form: Object,
    submissions: Object,
});

const fields = props.form.fields || [];

// Confirm modal
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
        router.delete(`/admin/forms/${props.form.id}/submissions/${id}`);
    });
};

const getSubmissionValue = (submission, field) => {
    if (!submission.data) return '';
    const value = submission.data[field.label];
    if (value === true) return 'Yes';
    if (value === false) return 'No';
    return value || '';
};
</script>

<template>
    <Head :title="`Submissions: ${form.name}`" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[
                { label: 'Dashboard', href: '/admin' },
                { label: 'Forms', href: '/admin/forms' },
                { label: form.name, href: `/admin/forms/${form.id}/edit` },
                { label: 'Submissions' },
            ]" />
        </template>

        <div class="space-y-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ form.name }} &mdash; Submissions
                </h1>
                <div class="flex items-center gap-2">
                    <a :href="`/admin/forms/${form.id}/submissions/export`"
                        class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export CSV
                    </a>
                    <Link :href="`/admin/forms/${form.id}/edit`"
                        class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Edit Form
                    </Link>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg bg-white dark:bg-gray-800 shadow">
                <div v-if="submissions.data.length" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/60">
                            <tr>
                                <th v-for="field in fields" :key="field.label"
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ field.label }}
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Received</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="submission in submissions.data" :key="submission.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td v-for="field in fields" :key="field.label"
                                    class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">
                                    {{ getSubmissionValue(submission, field) }}
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

                <EmptyStateComponent v-else title="No submissions yet" description="Submissions will appear here when visitors fill out this form." />
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
