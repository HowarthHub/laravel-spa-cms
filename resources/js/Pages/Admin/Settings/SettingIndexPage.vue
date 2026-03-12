<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import MediaPickerComponent from '@/Components/Admin/Forms/MediaPickerComponent.vue';

const props = defineProps({
    settings: Object,
});

const tabs = [
    { key: 'general', label: 'General', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
    { key: 'seo', label: 'SEO', icon: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' },
    { key: 'social', label: 'Social', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
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
                <h1 class="text-xl font-semibold text-gray-900">Settings</h1>
                <button @click="save" :disabled="form.processing"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50">
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
                            ? 'bg-indigo-50 text-indigo-700'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="tab.icon" />
                        </svg>
                        {{ tab.label }}
                    </button>
                </nav>

                <!-- Content -->
                <div class="flex-1">
                    <div class="rounded-md border border-gray-200 bg-white p-5 space-y-5">
                        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">
                            {{ tabs.find(t => t.key === activeTab)?.label }} Settings
                        </h2>

                        <div v-for="field in groupFields" :key="field.key" class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">{{ field.label }}</label>

                            <!-- Image picker -->
                            <MediaPickerComponent
                                v-if="field.type === 'image'"
                                :modelValue="form.settings[field.key]"
                                @update:modelValue="form.settings[field.key] = $event"
                                :label="''"
                            />

                            <!-- Textarea -->
                            <textarea
                                v-else-if="field.type === 'textarea'"
                                v-model="form.settings[field.key]"
                                rows="4"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
                            />

                            <!-- Text input (default) -->
                            <input
                                v-else
                                v-model="form.settings[field.key]"
                                type="text"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
                            />

                            <p v-if="field.key === 'seo.meta_title_template'" class="text-xs text-gray-400">
                                Use %title% and %site_name% as placeholders
                            </p>
                            <p v-if="field.key === 'mail.password'" class="text-xs text-gray-400">
                                Stored as plain text — use app password or API key
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
