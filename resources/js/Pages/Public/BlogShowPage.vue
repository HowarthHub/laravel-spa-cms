<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import TiptapRendererComponent from '@/Components/Public/TiptapRendererComponent.vue';
import BlockRendererComponent from '@/Components/Public/BlockRenderer/BlockRendererComponent.vue';
import PostCardComponent from '@/Components/Public/PostCardComponent.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
    relatedPosts: {
        type: Array,
        default: () => [],
    },
    meta: {
        type: Object,
        default: () => ({}),
    },
});

const isBlocks = computed(() =>
    Array.isArray(props.post.content) && props.post.content[0]?.id && props.post.content[0]?.type
);

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}
</script>

<template>
    <PublicLayout :meta="meta">
        <article>
            <!-- Featured Image -->
            <div v-if="post.featured_image" class="relative h-64 sm:h-80 lg:h-[28rem] overflow-hidden bg-gray-900">
                <img :src="post.featured_image" :alt="post.title" class="h-full w-full object-cover opacity-70" />
            </div>

            <!-- Header -->
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 pt-12" :class="{ 'pb-12': !isBlocks }">
                <!-- Meta -->
                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 mb-4">
                    <time v-if="post.published_at" :datetime="post.published_at">{{ formatDate(post.published_at) }}</time>
                    <span v-if="post.author">· {{ post.author.name }}</span>
                </div>

                <h1 class="text-4xl font-bold text-gray-900 mb-6">{{ post.title }}</h1>

                <!-- Categories & Tags -->
                <div v-if="post.categories?.length || post.tags?.length" class="flex flex-wrap gap-2" :class="{ 'mb-8': !isBlocks }">
                    <span v-for="category in post.categories" :key="'cat-' + category.id" class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">
                        {{ category.name }}
                    </span>
                    <span v-for="tag in post.tags" :key="'tag-' + tag.id" class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                        {{ tag.name }}
                    </span>
                </div>
            </div>

            <!-- Blocks content (outside constrained wrapper — blocks handle their own widths) -->
            <BlockRendererComponent v-if="isBlocks" :blocks="post.content" />

            <!-- Legacy TipTap content (inside constrained wrapper) -->
            <div v-else-if="post.content" class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <TiptapRendererComponent :content="post.content" />
            </div>

            <!-- Back Link -->
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 pb-12">
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <Link href="/blog" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                        &larr; Back to Blog
                    </Link>
                </div>
            </div>

            <!-- Related Posts -->
            <div v-if="relatedPosts.length" class="border-t border-gray-200 bg-gray-50">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Posts</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <PostCardComponent v-for="related in relatedPosts" :key="related.id" :post="related" />
                    </div>
                </div>
            </div>
        </article>
    </PublicLayout>
</template>
