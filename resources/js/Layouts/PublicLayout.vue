<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    meta: {
        type: Object,
        default: () => ({}),
    },
});

const page = usePage();
const site = computed(() => page.props.site || {});
const socialLinks = computed(() => page.props.socialLinks || {});
const navigation = computed(() => page.props.navigation || []);
const currentYear = new Date().getFullYear();
const searchQuery = ref('');

function submitSearch() {
    const q = searchQuery.value.trim();
    if (q) {
        router.get('/search', { q });
    }
}

function resolveUrl(item) {
    let url = item.url || '#';

    if (item.linkable_type && item.linkable) {
        const type = item.linkable_type;
        if (type.includes('PageModel')) {
            return `/${item.linkable.slug}`;
        }
        if (type.includes('PostModel')) {
            return `/blog/${item.linkable.slug}`;
        }
        if (type.includes('ServiceModel')) {
            return `/services/${item.linkable.slug}`;
        }
    }

    // Strip legacy /pages/ prefix from stored URLs
    if (url.startsWith('/pages/')) {
        return url.replace('/pages/', '/');
    }

    return url;
}

function isExternal(url) {
    return url && (url.startsWith('http://') || url.startsWith('https://'));
}

const socialPlatforms = [
    { key: 'facebook', label: 'Facebook' },
    { key: 'instagram', label: 'Instagram' },
    { key: 'twitter', label: 'X / Twitter' },
    { key: 'linkedin', label: 'LinkedIn' },
    { key: 'youtube', label: 'YouTube' },
];
</script>

<template>
    <div class="min-h-screen flex flex-col bg-white text-gray-900">
        <Head>
            <title>{{ meta.title }}</title>
            <meta v-if="meta.description" head-key="description" name="description" :content="meta.description" />
            <meta v-if="meta.ogImage" head-key="og:image" property="og:image" :content="meta.ogImage" />
            <link v-if="meta.canonicalUrl" rel="canonical" :href="meta.canonicalUrl" />
        </Head>

        <!-- Header -->
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <Link href="/" class="flex items-center gap-3">
                        <img v-if="site.logo" :src="site.logo" :alt="site.name" class="h-8 w-auto" />
                        <span class="text-xl font-bold text-gray-900">{{ site.name }}</span>
                    </Link>

                    <nav v-if="navigation.length" class="hidden md:flex items-center gap-6">
                        <template v-for="item in navigation" :key="item.id">
                            <div v-if="item.children?.length" class="relative group flex items-center">
                                <Link v-if="resolveUrl(item) !== '#'" :href="resolveUrl(item)" class="text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                                    {{ item.label }}
                                </Link>
                                <span v-else class="text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors cursor-default">
                                    {{ item.label }}
                                </span>
                                <div class="absolute left-0 top-full pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                                    <div class="rounded-lg border border-gray-200 bg-white py-2 shadow-lg min-w-[180px]">
                                        <template v-for="child in item.children" :key="child.id">
                                            <a v-if="isExternal(resolveUrl(child))" :href="resolveUrl(child)" :target="child.target || '_blank'" rel="noopener noreferrer" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                {{ child.label }}
                                            </a>
                                            <Link v-else :href="resolveUrl(child)" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                {{ child.label }}
                                            </Link>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <a v-else-if="isExternal(resolveUrl(item))" :href="resolveUrl(item)" :target="item.target || '_blank'" rel="noopener noreferrer" class="text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                                {{ item.label }}
                            </a>
                            <Link v-else :href="resolveUrl(item)" class="text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                                {{ item.label }}
                            </Link>
                        </template>
                    </nav>

                    <form @submit.prevent="submitSearch" class="hidden md:flex items-center ml-4">
                        <input
                            v-model="searchQuery"
                            type="search"
                            placeholder="Search..."
                            class="w-40 rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                        />
                    </form>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="bg-green-50 border-b border-green-200">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-3">
                <p class="text-sm text-green-800">{{ $page.props.flash.success }}</p>
            </div>
        </div>

        <!-- Main Content -->
        <main class="grow">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200 bg-gray-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <span class="text-lg font-bold text-gray-900">{{ site.name }}</span>
                        <p v-if="site.tagline" class="mt-2 text-sm text-gray-600">{{ site.tagline }}</p>
                        <p v-if="site.footer" class="mt-4 text-sm text-gray-600">{{ site.footer }}</p>
                    </div>

                    <div v-if="navigation.length">
                        <h3 class="text-sm font-semibold text-gray-900">Navigation</h3>
                        <ul class="mt-4 space-y-2">
                            <li v-for="item in navigation" :key="item.id">
                                <Link :href="resolveUrl(item)" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                                    {{ item.label }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Connect</h3>
                        <ul class="mt-4 space-y-2">
                            <template v-for="platform in socialPlatforms" :key="platform.key">
                                <li v-if="socialLinks[platform.key]">
                                    <a :href="socialLinks[platform.key]" target="_blank" rel="noopener noreferrer" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                                        {{ platform.label }}
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-200 pt-8 text-center">
                    <p class="text-sm text-gray-500">&copy; {{ currentYear }} {{ site.name }}. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>
