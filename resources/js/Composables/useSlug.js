import { ref, watch } from 'vue';

export function useSlug(titleRef, initialSlug = '') {
    const slug = ref(initialSlug);
    const manuallyEdited = ref(!!initialSlug);

    const slugify = (text) => {
        return text
            .toString()
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-')
            .replace(/[^\w-]+/g, '')
            .replace(/--+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    };

    let debounceTimer = null;

    watch(titleRef, (newTitle) => {
        if (manuallyEdited.value) return;

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            slug.value = slugify(newTitle || '');
        }, 300);
    });

    const setManual = (value) => {
        manuallyEdited.value = true;
        slug.value = slugify(value);
    };

    const resetAutoGenerate = () => {
        manuallyEdited.value = false;
    };

    return { slug, manuallyEdited, setManual, resetAutoGenerate };
}
