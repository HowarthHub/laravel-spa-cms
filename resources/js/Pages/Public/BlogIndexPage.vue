<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PostCardComponent from '@/Components/Public/PostCardComponent.vue';
import TiptapRendererComponent from '@/Components/Public/TiptapRendererComponent.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    posts: {
        type: Object,
        required: true,
    },
    page: {
        type: [Object, null],
        default: null,
    },
    meta: {
        type: Object,
        default: () => ({}),
    },
});
</script>

<template>
    <PublicLayout :meta="meta">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ page?.title || 'Blog' }}</h1>

            <div v-if="page?.excerpt" class="mb-8 text-lg text-gray-600">
                {{ page.excerpt }}
            </div>

            <div v-if="page?.content" class="mb-12">
                <TiptapRendererComponent :content="page.content" />
            </div>

            <div v-if="posts.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <PostCardComponent v-for="post in posts.data" :key="post.id" :post="post" />
            </div>

            <p v-else class="text-gray-600">No posts yet. Check back soon.</p>

            <!-- Pagination -->
            <nav v-if="posts.links && posts.last_page > 1" class="mt-12 flex items-center justify-center gap-2">
                <template v-for="link in posts.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="inline-flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors"
                        :class="link.active ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
                        v-html="link.label"
                    />
                    <span
                        v-else
                        class="inline-flex items-center rounded-md px-3 py-2 text-sm font-medium text-gray-400"
                        v-html="link.label"
                    />
                </template>
            </nav>
        </div>
    </PublicLayout>
</template>
