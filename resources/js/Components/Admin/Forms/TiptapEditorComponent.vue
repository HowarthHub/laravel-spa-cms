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
    <div class="rounded-md border border-gray-300 overflow-hidden">
        <div v-if="editor" class="flex flex-wrap items-center gap-0.5 border-b border-gray-200 bg-gray-50 px-2 py-1.5">
            <button type="button" @click="editor.chain().focus().toggleBold().run()"
                :class="{ 'bg-gray-200': editor.isActive('bold') }"
                class="rounded px-2 py-1 text-sm font-bold hover:bg-gray-200">B</button>
            <button type="button" @click="editor.chain().focus().toggleItalic().run()"
                :class="{ 'bg-gray-200': editor.isActive('italic') }"
                class="rounded px-2 py-1 text-sm italic hover:bg-gray-200">I</button>
            <span class="mx-1 h-5 w-px bg-gray-300" />
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                :class="{ 'bg-gray-200': editor.isActive('heading', { level: 2 }) }"
                class="rounded px-2 py-1 text-sm hover:bg-gray-200">H2</button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
                :class="{ 'bg-gray-200': editor.isActive('heading', { level: 3 }) }"
                class="rounded px-2 py-1 text-sm hover:bg-gray-200">H3</button>
            <span class="mx-1 h-5 w-px bg-gray-300" />
            <button type="button" @click="editor.chain().focus().toggleBulletList().run()"
                :class="{ 'bg-gray-200': editor.isActive('bulletList') }"
                class="rounded px-2 py-1 text-sm hover:bg-gray-200">UL</button>
            <button type="button" @click="editor.chain().focus().toggleOrderedList().run()"
                :class="{ 'bg-gray-200': editor.isActive('orderedList') }"
                class="rounded px-2 py-1 text-sm hover:bg-gray-200">OL</button>
            <span class="mx-1 h-5 w-px bg-gray-300" />
            <button type="button" @click="editor.chain().focus().toggleBlockquote().run()"
                :class="{ 'bg-gray-200': editor.isActive('blockquote') }"
                class="rounded px-2 py-1 text-sm hover:bg-gray-200">Quote</button>
            <button type="button" @click="setLink"
                :class="{ 'bg-gray-200': editor.isActive('link') }"
                class="rounded px-2 py-1 text-sm hover:bg-gray-200">Link</button>
        </div>
        <EditorContent :editor="editor" class="prose prose-sm max-w-none p-4 min-h-[200px] focus:outline-none" />
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
