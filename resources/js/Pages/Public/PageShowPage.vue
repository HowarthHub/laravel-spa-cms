<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import TiptapRendererComponent from '@/Components/Public/TiptapRendererComponent.vue';
import BlockRendererComponent from '@/Components/Public/BlockRenderer/BlockRendererComponent.vue';
import PostCardComponent from '@/Components/Public/PostCardComponent.vue';
import ServiceCardComponent from '@/Components/Public/ServiceCardComponent.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    page: {
        type: Object,
        required: true,
    },
    template: {
        type: String,
        default: 'default',
    },
    meta: {
        type: Object,
        default: () => ({}),
    },
    posts: {
        type: Object,
        default: null,
    },
    services: {
        type: Array,
        default: null,
    },
    form: {
        type: [Object, null],
        default: null,
    },
});

const isFullWidth = computed(() => props.template === 'page-builder' || props.template === 'landing');

const isBlocks = computed(() => {
    const c = props.page.content;
    return Array.isArray(c) && c.length > 0 && c[0]?.id && c[0]?.type;
});

// Dynamic form handling
const formData = ref(null);
const formSubmitted = ref(false);

if (props.form?.fields) {
    const initial = {};
    props.form.fields.forEach(field => {
        const key = field.label.toLowerCase().replace(/\s+/g, '_');
        initial[key] = '';
    });
    formData.value = useForm(initial);
}

function submitForm() {
    if (formData.value && props.form) {
        formData.value.post(`/forms/${props.form.id}/submit`, {
            onSuccess: () => {
                formSubmitted.value = true;
                formData.value.reset();
            },
        });
    }
}

function fieldKey(label) {
    return label.toLowerCase().replace(/\s+/g, '_');
}
</script>

<template>
    <PublicLayout :meta="meta">
        <!-- Featured Image Banner -->
        <div v-if="page.featured_image" class="relative h-64 sm:h-80 lg:h-96 overflow-hidden bg-gray-900">
            <img :src="page.featured_image" :alt="page.title" class="h-full w-full object-cover opacity-60" />
            <div class="absolute inset-0 flex items-center justify-center">
                <h1 v-scroll-animate class="text-4xl sm:text-5xl font-bold text-white text-center px-4">{{ page.title }}</h1>
            </div>
        </div>

        <!-- Block-based Page Builder content -->
        <BlockRendererComponent v-if="isBlocks" :blocks="page.content" />

        <!-- Standard content -->
        <div v-else :class="isFullWidth ? '' : 'mx-auto max-w-4xl px-4 sm:px-6 lg:px-8'" class="py-12">
            <div :class="isFullWidth ? 'mx-auto max-w-7xl px-4 sm:px-6 lg:px-8' : ''">
                <h1 v-if="!page.featured_image" class="text-4xl font-bold text-gray-900 mb-8">{{ page.title }}</h1>

                <TiptapRendererComponent v-if="page.content" :content="page.content" />
            </div>

            <!-- Blog Posts (blog template) -->
            <div v-if="template === 'blog' && posts?.data?.length" class="mt-12">
                <div :class="isFullWidth ? 'mx-auto max-w-7xl px-4 sm:px-6 lg:px-8' : ''">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8">Latest Posts</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <PostCardComponent v-for="(post, i) in posts.data" :key="post.id" :post="post" :index="i" />
                    </div>
                </div>
            </div>

            <!-- Services (services template) -->
            <div v-if="template === 'services' && services?.length" class="mt-12">
                <div :class="isFullWidth ? 'mx-auto max-w-7xl px-4 sm:px-6 lg:px-8' : ''">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8">Our Services</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <ServiceCardComponent v-for="(service, i) in services" :key="service.id" :service="service" :index="i" />
                    </div>
                </div>
            </div>

        </div>

        <!-- Contact Form (contact template) — outside v-if/v-else so it renders with both block and standard content -->
        <div v-if="template === 'contact' && form" class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 py-12">
            <!-- Success message -->
            <div v-if="formSubmitted" v-scroll-animate class="rounded-xl border border-green-200 bg-green-50 p-6 sm:p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-semibold text-green-800 mb-2">Thank You</h3>
                <p class="text-sm text-green-700">{{ form.success_message || 'Your message has been sent. We\'ll be in touch soon.' }}</p>
            </div>

            <form v-else v-scroll-animate @submit.prevent="submitForm" class="rounded-xl border border-gray-200 bg-gray-50 p-6 sm:p-8 space-y-5">
                <div v-for="field in form.fields" :key="field.label">
                    <!-- Checkbox -->
                    <div v-if="field.type === 'checkbox'" class="flex items-start gap-3">
                        <input
                            :id="fieldKey(field.label)"
                            type="checkbox"
                            v-model="formData[fieldKey(field.label)]"
                            class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />
                        <label :for="fieldKey(field.label)" class="text-sm font-medium text-gray-700">
                            {{ field.label }}
                            <span v-if="field.required" class="text-red-500">*</span>
                        </label>
                    </div>

                    <!-- Textarea -->
                    <template v-else-if="field.type === 'textarea'">
                        <label :for="fieldKey(field.label)" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ field.label }}
                            <span v-if="field.required" class="text-red-500">*</span>
                        </label>
                        <textarea
                            :id="fieldKey(field.label)"
                            v-model="formData[fieldKey(field.label)]"
                            rows="5"
                            :placeholder="field.placeholder"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 placeholder-gray-400 shadow-sm transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none"
                        />
                    </template>

                    <!-- Text / Email / Tel / Other -->
                    <template v-else>
                        <label :for="fieldKey(field.label)" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ field.label }}
                            <span v-if="field.required" class="text-red-500">*</span>
                        </label>
                        <input
                            :id="fieldKey(field.label)"
                            :type="field.type || 'text'"
                            v-model="formData[fieldKey(field.label)]"
                            :placeholder="field.placeholder"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 placeholder-gray-400 shadow-sm transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none"
                        />
                    </template>

                    <p v-if="formData.errors[fieldKey(field.label)]" class="mt-1.5 text-sm text-red-600">
                        {{ formData.errors[fieldKey(field.label)] }}
                    </p>
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        :disabled="formData.processing"
                        class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ formData.processing ? 'Sending...' : 'Send Message' }}
                    </button>
                </div>
            </form>
        </div>
    </PublicLayout>
</template>
