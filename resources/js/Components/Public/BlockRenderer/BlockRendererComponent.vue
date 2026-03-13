<script setup>
import HeroBlock from './Blocks/HeroBlock.vue';
import RichTextBlock from './Blocks/RichTextBlock.vue';
import ImageBlock from './Blocks/ImageBlock.vue';
import TwoColumnBlock from './Blocks/TwoColumnBlock.vue';
import ThreeColumnBlock from './Blocks/ThreeColumnBlock.vue';
import CallToActionBlock from './Blocks/CallToActionBlock.vue';
import FeatureGridBlock from './Blocks/FeatureGridBlock.vue';
import TestimonialBlock from './Blocks/TestimonialBlock.vue';
import SpacerBlock from './Blocks/SpacerBlock.vue';
import ChecklistBlock from './Blocks/ChecklistBlock.vue';

defineProps({
    blocks: { type: Array, required: true },
});

const blockComponents = {
    hero: HeroBlock,
    richText: RichTextBlock,
    image: ImageBlock,
    twoColumn: TwoColumnBlock,
    threeColumn: ThreeColumnBlock,
    callToAction: CallToActionBlock,
    featureGrid: FeatureGridBlock,
    testimonial: TestimonialBlock,
    spacer: SpacerBlock,
    checklist: ChecklistBlock,
};
</script>

<template>
    <div>
        <component
            v-for="block in blocks"
            :key="block.id"
            :is="blockComponents[block.type]"
            :data="block.data"
        />
    </div>
</template>
