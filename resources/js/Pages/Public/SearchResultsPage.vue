<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    query: {
        type: String,
        default: '',
    },
    results: {
        type: Object,
        default: () => ({ pages: [], posts: [] }),
    },
    meta: {
        type: Object,
        default: () => ({}),
    },
});

function snippet(item) {
    return item.excerpt || item.short_description || item.meta_description || '';
}

const sections = [
    { key: 'pages', label: 'Pages', urlPrefix: '/' },
    { key: 'posts', label: 'Posts', urlPrefix: '/blog/' },
];
</script>

<template>
    <PublicLayout :meta="meta">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Search</h1>

            <p v-if="query" class="text-lg text-gray-600 mb-8">
                Results for <span class="font-semibold text-gray-900">"{{ query }}"</span>
            </p>
            <p v-else class="text-lg text-gray-600 mb-8">Enter a search term to find content.</p>

            <template v-if="query">
                <div
                    v-if="!results.pages.length && !results.posts.length"
                    class="rounded-lg border border-gray-200 bg-gray-50 p-12 text-center"
                >
                    <p class="text-lg text-gray-600">No results found for "{{ query }}".</p>
                    <p class="mt-2 text-sm text-gray-500">Try using different keywords or check your spelling.</p>
                </div>

                <div v-else class="space-y-10">
                    <section v-for="section in sections" :key="section.key">
                        <template v-if="results[section.key].length">
                            <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                                {{ section.label }}
                                <span class="text-base font-normal text-gray-500">({{ results[section.key].length }})</span>
                            </h2>

                            <ul class="divide-y divide-gray-200 rounded-lg border border-gray-200">
                                <li v-for="item in results[section.key]" :key="item.id">
                                    <Link
                                        :href="section.urlPrefix + item.slug"
                                        class="block px-6 py-4 hover:bg-gray-50 transition-colors"
                                    >
                                        <h3 class="text-lg font-medium text-blue-600 hover:text-blue-800">
                                            {{ item.title }}
                                        </h3>
                                        <p v-if="snippet(item)" class="mt-1 text-sm text-gray-600 line-clamp-2">
                                            {{ snippet(item) }}
                                        </p>
                                    </Link>
                                </li>
                            </ul>
                        </template>
                    </section>
                </div>
            </template>
        </div>
    </PublicLayout>
</template>
