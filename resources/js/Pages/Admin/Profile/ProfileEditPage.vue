<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumbComponent from '@/Components/Admin/Shared/AppBreadcrumbComponent.vue';
import MediaPickerComponent from '@/Components/Admin/Forms/MediaPickerComponent.vue';

const props = defineProps({
    user: Object,
});

const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
    avatar: props.user.avatar || '',
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updateProfile = () => {
    profileForm.put('/admin/profile');
};

const updatePassword = () => {
    passwordForm.put('/admin/profile/password', {
        onSuccess: () => passwordForm.reset(),
    });
};
</script>

<template>
    <Head title="My Profile" />
    <AdminLayout>
        <template #breadcrumb>
            <AppBreadcrumbComponent :items="[{ label: 'Dashboard', href: '/admin' }, { label: 'Profile' }]" />
        </template>

        <div class="max-w-2xl space-y-6">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">My Profile</h1>

            <!-- Profile info -->
            <form @submit.prevent="updateProfile" class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Profile Information</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                    <input v-model="profileForm.name" type="text" required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm dark:text-gray-100 shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none"
                        :class="{ 'border-red-500': profileForm.errors.name }" />
                    <p v-if="profileForm.errors.name" class="mt-1 text-sm text-red-600">{{ profileForm.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input v-model="profileForm.email" type="email" required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm dark:text-gray-100 shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none"
                        :class="{ 'border-red-500': profileForm.errors.email }" />
                    <p v-if="profileForm.errors.email" class="mt-1 text-sm text-red-600">{{ profileForm.errors.email }}</p>
                </div>

                <MediaPickerComponent v-model="profileForm.avatar" label="Avatar" />

                <div class="flex justify-end">
                    <button type="submit" :disabled="profileForm.processing"
                        class="inline-flex items-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700 disabled:opacity-50">
                        Save Profile
                    </button>
                </div>
            </form>

            <!-- Password -->
            <form @submit.prevent="updatePassword" class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Change Password</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
                    <input v-model="passwordForm.current_password" type="password" required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm dark:text-gray-100 shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none"
                        :class="{ 'border-red-500': passwordForm.errors.current_password }" />
                    <p v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-600">{{ passwordForm.errors.current_password }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                        <input v-model="passwordForm.password" type="password" required
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm dark:text-gray-100 shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none"
                            :class="{ 'border-red-500': passwordForm.errors.password }" />
                        <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-600">{{ passwordForm.errors.password }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                        <input v-model="passwordForm.password_confirmation" type="password" required
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm dark:text-gray-100 shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none" />
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" :disabled="passwordForm.processing"
                        class="inline-flex items-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-cyan-700 disabled:opacity-50">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
