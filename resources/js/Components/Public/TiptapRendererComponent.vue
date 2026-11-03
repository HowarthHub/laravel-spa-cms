<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import { watch } from 'vue';

const props = defineProps({
    content: {
        type: [Object, Array, null],
        default: null,
    },
});

const editor = useEditor({
    extensions: [
        StarterKit,
        Image,
        Link.configure({ openOnClick: true }),
    ],
    content: props.content,
    editable: false,
});

watch(() => props.content, (val) => {
    if (editor.value) {
        editor.value.commands.setContent(val || '');
    }
});
</script>

<template>
    <div class="prose prose-gray max-w-none prose-img:rounded-lg prose-a:text-blue-600 hover:prose-a:text-blue-800">
        <EditorContent :editor="editor" />
    </div>
</template>
