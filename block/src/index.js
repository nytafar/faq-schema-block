const { registerBlockType } = wp.blocks;
const { RichText } = wp.blockEditor;
const { __ } = wp.i18n;

registerBlockType('faq-schema-block/faq', {
    title: __('FAQ Schema', 'faq-schema-block'),
    icon: 'editor-help',
    category: 'common',
    attributes: {
        question: {
            type: 'string',
            default: ''
        },
        answer: {
            type: 'string',
            default: 'Your answer here...'
        },
        id: {
            type: 'string',
            default: ''
        }
    },

    edit: ({ attributes, setAttributes }) => (
        <>
            <RichText
                tagName="h3"
                value={attributes.question || ''}
                onChange={question => setAttributes({ question })}
                placeholder="Enter question..."
            />
            <RichText
                tagName="p"
                value={attributes.answer || ''}
                onChange={answer => setAttributes({ answer })}
                placeholder="Enter answer..."
            />
        </>
    ),

    save: ({ attributes }) => (
        <div className="wp-block-faq-schema-block-faq">
            <RichText.Content tagName="h3" value={attributes.question || ''} />
            <RichText.Content tagName="p" value={attributes.answer || ''} />
        </div>
    )
});