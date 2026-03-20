<script setup>
const props = defineProps({
    modelValue: { type: Object, required: true },
});

const emit = defineEmits(['update:modelValue']);

function update(key, value) {
    emit('update:modelValue', { ...props.modelValue, [key]: value });
}

function updateCard(index, key, value) {
    const cards = props.modelValue.cards.map((c, i) =>
        i === index ? { ...c, [key]: value } : c
    );
    emit('update:modelValue', { ...props.modelValue, cards });
}

function addCard() {
    const cards = [...props.modelValue.cards, { image: '', title: '', subtitle: '', description: '', extended: '', link: '' }];
    emit('update:modelValue', { ...props.modelValue, cards });
}

function removeCard(index) {
    const cards = props.modelValue.cards.filter((_, i) => i !== index);
    emit('update:modelValue', { ...props.modelValue, cards });
}
</script>

<template>
    <div class="space-y-4">
        <!-- Grid settings -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Section Heading (optional)</label>
                <input
                    :value="modelValue.heading"
                    @input="update('heading', $event.target.value)"
                    type="text"
                    placeholder="e.g. Our Team, Features..."
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Columns (2–4)</label>
                <select
                    :value="modelValue.columns"
                    @change="update('columns', parseInt($event.target.value))"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                >
                    <option :value="2">2 Columns</option>
                    <option :value="3">3 Columns</option>
                    <option :value="4">4 Columns</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Section Subheading (optional)</label>
            <input
                :value="modelValue.subheading"
                @input="update('subheading', $event.target.value)"
                type="text"
                class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
            />
        </div>

        <!-- Cards -->
        <div
            v-for="(card, index) in modelValue.cards"
            :key="index"
            class="rounded-md border border-gray-200 dark:border-gray-700 p-3 space-y-3"
        >
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Card {{ index + 1 }}</span>
                <button
                    type="button"
                    @click="removeCard(index)"
                    class="text-sm text-red-600 hover:text-red-700 dark:text-red-400"
                >
                    Remove
                </button>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Image URL (optional)</label>
                <input
                    :value="card.image"
                    @input="updateCard(index, 'image', $event.target.value)"
                    type="text"
                    placeholder="/images/..."
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Title</label>
                    <input
                        :value="card.title"
                        @input="updateCard(index, 'title', $event.target.value)"
                        type="text"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                    />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Subtitle (optional)</label>
                    <input
                        :value="card.subtitle"
                        @input="updateCard(index, 'subtitle', $event.target.value)"
                        type="text"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                    />
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Description</label>
                <textarea
                    :value="card.description"
                    @input="updateCard(index, 'description', $event.target.value)"
                    rows="2"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                />
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Extended Text (optional — "Read more" expandable)</label>
                <textarea
                    :value="card.extended"
                    @input="updateCard(index, 'extended', $event.target.value)"
                    rows="2"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                />
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Link URL (optional)</label>
                <input
                    :value="card.link"
                    @input="updateCard(index, 'link', $event.target.value)"
                    type="text"
                    placeholder="/about, https://..."
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none dark:bg-gray-700 dark:text-gray-100"
                />
            </div>
        </div>

        <button
            type="button"
            @click="addCard"
            class="inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700"
        >
            + Add Card
        </button>
    </div>
</template>
