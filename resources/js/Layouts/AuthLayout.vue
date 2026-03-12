<script setup>
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 px-4 py-12">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laravel CMS</h1>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
                <slot />
            </div>
        </div>
    </div>
</template>
