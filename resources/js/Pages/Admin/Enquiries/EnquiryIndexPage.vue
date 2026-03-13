<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import EmptyStateComponent from '@/Components/Admin/Shared/EmptyStateComponent.vue';
import PaginationComponent from '@/Components/Admin/Shared/PaginationComponent.vue';
import StatusBadgeComponent from '@/Components/Admin/Shared/StatusBadgeComponent.vue';
import ConfirmModalComponent from '@/Components/Admin/Shared/ConfirmModalComponent.vue';
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
const sortBy = ref('created_at');
const sortDir = ref('desc');

const applyFilters = () => {
    router.get('/admin/enquiries', {
        search: search.value || undefined,
        status: status.value || undefined,
        sort: sortBy.value !== 'created_at' ? sortBy.value : undefined,
        direction: sortDir.value !== 'desc' ? sortDir.value : undefined,
    }, { preserveState: true, replace: true });
};

let searchTimeout = null;
watch(search, () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(applyFilters, 300); });
watch(status, applyFilters);

const toggleSort = (field) => {
    if (sortBy.value === field) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = field;
        sortDir.value = 'asc';
    }
    applyFilters();
};

const sortIcon = (field) => {
    if (sortBy.value !== field) return '';
    return sortDir.value === 'asc' ? ' ↑' : ' ↓';
};

const toggleAll = (e) => {
    selected.value = e.target.checked ? props.enquiries.data.map(e => e.id) : [];
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

const bulkArchive = () => {
    showConfirm(
        'Archive Enquiries',
        `Are you sure you want to archive ${selected.value.length} enquiry/enquiries?`,
        () => router.post('/admin/enquiries/bulk-archive', { ids: selected.value }, {
            onSuccess: () => { selected.value = []; },
        })
    );
};

const deleteEnquiry = (id) => {
    showConfirm('Delete Enquiry', 'Are you sure you want to delete this enquiry?', () => {
        router.delete(`/admin/enquiries/${id}`);
    });
};

const exportUrl = computed(() => {
    const params = new URLSearchParams();
    if (search.value) params.set('search', search.value);
    if (status.value) params.set('status', status.value);
    const qs = params.toString();
    return '/admin/enquiries/export' + (qs ? '?' + qs : '');
});

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
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Enquiries</h1>
                    <span v-if="newCount" class="inline-flex items-center rounded-full bg-cyan-500 px-2.5 py-0.5 text-xs font-semibold text-white">
                        {{ newCount }} new
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input v-model="search" type="text" placeholder="Search name, email, subject..."
                    class="w-64 rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100" />
                <select v-model="status"
                    class="rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100">
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
                <a :href="exportUrl"
                    class="ml-auto inline-flex items-center gap-1.5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export CSV
                </a>
            </div>

            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
                <table v-if="enquiries.data.length" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/60">
                        <tr>
                            <th v-if="can('manage enquiries')" class="w-10 px-4 py-3">
                                <input type="checkbox" @change="toggleAll" class="rounded border-gray-300 dark:border-gray-600" />
                            </th>
                            <th @click="toggleSort('name')" class="cursor-pointer px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 select-none">
                                Name{{ sortIcon('name') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Subject</th>
                            <th @click="toggleSort('status')" class="cursor-pointer px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 select-none">
                                Status{{ sortIcon('status') }}
                            </th>
                            <th @click="toggleSort('created_at')" class="cursor-pointer px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 select-none">
                                Received{{ sortIcon('created_at') }}
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="enq in enquiries.data" :key="enq.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td v-if="can('manage enquiries')" class="px-4 py-3">
                                <input type="checkbox" :value="enq.id" v-model="selected" class="rounded border-gray-300 dark:border-gray-600" />
                            </td>
                            <td class="px-4 py-3">
                                <Link :href="`/admin/enquiries/${enq.id}`" class="text-sm font-medium text-cyan-600 hover:text-cyan-500">
                                    {{ enq.name }}
                                </Link>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ enq.email }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                <span :class="{ 'font-semibold': enq.status === 'new' }">{{ enq.subject || '(no subject)' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <StatusBadgeComponent :status="enq.status" />
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ new Date(enq.created_at).toLocaleDateString() }}</td>
                            <td class="px-4 py-3 text-right">
                                <Link :href="`/admin/enquiries/${enq.id}`"
                                    class="text-sm text-cyan-600 hover:text-cyan-500 mr-3">View</Link>
                                <button v-if="can('manage enquiries')" @click="deleteEnquiry(enq.id)"
                                    class="text-sm text-red-600 hover:text-red-500">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <EmptyStateComponent v-else title="No enquiries found" description="Enquiries from your contact form will appear here." />
            </div>

            <PaginationComponent :links="enquiries.links" :last-page="enquiries.last_page" />
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
