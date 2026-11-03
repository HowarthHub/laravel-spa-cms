<script setup>
import { ref } from 'vue';

defineProps({
    data: { type: Object, required: true },
});

const expandedCard = ref(null);

function toggleCard(index) {
    expandedCard.value = expandedCard.value === index ? null : index;
}

const colClasses = {
    2: 'sm:grid-cols-2',
    3: 'sm:grid-cols-2 lg:grid-cols-3',
    4: 'sm:grid-cols-2 lg:grid-cols-4',
};
</script>

<template>
    <div v-scroll-animate class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <!-- Section heading -->
        <div v-if="data.heading" class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight sm:text-4xl">{{ data.heading }}</h2>
            <p v-if="data.subheading" class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">{{ data.subheading }}</p>
        </div>

        <!-- Cards -->
        <div class="flex flex-wrap justify-center gap-8">
            <div
                v-for="(card, index) in data.cards"
                :key="index"
                class="group w-full rounded-2xl bg-white shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300"
                :class="colClasses[data.columns || 3]?.includes('lg:grid-cols-4') ? 'md:w-[calc(50%-1rem)] lg:w-[calc(25%-1.5rem)]' : colClasses[data.columns || 3]?.includes('lg:grid-cols-3') ? 'md:w-[calc(50%-1rem)] lg:w-[calc(33.333%-1.34rem)]' : 'md:w-[calc(50%-1rem)]'"
            >
                <!-- Image / Initials placeholder -->
                <div class="relative h-56 overflow-hidden bg-gradient-to-br from-gray-700 to-gray-900">
                    <img
                        v-if="card.image"
                        :src="card.image"
                        :alt="card.title"
                        class="h-full w-full object-cover object-top group-hover:scale-105 transition-transform duration-500"
                    />
                    <div v-else class="flex h-full items-center justify-center">
                        <span class="text-6xl font-bold text-white/20">{{ card.title?.split(' ').map(w => w[0]).join('').slice(0, 2) }}</span>
                    </div>
                    <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/40 to-transparent"></div>
                </div>

                <!-- Content -->
                <div class="p-6 sm:p-8">
                    <div class="mb-4">
                        <component :is="card.link ? 'a' : 'div'" :href="card.link || undefined">
                            <h3 class="text-xl font-bold text-gray-900" :class="{ 'group-hover:text-gray-700': card.link }">{{ card.title }}</h3>
                        </component>
                        <p v-if="card.subtitle" class="mt-1 text-sm font-medium text-gray-500">{{ card.subtitle }}</p>
                    </div>

                    <p class="text-sm text-gray-600 leading-relaxed">{{ card.description }}</p>

                    <!-- Expandable extended text -->
                    <div v-if="card.extended" class="mt-4">
                        <p v-if="expandedCard === index" class="text-sm text-gray-600 leading-relaxed">
                            {{ card.extended }}
                        </p>
                        <button
                            @click="toggleCard(index)"
                            class="mt-3 inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors"
                        >
                            {{ expandedCard === index ? 'Read less' : 'Read more' }}
                            <svg
                                class="ml-1 h-4 w-4 transition-transform duration-200"
                                :class="{ 'rotate-180': expandedCard === index }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
