<script setup>
import AppSidebarComponent from '@/Components/Admin/Shared/AppSidebarComponent.vue';
import AppTopbarComponent from '@/Components/Admin/Shared/AppTopbarComponent.vue';
import FlashMessageComponent from '@/Components/Admin/Shared/FlashMessageComponent.vue';
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <AppSidebarComponent />

        <div class="pl-64">
            <AppTopbarComponent>
                <template #breadcrumb>
                    <slot name="breadcrumb" />
                </template>
            </AppTopbarComponent>

            <main class="p-6">
                <FlashMessageComponent />
                <slot />
            </main>
        </div>
    </div>
</template>
