<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const props = defineProps({
    token: String,
    email: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/reset-password', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Reset Password" />
    <AuthLayout>
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Reset your password</h2>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    autocomplete="username"
                    required
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
                    :class="{ 'border-red-500': form.errors.email }"
                />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                    required
                    autofocus
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
                    :class="{ 'border-red-500': form.errors.password }"
                />
                <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    required
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
                />
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span v-if="form.processing">Resetting...</span>
                <span v-else>Reset password</span>
            </button>

            <div class="text-center">
                <a href="/login" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Back to login
                </a>
            </div>
        </form>
    </AuthLayout>
</template>
