<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import EmptyStateComponent from '@/Components/Admin/Shared/EmptyStateComponent.vue';
import { usePermissions } from '@/Composables/usePermissions.js';

const { can } = usePermissions();

const props = defineProps({
    enquiries: Object,
    filters: Object,
    newCount: Number,
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const selected = ref([]);

const applyFilters = () => {
    router.get('/admin/enquiries', {
        search: search.value || undefined,
        status: status.value || undefined,
    }, { preserveState: true, replace: true });
};

let searchTimeout = null;
watch(search, () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(applyFilters, 300); });
watch(status, applyFilters);

const toggleAll = (e) => {
    selected.value = e.target.checked ? props.enquiries.data.map(e => e.id) : [];
};

const bulkArchive = () => {
    if (!confirm(`Archive ${selected.value.length} enquiry/enquiries?`)) return;
    router.post('/admin/enquiries/bulk-archive', { ids: selected.value }, {
        onSuccess: () => { selected.value = []; },
    });
};

const deleteEnquiry = (id) => {
    if (!confirm('Delete this enquiry?')) return;
    router.delete(`/admin/enquiries/${id}`);
};

const statusBadge = (s) => ({
    new: 'bg-blue-100 text-blue-700',
    read: 'bg-gray-100 text-gray-700',
    replied: 'bg-green-100 text-green-700',
    archived: 'bg-yellow-100 text-yellow-700',
})[s] || 'bg-gray-100 text-gray-700';
</script>

<template>
    <Head title="Enquiries" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[{ label: 'Dashboard', href: '/admin' }, { label: 'Enquiries' }]" />
        </template>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-semibold text-gray-900">Enquiries</h1>
                    <span v-if="newCount" class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">
                        {{ newCount }} new
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input v-model="search" type="text" placeholder="Search name, email, subject..."
                    class="w-64 rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                <select v-model="status"
                    class="rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                    <option value="">All Statuses</option>
                    <option value="new">New</option>
                    <option value="read">Read</option>
                    <option value="replied">Replied</option>
                    <option value="archived">Archived</option>
                </select>
                <button v-if="selected.length && can('manage enquiries')" @click="bulkArchive"
                    class="rounded-md bg-yellow-600 px-3 py-2 text-sm font-medium text-white hover:bg-yellow-700">
                    Archive ({{ selected.length }})
                </button>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow">
                <table v-if="enquiries.data.length" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th v-if="can('manage enquiries')" class="w-10 px-4 py-3">
                                <input type="checkbox" @change="toggleAll" class="rounded border-gray-300" />
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Subject</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Received</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="enq in enquiries.data" :key="enq.id" class="hover:bg-gray-50"
                            :class="{ 'bg-blue-50/50': enq.status === 'new' }">
                            <td v-if="can('manage enquiries')" class="px-4 py-3">
                                <input type="checkbox" :value="enq.id" v-model="selected" class="rounded border-gray-300" />
                            </td>
                            <td class="px-4 py-3">
                                <Link :href="`/admin/enquiries/${enq.id}`" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    {{ enq.name }}
                                </Link>
                                <p class="text-xs text-gray-400">{{ enq.email }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <span :class="{ 'font-semibold': enq.status === 'new' }">{{ enq.subject || '(no subject)' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="statusBadge(enq.status)" class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium capitalize">
                                    {{ enq.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ new Date(enq.created_at).toLocaleDateString() }}</td>
                            <td class="px-4 py-3 text-right">
                                <Link :href="`/admin/enquiries/${enq.id}`"
                                    class="text-sm text-indigo-600 hover:text-indigo-500 mr-3">View</Link>
                                <button v-if="can('manage enquiries')" @click="deleteEnquiry(enq.id)"
                                    class="text-sm text-red-600 hover:text-red-500">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <EmptyStateComponent v-else title="No enquiries found" description="Enquiries from your contact form will appear here." />
            </div>

            <div v-if="enquiries.last_page > 1" class="flex justify-center gap-1">
                <Link v-for="link in enquiries.links" :key="link.label" :href="link.url || '#'" v-html="link.label"
                    class="rounded-md px-3 py-1 text-sm"
                    :class="link.active ? 'bg-indigo-600 text-white' : link.url ? 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' : 'bg-white text-gray-300 border border-gray-200 cursor-default'" />
            </div>
        </div>
    </AdminLayout>
</template>
