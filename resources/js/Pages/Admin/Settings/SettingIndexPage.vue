<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import MediaPickerComponent from '@/Components/Admin/Forms/MediaPickerComponent.vue';

const props = defineProps({
    settings: Object,
    pages: Array,
});

const tabs = [
    { key: 'general', label: 'General', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
    { key: 'seo', label: 'SEO', icon: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' },
    { key: 'social', label: 'Social', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
    { key: 'schema', label: 'Schema', icon: 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4' },
    { key: 'mail', label: 'Mail', icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
];

const urlParams = new URLSearchParams(window.location.search);
const activeTab = ref(urlParams.get('tab') || 'general');

// Build flat form data from settings
const buildFormData = () => {
    const data = {};
    for (const [group, fields] of Object.entries(props.settings)) {
        for (const [key, meta] of Object.entries(fields)) {
            data[`${group}.${key}`] = meta.value || '';
        }
    }
    return data;
};

const form = useForm({
    tab: activeTab.value,
    settings: buildFormData(),
});

const save = () => {
    form.tab = activeTab.value;
    form.put('/admin/settings');
};

const fieldLabel = (key) => {
    return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const groupFields = computed(() => {
    const group = props.settings[activeTab.value];
    if (!group) return [];
    return Object.entries(group).map(([key, meta]) => ({
        key: `${activeTab.value}.${key}`,
        label: fieldLabel(key),
        type: meta.type,
    }));
});
</script>

<template>
    <Head title="Settings" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[{ label: 'Dashboard', href: '/admin' }, { label: 'Settings' }]" />
        </template>

        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Settings</h1>
                <button @click="save" :disabled="form.processing"
                    class="inline-flex items-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700 disabled:opacity-50">
                    Save Settings
                </button>
            </div>

            <div class="flex gap-6">
                <!-- Tab sidebar -->
                <nav class="w-48 shrink-0 space-y-1">
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        type="button"
                        @click="activeTab = tab.key"
                        class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-colors"
                        :class="activeTab === tab.key
                            ? 'bg-cyan-50 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-400'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white'"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="tab.icon" />
                        </svg>
                        {{ tab.label }}
                    </button>
                </nav>

                <!-- Content -->
                <div class="flex-1">
                    <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 space-y-5">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider">
                            {{ tabs.find(t => t.key === activeTab)?.label }} Settings
                        </h2>

                        <div v-for="field in groupFields" :key="field.key" class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ field.label }}</label>

                            <!-- Image picker -->
                            <MediaPickerComponent
                                v-if="field.type === 'image'"
                                :modelValue="form.settings[field.key]"
                                @update:modelValue="form.settings[field.key] = $event"
                                :label="''"
                            />

                            <!-- Code (tracking scripts etc) -->
                            <textarea
                                v-else-if="field.type === 'code'"
                                v-model="form.settings[field.key]"
                                rows="6"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm dark:text-gray-100 shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none font-mono text-xs"
                                placeholder="Paste your tracking code here (e.g. Google Analytics, GTM)"
                            />

                            <!-- Textarea -->
                            <textarea
                                v-else-if="field.type === 'textarea'"
                                v-model="form.settings[field.key]"
                                rows="4"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm dark:text-gray-100 shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none"
                            />

                            <!-- Homepage page picker -->
                            <select
                                v-else-if="field.key === 'general.homepage'"
                                v-model="form.settings[field.key]"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm dark:text-gray-100 shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none"
                            >
                                <option value="">None</option>
                                <option v-for="page in pages" :key="page.id" :value="String(page.id)">{{ page.title }}</option>
                            </select>
                            <p v-if="field.key === 'general.homepage'" class="text-xs text-gray-400 dark:text-gray-500">
                                The selected page will be served at / and its slug will be hidden
                            </p>

                            <!-- Text input (default) -->
                            <input
                                v-else
                                v-model="form.settings[field.key]"
                                type="text"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm dark:text-gray-100 shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none"
                            />

                            <p v-if="field.key === 'seo.meta_title_template'" class="text-xs text-gray-400 dark:text-gray-500">
                                Use %title% and %site_name% as placeholders
                            </p>
                            <p v-if="field.key === 'seo.meta_description'" class="text-xs text-gray-400 dark:text-gray-500">
                                Default meta description used for the homepage and as a fallback for pages without one
                            </p>
                            <p v-if="field.key === 'mail.password'" class="text-xs text-gray-400 dark:text-gray-500">
                                Stored as plain text — use app password or API key
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
