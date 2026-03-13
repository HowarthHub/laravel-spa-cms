<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    siteName: {
        type: String,
        default: 'Our Site',
    },
    message: {
        type: String,
        default: 'We are currently performing scheduled maintenance. We\'ll be back shortly.',
    },
});
</script>

<template>
    <Head :title="`Maintenance - ${siteName}`" />

    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-lg text-center">
            <h1 class="text-2xl font-bold text-gray-900">{{ siteName }}</h1>

            <div class="mt-8">
                <svg class="mx-auto h-16 w-16 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>

            <h2 class="mt-6 text-xl font-semibold text-gray-700">We'll be back soon</h2>

            <p class="mt-4 text-gray-500 leading-relaxed">{{ message }}</p>
        </div>
    </div>
</template>
