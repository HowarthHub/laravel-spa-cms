<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import StatusBadgeComponent from '@/Components/Admin/Shared/StatusBadgeComponent.vue';
import ConfirmModalComponent from '@/Components/Admin/Shared/ConfirmModalComponent.vue';
import { usePermissions } from '@/Composables/usePermissions.js';

const { can } = usePermissions();

const props = defineProps({
    enquiry: Object,
});

const form = useForm({
    reply_note: props.enquiry.reply_note || '',
});

const submitReply = () => {
    form.put(`/admin/enquiries/${props.enquiry.id}`);
};

const archiveEnquiry = () => {
    router.put(`/admin/enquiries/${props.enquiry.id}`, { status: 'archived' });
};

// Confirm modal
const confirmModal = ref(false);
const confirmAction = ref(null);
const confirmTitle = ref('');
const confirmMessage = ref('');

const showConfirm = (title, message, action) => {
    confirmTitle.value = title;
    confirmMessage.value = message;
    confirmAction.value = action;
    confirmModal.value = true;
};

const onConfirm = () => {
    confirmModal.value = false;
    if (confirmAction.value) confirmAction.value();
};

const deleteEnquiry = () => {
    showConfirm('Delete Enquiry', 'Are you sure you want to delete this enquiry?', () => {
        router.delete(`/admin/enquiries/${props.enquiry.id}`);
    });
};

const formatDate = (d) => d ? new Date(d).toLocaleString() : '\u2014';
</script>

<template>
    <Head :title="`Enquiry from ${enquiry.name}`" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[
                { label: 'Dashboard', href: '/admin' },
                { label: 'Enquiries', href: '/admin/enquiries' },
                { label: enquiry.name },
            ]" />
        </template>

        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Enquiry from {{ enquiry.name }}</h1>
                <div class="flex items-center gap-2">
                    <StatusBadgeComponent :status="enquiry.status" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Message -->
                <div class="lg:col-span-2 space-y-5">
                    <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 space-y-4">
                        <div v-if="enquiry.subject">
                            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Subject</h2>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ enquiry.subject }}</p>
                        </div>

                        <div>
                            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Message</h2>
                            <p class="mt-1 whitespace-pre-wrap text-gray-900 dark:text-white">{{ enquiry.message }}</p>
                        </div>
                    </div>

                    <!-- Reply -->
                    <div v-if="can('manage enquiries')" class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 space-y-3">
                        <h2 class="text-sm font-medium text-gray-700 dark:text-gray-300">Reply Note</h2>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Internal note — this is not sent to the contact.</p>
                        <textarea v-model="form.reply_note" rows="4"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm dark:text-gray-100 shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none" />
                        <div class="flex justify-end">
                            <button @click="submitReply" :disabled="form.processing"
                                class="inline-flex items-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700 disabled:opacity-50">
                                {{ enquiry.replied_at ? 'Update Note' : 'Save & Mark Replied' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Details sidebar -->
                <div class="space-y-5">
                    <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 space-y-3">
                        <h2 class="text-sm font-medium text-gray-700 dark:text-gray-300">Contact Details</h2>
                        <dl class="space-y-2 text-sm">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Name</dt>
                                <dd class="text-gray-900 dark:text-white">{{ enquiry.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Email</dt>
                                <dd><a :href="`mailto:${enquiry.email}`" class="text-cyan-600 hover:text-cyan-500">{{ enquiry.email }}</a></dd>
                            </div>
                            <div v-if="enquiry.phone">
                                <dt class="text-gray-500 dark:text-gray-400">Phone</dt>
                                <dd class="text-gray-900 dark:text-white">{{ enquiry.phone }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 space-y-3">
                        <h2 class="text-sm font-medium text-gray-700 dark:text-gray-300">Timestamps</h2>
                        <dl class="space-y-2 text-sm">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Received</dt>
                                <dd class="text-gray-900 dark:text-white">{{ formatDate(enquiry.created_at) }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Read</dt>
                                <dd class="text-gray-900 dark:text-white">{{ formatDate(enquiry.read_at) }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Replied</dt>
                                <dd class="text-gray-900 dark:text-white">{{ formatDate(enquiry.replied_at) }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 space-y-3">
                        <h2 class="text-sm font-medium text-gray-700 dark:text-gray-300">Meta</h2>
                        <dl class="space-y-2 text-sm">
                            <div v-if="enquiry.ip_address">
                                <dt class="text-gray-500 dark:text-gray-400">IP Address</dt>
                                <dd class="text-gray-900 dark:text-white font-mono text-xs">{{ enquiry.ip_address }}</dd>
                            </div>
                            <div v-if="enquiry.user_agent">
                                <dt class="text-gray-500 dark:text-gray-400">User Agent</dt>
                                <dd class="text-gray-900 dark:text-white text-xs truncate" :title="enquiry.user_agent">{{ enquiry.user_agent }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div v-if="can('manage enquiries')" class="flex flex-col gap-2">
                        <button v-if="enquiry.status !== 'archived'" @click="archiveEnquiry"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Archive
                        </button>
                        <button @click="deleteEnquiry"
                            class="w-full rounded-md border border-red-300 dark:border-red-700 px-3 py-2 text-sm font-medium text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmModalComponent
            :show="confirmModal"
            :title="confirmTitle"
            :message="confirmMessage"
            confirm-text="Delete"
            @confirm="onConfirm"
            @cancel="confirmModal = false"
        />
    </AdminLayout>
</template>
