const { getBlockType, registerBlockVariation } = wp.blocks;
const { __ } = wp.i18n;

const FAQ_CLASS = 'faq-schema-accordion';

const hasFaqClass = className => (className || '').split(/\s+/).includes(FAQ_CLASS);

wp.domReady(() => {
    if (!getBlockType('core/accordion')) {
        return;
    }

    registerBlockVariation('core/accordion', {
        name: 'faq-schema-block/faq-accordion',
        title: __('FAQ Schema Accordion', 'faq-schema-block'),
        description: __('Native accordion markup with FAQPage schema output.', 'faq-schema-block'),
        icon: 'editor-help',
        attributes: {
            className: FAQ_CLASS,
            headingLevel: 3,
            autoclose: false
        },
        innerBlocks: [
            ['core/accordion-item', { openByDefault: false }, [
                ['core/accordion-heading', {
                    title: __('Question with highlighted keywords', 'faq-schema-block')
                }],
                ['core/accordion-panel', {}, [
                    ['core/paragraph', {
                        placeholder: __('Write the answer…', 'faq-schema-block')
                    }]
                ]]
            ]]
        ],
        isActive: attributes => hasFaqClass(attributes.className),
        scope: ['inserter', 'transform']
    });
});