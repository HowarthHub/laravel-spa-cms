<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';

const form = useForm({
    name: '',
    handle: '',
    success_message: '',
    notification_email: '',
    is_active: true,
    fields: [],
});

let manualHandle = false;

const generateHandle = (name) => {
    return name
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '_')
        .replace(/^_+|_+$/g, '');
};

watch(() => form.name, (val) => {
    if (!manualHandle) {
        form.handle = generateHandle(val);
    }
});

const onHandleInput = () => {
    manualHandle = true;
};

const addField = () => {
    form.fields.push({
        label: '',
        type: 'text',
        required: false,
        placeholder: '',
        options: '',
    });
};

const removeField = (index) => {
    form.fields.splice(index, 1);
};

const moveField = (index, direction) => {
    const newIndex = index + direction;
    if (newIndex < 0 || newIndex >= form.fields.length) return;
    const temp = form.fields[index];
    form.fields[index] = form.fields[newIndex];
    form.fields[newIndex] = temp;
};

const submit = () => {
    const data = {
        ...form.data(),
        fields: form.fields.map((f) => ({
            ...f,
            options: f.type === 'select' && f.options
                ? f.options.split('\n').map((o) => o.trim()).filter(Boolean)
                : [],
        })),
    };
    form.transform(() => data).post('/admin/forms');
};
</script>

<template>
    <Head title="Create Form" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[
                { label: 'Dashboard', href: '/admin' },
                { label: 'Forms', href: '/admin/forms' },
                { label: 'Create' },
            ]" />
        </template>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Create Form</h1>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-5">
                    <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                autofocus
                                placeholder="e.g. Contact Form"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Handle</label>
                            <input
                                v-model="form.handle"
                                @input="onHandleInput"
                                type="text"
                                required
                                placeholder="e.g. contact_form"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none font-mono dark:bg-gray-700 dark:text-gray-100"
                                :class="{ 'border-red-500': form.errors.handle }"
                            />
                            <p v-if="form.errors.handle" class="mt-1 text-sm text-red-600">{{ form.errors.handle }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Auto-generated from name, or set manually.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Success Message</label>
                            <textarea
                                v-model="form.success_message"
                                rows="3"
                                placeholder="Thank you for your submission!"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                            />
                            <p v-if="form.errors.success_message" class="mt-1 text-sm text-red-600">{{ form.errors.success_message }}</p>
                        </div>
                    </div>

                    <!-- Field Builder -->
                    <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Fields</h2>
                            <button type="button" @click="addField"
                                class="rounded-md bg-cyan-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-cyan-700">
                                Add Field
                            </button>
                        </div>

                        <p v-if="form.errors.fields" class="mb-3 text-sm text-red-600">{{ form.errors.fields }}</p>

                        <div v-if="form.fields.length === 0" class="text-center py-8 text-sm text-gray-500 dark:text-gray-400">
                            No fields added yet. Click "Add Field" to get started.
                        </div>

                        <div class="space-y-4">
                            <div
                                v-for="(field, index) in form.fields"
                                :key="index"
                                class="rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-4"
                            >
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Field {{ index + 1 }}</span>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="moveField(index, -1)" :disabled="index === 0"
                                            class="rounded p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 disabled:opacity-30 disabled:cursor-not-allowed"
                                            title="Move Up">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                        </button>
                                        <button type="button" @click="moveField(index, 1)" :disabled="index === form.fields.length - 1"
                                            class="rounded p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 disabled:opacity-30 disabled:cursor-not-allowed"
                                            title="Move Down">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </button>
                                        <button type="button" @click="removeField(index)"
                                            class="rounded p-1 text-red-400 hover:text-red-600"
                                            title="Remove Field">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Label</label>
                                        <input
                                            v-model="field.label"
                                            type="text"
                                            required
                                            placeholder="e.g. Full Name"
                                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                                        <select
                                            v-model="field.type"
                                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                                        >
                                            <option value="text">Text</option>
                                            <option value="email">Email</option>
                                            <option value="tel">Phone</option>
                                            <option value="textarea">Textarea</option>
                                            <option value="select">Select</option>
                                            <option value="checkbox">Checkbox</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Placeholder</label>
                                        <input
                                            v-model="field.placeholder"
                                            type="text"
                                            placeholder="Optional placeholder text"
                                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                                        />
                                    </div>
                                    <div class="flex items-end">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input
                                                v-model="field.required"
                                                type="checkbox"
                                                class="rounded border-gray-300 dark:border-gray-600 text-cyan-600 focus:ring-cyan-500"
                                            />
                                            <span class="text-sm text-gray-700 dark:text-gray-300">Required</span>
                                        </label>
                                    </div>
                                </div>

                                <div v-if="field.type === 'select'" class="mt-3">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Options (one per line)</label>
                                    <textarea
                                        v-model="field.options"
                                        rows="3"
                                        placeholder="Option 1&#10;Option 2&#10;Option 3"
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column / Sidebar -->
                <div class="space-y-5">
                    <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Settings</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notification Email</label>
                            <input
                                v-model="form.notification_email"
                                type="email"
                                placeholder="admin@example.com"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                            />
                            <p v-if="form.errors.notification_email" class="mt-1 text-sm text-red-600">{{ form.errors.notification_email }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Receive an email when a submission is received.</p>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
                            <button type="button" @click="form.is_active = !form.is_active"
                                :class="form.is_active ? 'bg-cyan-600' : 'bg-gray-300 dark:bg-gray-600'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out">
                                <span
                                    :class="form.is_active ? 'translate-x-5' : 'translate-x-0'"
                                    class="pointer-events-none inline-block h-5 w-5 translate-y-0.5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out ml-0.5"
                                />
                            </button>
                        </div>
                    </div>

                    <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <button type="submit" :disabled="form.processing"
                            class="w-full rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white hover:bg-cyan-700 disabled:opacity-50">
                            {{ form.processing ? 'Saving...' : 'Create Form' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
