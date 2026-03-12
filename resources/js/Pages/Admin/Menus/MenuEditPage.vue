<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import MenuTreeComponent from '@/Components/Admin/Menus/MenuTreeComponent.vue';

const props = defineProps({
    menu: Object,
    tree: Array,
    pages: Array,
    posts: Array,
});

const items = ref(JSON.parse(JSON.stringify(props.tree || [])));

const form = useForm({
    name: props.menu.name,
    handle: props.menu.handle,
    tree: [],
});

const save = () => {
    form.tree = items.value;
    form.put(`/admin/menus/${props.menu.id}`);
};

// Add item panel
const addType = ref('custom');
const addLabel = ref('');
const addUrl = ref('');
const addTarget = ref('_self');
const addLinkableId = ref('');

const linkableOptions = computed(() => {
    if (addType.value === 'page') return props.pages;
    if (addType.value === 'post') return props.posts;
    return [];
});

let nextTempId = Date.now();

const addItem = () => {
    if (!addLabel.value) return;

    const item = {
        id: nextTempId++,
        label: addLabel.value,
        type: addType.value,
        linkable_type: null,
        linkable_id: null,
        url: null,
        target: addTarget.value,
        children: [],
    };

    if (addType.value === 'custom') {
        item.url = addUrl.value;
    } else if (addType.value === 'page') {
        const page = props.pages.find(p => p.id === Number(addLinkableId.value));
        if (page) {
            item.linkable_type = 'App\\Models\\PageModel';
            item.linkable_id = page.id;
            item.url = `/${page.slug}`;
        }
    } else if (addType.value === 'post') {
        const post = props.posts.find(p => p.id === Number(addLinkableId.value));
        if (post) {
            item.linkable_type = 'App\\Models\\PostModel';
            item.linkable_id = post.id;
            item.url = `/blog/${post.slug}`;
        }
    }

    items.value.push(item);

    // Reset
    addLabel.value = '';
    addUrl.value = '';
    addLinkableId.value = '';
    addTarget.value = '_self';
};

// Edit modal
const editingItem = ref(null);
const editingIndex = ref(null);
const showEditModal = ref(false);

const findItemByPath = (tree, targetIndex, currentDepth, targetDepth) => {
    if (currentDepth === targetDepth) {
        return tree[targetIndex] || null;
    }
    for (const item of tree) {
        if (item.children?.length) {
            const found = findItemByPath(item.children, targetIndex, currentDepth + 1, targetDepth);
            if (found) return found;
        }
    }
    return null;
};

const onEditItem = (index, depth) => {
    const item = depth === 0 ? items.value[index] : findItemByPath(items.value, index, 0, depth);
    if (item) {
        editingItem.value = { ...item };
        editingIndex.value = { index, depth };
        showEditModal.value = true;
    }
};

const saveEdit = () => {
    if (!editingItem.value || !editingIndex.value) return;

    const applyEdit = (tree, idx, depth, targetDepth) => {
        if (depth === targetDepth) {
            tree[idx] = { ...tree[idx], ...editingItem.value, children: tree[idx].children };
            return true;
        }
        for (const item of tree) {
            if (item.children?.length && applyEdit(item.children, idx, depth + 1, targetDepth)) {
                return true;
            }
        }
        return false;
    };

    applyEdit(items.value, editingIndex.value.index, 0, editingIndex.value.depth);
    showEditModal.value = false;
    editingItem.value = null;
};
</script>

<template>
    <Head :title="`Edit Menu: ${menu.name}`" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[
                { label: 'Dashboard', href: '/admin' },
                { label: 'Menus', href: '/admin/menus' },
                { label: menu.name },
            ]" />
        </template>

        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">Edit Menu: {{ menu.name }}</h1>
                <button @click="save" :disabled="form.processing"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50">
                    Save Menu
                </button>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Menu items tree -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="rounded-md border border-gray-200 bg-white p-4 space-y-3">
                        <h2 class="text-sm font-medium text-gray-700">Menu Name</h2>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <input v-model="form.name" type="text" placeholder="Menu name"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                            </div>
                            <div>
                                <input v-model="form.handle" type="text" placeholder="Handle"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none font-mono" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-md border border-gray-200 bg-gray-50 p-4 min-h-[200px]">
                        <h2 class="mb-3 text-sm font-medium text-gray-700">Menu Structure</h2>
                        <p v-if="!items.length" class="text-sm text-gray-400 py-8 text-center">
                            No items yet. Add items from the panel on the right.
                        </p>
                        <MenuTreeComponent
                            v-model="items"
                            @edit="onEditItem"
                        />
                    </div>
                </div>

                <!-- Add item panel -->
                <div class="space-y-4">
                    <div class="rounded-md border border-gray-200 bg-white p-4 space-y-3">
                        <h2 class="text-sm font-medium text-gray-700">Add Menu Item</h2>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                            <select v-model="addType"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                                <option value="custom">Custom URL</option>
                                <option value="page">Page</option>
                                <option value="post">Post</option>
                            </select>
                        </div>

                        <div v-if="addType !== 'custom'">
                            <label class="block text-xs font-medium text-gray-500 mb-1">
                                {{ addType === 'page' ? 'Select Page' : 'Select Post' }}
                            </label>
                            <select v-model="addLinkableId"
                                @change="!addLabel && linkableOptions.find(o => o.id === Number(addLinkableId)) && (addLabel = linkableOptions.find(o => o.id === Number(addLinkableId)).title)"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                                <option value="">Choose...</option>
                                <option v-for="opt in linkableOptions" :key="opt.id" :value="opt.id">{{ opt.title }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Label</label>
                            <input v-model="addLabel" type="text" placeholder="Menu item label"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                        </div>

                        <div v-if="addType === 'custom'">
                            <label class="block text-xs font-medium text-gray-500 mb-1">URL</label>
                            <input v-model="addUrl" type="text" placeholder="https://..."
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Target</label>
                            <select v-model="addTarget"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                                <option value="_self">Same Window</option>
                                <option value="_blank">New Tab</option>
                            </select>
                        </div>

                        <button type="button" @click="addItem" :disabled="!addLabel"
                            class="w-full rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">
                            Add to Menu
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit item modal -->
        <Teleport to="body">
            <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50" @click="showEditModal = false"></div>
                <div class="relative z-10 w-full max-w-md rounded-lg bg-white p-5 shadow-xl space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Menu Item</h3>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Label</label>
                        <input v-model="editingItem.label" type="text"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">URL</label>
                        <input v-model="editingItem.url" type="text"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Target</label>
                        <select v-model="editingItem.target"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                            <option value="_self">Same Window</option>
                            <option value="_blank">New Tab</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showEditModal = false"
                            class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="button" @click="saveEdit"
                            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
