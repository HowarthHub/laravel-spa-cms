<script setup>
import { ref, provide, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppSidebarComponent from '@/Components/Admin/Shared/AppSidebarComponent.vue';
import AppTopbarComponent from '@/Components/Admin/Shared/AppTopbarComponent.vue';
import FlashMessageComponent from '@/Components/Admin/Shared/FlashMessageComponent.vue';

const sidebarOpen = ref(false);
provide('sidebarOpen', sidebarOpen);

const site = computed(() => usePage().props.site || {});
</script>

<template>
    <Head v-if="site.favicon">
        <link rel="icon" :href="site.favicon" head-key="favicon" />
    </Head>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Mobile backdrop -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="sidebarOpen"
                class="fixed inset-0 z-30 bg-black/50 lg:hidden"
                @click="sidebarOpen = false"
            />
        </Transition>

        <AppSidebarComponent :open="sidebarOpen" @close="sidebarOpen = false" />

        <div class="lg:pl-64">
            <AppTopbarComponent @toggle-sidebar="sidebarOpen = !sidebarOpen">
                <template #breadcrumb>
                    <slot name="breadcrumb" />
                </template>
            </AppTopbarComponent>

            <main class="p-4 sm:p-6">
                <FlashMessageComponent />
                <div class="animate-fade-in-up">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
