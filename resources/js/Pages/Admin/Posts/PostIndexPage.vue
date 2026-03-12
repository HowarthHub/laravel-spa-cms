<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import EmptyStateComponent from '@/Components/Admin/Shared/EmptyStateComponent.vue';
import { usePermissions } from '@/Composables/usePermissions.js';

const { can } = usePermissions();

const props = defineProps({
    posts: Object,
    filters: Object,
    categories: Array,
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const categoryId = ref(props.filters?.category_id || '');
const selected = ref([]);

const applyFilters = () => {
    router.get('/admin/posts', {
        search: search.value || undefined,
        status: status.value || undefined,
        category_id: categoryId.value || undefined,
    }, { preserveState: true, replace: true });
};

let searchTimeout = null;
watch(search, () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(applyFilters, 300); });
watch(status, applyFilters);
watch(categoryId, applyFilters);

const toggleAll = (e) => {
    selected.value = e.target.checked ? props.posts.data.map(p => p.id) : [];
};

const bulkDelete = () => {
    if (!confirm(`Delete ${selected.value.length} post(s)?`)) return;
    router.post('/admin/posts/bulk-destroy', { ids: selected.value }, {
        onSuccess: () => { selected.value = []; },
    });
};

const deletePost = (id) => {
    if (!confirm('Delete this post?')) return;
    router.delete(`/admin/posts/${id}`);
};

const statusBadge = (s) => ({
    draft: 'bg-gray-100 text-gray-700',
    published: 'bg-green-100 text-green-700',
    scheduled: 'bg-yellow-100 text-yellow-700',
})[s] || 'bg-gray-100 text-gray-700';
</script>

<template>
    <Head title="Posts" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[{ label: 'Dashboard', href: '/admin' }, { label: 'Posts' }]" />
        </template>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">Posts</h1>
                <Link
                    v-if="can('create posts')"
                    href="/admin/posts/create"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                >
                    Add Post
                </Link>
            </div>

            <div class="flex items-center gap-3">
                <input v-model="search" type="text" placeholder="Search posts..."
                    class="w-64 rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                <select v-model="status"
                    class="rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                    <option value="">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="scheduled">Scheduled</option>
                </select>
                <select v-model="categoryId"
                    class="rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                    <option value="">All Categories</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
                <button v-if="selected.length && can('delete posts')" @click="bulkDelete"
                    class="rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700">
                    Delete ({{ selected.length }})
                </button>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow">
                <table v-if="posts.data.length" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th v-if="can('delete posts')" class="w-10 px-4 py-3">
                                <input type="checkbox" @change="toggleAll" class="rounded border-gray-300" />
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Categories</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Author</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Updated</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="post in posts.data" :key="post.id" class="hover:bg-gray-50">
                            <td v-if="can('delete posts')" class="px-4 py-3">
                                <input type="checkbox" :value="post.id" v-model="selected" class="rounded border-gray-300" />
                            </td>
                            <td class="px-4 py-3">
                                <Link :href="`/admin/posts/${post.id}/edit`" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    {{ post.title }}
                                </Link>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ post.categories?.map(c => c.name).join(', ') || '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span :class="statusBadge(post.status)" class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium capitalize">
                                    {{ post.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ post.author?.name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ new Date(post.updated_at).toLocaleDateString() }}</td>
                            <td class="px-4 py-3 text-right">
                                <Link v-if="can('edit posts')" :href="`/admin/posts/${post.id}/edit`"
                                    class="text-sm text-indigo-600 hover:text-indigo-500 mr-3">Edit</Link>
                                <button v-if="can('delete posts')" @click="deletePost(post.id)"
                                    class="text-sm text-red-600 hover:text-red-500">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <EmptyStateComponent v-else title="No posts found" description="Get started by creating a new post.">
                    <template #action>
                        <Link v-if="can('create posts')" href="/admin/posts/create"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                            Add Post
                        </Link>
                    </template>
                </EmptyStateComponent>
            </div>

            <div v-if="posts.last_page > 1" class="flex justify-center gap-1">
                <Link v-for="link in posts.links" :key="link.label" :href="link.url || '#'" v-html="link.label"
                    class="rounded-md px-3 py-1 text-sm"
                    :class="link.active ? 'bg-indigo-600 text-white' : link.url ? 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' : 'bg-white text-gray-300 border border-gray-200 cursor-default'" />
            </div>
        </div>
    </AdminLayout>
</template>
