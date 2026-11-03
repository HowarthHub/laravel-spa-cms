<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import { watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: [Object, Array, null],
        default: null,
    },
});

const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
    extensions: [
        StarterKit,
        Image,
        Link.configure({ openOnClick: false }),
    ],
    content: props.modelValue,
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getJSON());
    },
});

watch(() => props.modelValue, (val) => {
    if (editor.value && JSON.stringify(editor.value.getJSON()) !== JSON.stringify(val)) {
        editor.value.commands.setContent(val || '');
    }
});
</script>

<template>
    <div class="rounded-md border border-gray-300 dark:border-gray-600 overflow-hidden">
        <div v-if="editor" class="flex flex-wrap items-center gap-0.5 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 px-2 py-1.5">
            <button type="button" @click="editor.chain().focus().toggleBold().run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('bold') }"
                class="rounded px-2 py-1 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">B</button>
            <button type="button" @click="editor.chain().focus().toggleItalic().run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('italic') }"
                class="rounded px-2 py-1 text-sm italic text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">I</button>
            <span class="mx-1 h-5 w-px bg-gray-300 dark:bg-gray-600" />
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('heading', { level: 2 }) }"
                class="rounded px-2 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">H2</button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('heading', { level: 3 }) }"
                class="rounded px-2 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">H3</button>
            <span class="mx-1 h-5 w-px bg-gray-300 dark:bg-gray-600" />
            <button type="button" @click="editor.chain().focus().toggleBulletList().run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('bulletList') }"
                class="rounded px-2 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">UL</button>
            <button type="button" @click="editor.chain().focus().toggleOrderedList().run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('orderedList') }"
                class="rounded px-2 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">OL</button>
            <span class="mx-1 h-5 w-px bg-gray-300 dark:bg-gray-600" />
            <button type="button" @click="editor.chain().focus().toggleBlockquote().run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('blockquote') }"
                class="rounded px-2 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Quote</button>
            <button type="button" @click="setLink"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('link') }"
                class="rounded px-2 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Link</button>
        </div>
        <EditorContent :editor="editor" class="prose prose-sm dark:prose-invert max-w-none p-4 min-h-[200px] focus:outline-none dark:bg-gray-700 dark:text-gray-100" />
    </div>
</template>

<script>
export default {
    methods: {
        setLink() {
            const url = window.prompt('URL');
            if (url) {
                this.editor.chain().focus().setLink({ href: url }).run();
            }
        },
    },
};
</script>
