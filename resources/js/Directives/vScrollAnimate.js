/**
 * v-scroll-animate directive
 *
 * Reveals elements with a subtle animation when they scroll into view.
 * Uses IntersectionObserver — fires once, then disconnects.
 *
 * Usage:
 *   <div v-scroll-animate>               → default fade-in-up
 *   <div v-scroll-animate="'fade-in'">   → specific animation
 *   <div v-scroll-animate.delay-1>       → with stagger delay
 *   <div v-scroll-animate.delay-3="'fade-in-up'"> → both
 */

const animationMap = {
    'fade-in': 'animate-fade-in',
    'fade-in-up': 'animate-fade-in-up',
    'fade-in-down': 'animate-fade-in-down',
    'scale-in': 'animate-scale-in',
    'slide-in-left': 'animate-slide-in-left',
};

export const vScrollAnimate = {
    mounted(el, binding) {
        const animation = animationMap[binding.value] || animationMap['fade-in-up'];

        // Parse delay modifier (e.g. .delay-1, .delay-3)
        let delayClass = '';
        for (const mod in binding.modifiers) {
            if (mod.startsWith('delay-')) {
                delayClass = `stagger-${mod.replace('delay-', '')}`;
            }
        }

        // Start hidden
        el.style.opacity = '0';

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    if (delayClass) el.classList.add(delayClass);
                    el.classList.add(animation);
                    el.style.opacity = '';
                    observer.disconnect();
                }
            },
            { threshold: 0.1, rootMargin: '0px 0px -40px 0px' },
        );

        observer.observe(el);

        // Store for cleanup
        el._scrollObserver = observer;
    },

    unmounted(el) {
        if (el._scrollObserver) {
            el._scrollObserver.disconnect();
        }
    },
};
