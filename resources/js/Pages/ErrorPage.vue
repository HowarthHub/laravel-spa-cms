<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    status: Number,
});

const title = computed(() => ({
    403: 'Forbidden',
    404: 'Page Not Found',
    500: 'Server Error',
    503: 'Service Unavailable',
})[props.status] || 'Error');

const description = computed(() => ({
    403: 'Sorry, you don\'t have permission to access this page.',
    404: 'Sorry, the page you\'re looking for doesn\'t exist or has been moved.',
    500: 'Something went wrong on our end. Please try again later.',
    503: 'We\'re currently performing maintenance. Please check back shortly.',
})[props.status] || 'An unexpected error occurred.');
</script>

<template>
    <Head :title="title" />
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="text-center max-w-md">
            <p class="text-6xl font-bold text-cyan-600">{{ status }}</p>
            <h1 class="mt-4 text-2xl font-semibold text-gray-900">{{ title }}</h1>
            <p class="mt-3 text-gray-600">{{ description }}</p>
            <div class="mt-8 flex items-center justify-center gap-4">
                <Link href="/" class="rounded-md bg-cyan-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-cyan-700">
                    Go Home
                </Link>
                <button @click="$inertia?.visit(window.location.href)" class="rounded-md border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    Try Again
                </button>
            </div>
        </div>
    </div>
</template>
