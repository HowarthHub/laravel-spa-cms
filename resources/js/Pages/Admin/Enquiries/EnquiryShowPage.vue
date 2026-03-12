<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
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

const deleteEnquiry = () => {
    if (!confirm('Delete this enquiry?')) return;
    router.delete(`/admin/enquiries/${props.enquiry.id}`);
};

const statusBadge = (s) => ({
    new: 'bg-blue-100 text-blue-700',
    read: 'bg-gray-100 text-gray-700',
    replied: 'bg-green-100 text-green-700',
    archived: 'bg-yellow-100 text-yellow-700',
})[s] || 'bg-gray-100 text-gray-700';

const formatDate = (d) => d ? new Date(d).toLocaleString() : '—';
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
                <h1 class="text-xl font-semibold text-gray-900">Enquiry from {{ enquiry.name }}</h1>
                <div class="flex items-center gap-2">
                    <span :class="statusBadge(enquiry.status)" class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium capitalize">
                        {{ enquiry.status }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Message -->
                <div class="lg:col-span-2 space-y-5">
                    <div class="rounded-md border border-gray-200 bg-white p-5 space-y-4">
                        <div v-if="enquiry.subject">
                            <h2 class="text-sm font-medium text-gray-500">Subject</h2>
                            <p class="mt-1 text-gray-900">{{ enquiry.subject }}</p>
                        </div>

                        <div>
                            <h2 class="text-sm font-medium text-gray-500">Message</h2>
                            <p class="mt-1 whitespace-pre-wrap text-gray-900">{{ enquiry.message }}</p>
                        </div>
                    </div>

                    <!-- Reply -->
                    <div v-if="can('manage enquiries')" class="rounded-md border border-gray-200 bg-white p-5 space-y-3">
                        <h2 class="text-sm font-medium text-gray-700">Reply Note</h2>
                        <p class="text-xs text-gray-400">Internal note — this is not sent to the contact.</p>
                        <textarea v-model="form.reply_note" rows="4"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                        <div class="flex justify-end">
                            <button @click="submitReply" :disabled="form.processing"
                                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50">
                                {{ enquiry.replied_at ? 'Update Note' : 'Save & Mark Replied' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Details sidebar -->
                <div class="space-y-5">
                    <div class="rounded-md border border-gray-200 bg-white p-4 space-y-3">
                        <h2 class="text-sm font-medium text-gray-700">Contact Details</h2>
                        <dl class="space-y-2 text-sm">
                            <div>
                                <dt class="text-gray-500">Name</dt>
                                <dd class="text-gray-900">{{ enquiry.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Email</dt>
                                <dd><a :href="`mailto:${enquiry.email}`" class="text-indigo-600 hover:text-indigo-500">{{ enquiry.email }}</a></dd>
                            </div>
                            <div v-if="enquiry.phone">
                                <dt class="text-gray-500">Phone</dt>
                                <dd class="text-gray-900">{{ enquiry.phone }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-md border border-gray-200 bg-white p-4 space-y-3">
                        <h2 class="text-sm font-medium text-gray-700">Timestamps</h2>
                        <dl class="space-y-2 text-sm">
                            <div>
                                <dt class="text-gray-500">Received</dt>
                                <dd class="text-gray-900">{{ formatDate(enquiry.created_at) }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Read</dt>
                                <dd class="text-gray-900">{{ formatDate(enquiry.read_at) }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Replied</dt>
                                <dd class="text-gray-900">{{ formatDate(enquiry.replied_at) }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-md border border-gray-200 bg-white p-4 space-y-3">
                        <h2 class="text-sm font-medium text-gray-700">Meta</h2>
                        <dl class="space-y-2 text-sm">
                            <div v-if="enquiry.ip_address">
                                <dt class="text-gray-500">IP Address</dt>
                                <dd class="text-gray-900 font-mono text-xs">{{ enquiry.ip_address }}</dd>
                            </div>
                            <div v-if="enquiry.user_agent">
                                <dt class="text-gray-500">User Agent</dt>
                                <dd class="text-gray-900 text-xs truncate" :title="enquiry.user_agent">{{ enquiry.user_agent }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div v-if="can('manage enquiries')" class="flex flex-col gap-2">
                        <button v-if="enquiry.status !== 'archived'" @click="archiveEnquiry"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Archive
                        </button>
                        <button @click="deleteEnquiry"
                            class="w-full rounded-md border border-red-300 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
