<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    post: {
        type: Object,
        required: true,
    },
    index: {
        type: Number,
        default: 0,
    },
});

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}
</script>

<template>
    <article class="group overflow-hidden rounded-lg border border-gray-200 bg-white transition-shadow hover:shadow-lg animate-fade-in-up" :class="`stagger-${index + 1}`">
        <Link :href="`/blog/${post.slug}`">
            <div v-if="post.featured_image" class="aspect-video overflow-hidden">
                <img :src="post.featured_image" :alt="post.title" class="h-full w-full object-cover transition-transform group-hover:scale-105" />
            </div>
            <div v-else class="aspect-video bg-gray-100 flex items-center justify-center">
                <span class="text-gray-400 text-sm">No image</span>
            </div>
        </Link>

        <div class="p-5">
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                <time v-if="post.published_at" :datetime="post.published_at">{{ formatDate(post.published_at) }}</time>
                <span v-if="post.author">· {{ post.author.name }}</span>
            </div>

            <Link :href="`/blog/${post.slug}`">
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                    {{ post.title }}
                </h3>
            </Link>

            <p v-if="post.excerpt" class="mt-2 text-sm text-gray-600 line-clamp-3">{{ post.excerpt }}</p>

            <div v-if="post.categories?.length" class="mt-4 flex flex-wrap gap-2">
                <span v-for="category in post.categories" :key="category.id" class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700">
                    {{ category.name }}
                </span>
            </div>
        </div>
    </article>
</template>
