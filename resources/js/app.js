import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { useDarkMode } from '@/Composables/useDarkMode.js';

createInertiaApp({
    title: (title) => title ? `${title} - Laravel CMS` : 'Laravel CMS',
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);

        // Initialise dark mode watcher
        useDarkMode();

        return app;
    },
    progress: {
        color: '#06b6d4',
    },
});
