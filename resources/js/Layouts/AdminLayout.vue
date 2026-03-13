<script setup>
import { ref, provide } from 'vue';
import AppSidebarComponent from '@/Components/Admin/Shared/AppSidebarComponent.vue';
import AppTopbarComponent from '@/Components/Admin/Shared/AppTopbarComponent.vue';
import FlashMessageComponent from '@/Components/Admin/Shared/FlashMessageComponent.vue';

const sidebarOpen = ref(false);
provide('sidebarOpen', sidebarOpen);
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Mobile backdrop -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-30 bg-black/50 lg:hidden"
            @click="sidebarOpen = false"
        />

        <AppSidebarComponent :open="sidebarOpen" @close="sidebarOpen = false" />

        <div class="lg:pl-64">
            <AppTopbarComponent @toggle-sidebar="sidebarOpen = !sidebarOpen">
                <template #breadcrumb>
                    <slot name="breadcrumb" />
                </template>
            </AppTopbarComponent>

            <main class="p-4 sm:p-6">
                <FlashMessageComponent />
                <slot />
            </main>
        </div>
    </div>
</template>
