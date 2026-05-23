(function (blocks, blockEditor, components, element, i18n) {
    const el = element.createElement;
    const Fragment = element.Fragment;
    const MediaUpload = blockEditor.MediaUpload;
    const MediaUploadCheck = blockEditor.MediaUploadCheck;
    const InspectorControls = blockEditor.InspectorControls;
    const Button = components.Button;
    const PanelBody = components.PanelBody;
    const RangeControl = components.RangeControl;
    const TextControl = components.TextControl;
    const TextareaControl = components.TextareaControl;
    const __ = i18n.__;

    function percentStyle(attributes) {
        return {
            left: attributes.highlightX + '%',
            top: attributes.highlightY + '%',
            width: attributes.highlightWidth + '%',
            height: attributes.highlightHeight + '%',
        };
    }

    blocks.registerBlockType('general-wp-instructions/highlighted-screenshot', {
        edit: function (props) {
            const attributes = props.attributes;
            const hasImage = Boolean(attributes.imageUrl);

            function selectImage(media) {
                props.setAttributes({
                    imageId: media.id || 0,
                    imageUrl: media.url || '',
                    alt: media.alt || '',
                });
            }

            return el(
                Fragment,
                {},
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        { title: __('Highlight settings', 'general-wp-instructions'), initialOpen: true },
                        el(TextControl, {
                            label: __('Highlight label', 'general-wp-instructions'),
                            value: attributes.label,
                            onChange: function (value) {
                                props.setAttributes({ label: value });
                            },
                        }),
                        el(TextareaControl, {
                            label: __('Caption', 'general-wp-instructions'),
                            value: attributes.caption,
                            onChange: function (value) {
                                props.setAttributes({ caption: value });
                            },
                        }),
                        el(RangeControl, {
                            label: __('Horizontal position', 'general-wp-instructions'),
                            min: 0,
                            max: 100,
                            value: attributes.highlightX,
                            onChange: function (value) {
                                props.setAttributes({ highlightX: value });
                            },
                        }),
                        el(RangeControl, {
                            label: __('Vertical position', 'general-wp-instructions'),
                            min: 0,
                            max: 100,
                            value: attributes.highlightY,
                            onChange: function (value) {
                                props.setAttributes({ highlightY: value });
                            },
                        }),
                        el(RangeControl, {
                            label: __('Highlight width', 'general-wp-instructions'),
                            min: 5,
                            max: 100,
                            value: attributes.highlightWidth,
                            onChange: function (value) {
                                props.setAttributes({ highlightWidth: value });
                            },
                        }),
                        el(RangeControl, {
                            label: __('Highlight height', 'general-wp-instructions'),
                            min: 5,
                            max: 100,
                            value: attributes.highlightHeight,
                            onChange: function (value) {
                                props.setAttributes({ highlightHeight: value });
                            },
                        })
                    )
                ),
                el(
                    'div',
                    { className: 'gwi-editor-card' },
                    hasImage && el(
                        'figure',
                        { className: 'gwi-highlighted-screenshot' },
                        el(
                            'div',
                            { className: 'gwi-highlighted-screenshot__frame' },
                            el('img', { src: attributes.imageUrl, alt: attributes.alt }),
                            el(
                                'span',
                                { className: 'gwi-highlighted-screenshot__box', style: percentStyle(attributes) },
                                el('span', { className: 'gwi-highlighted-screenshot__label' }, attributes.label)
                            )
                        ),
                        attributes.caption && el('figcaption', {}, attributes.caption)
                    ),
                    !hasImage && el(
                        'div',
                        { className: 'gwi-editor-screenshot-placeholder' },
                        el('p', {}, __('Choose a screenshot, then adjust the highlight in the sidebar.', 'general-wp-instructions'))
                    ),
                    el(
                        MediaUploadCheck,
                        {},
                        el(MediaUpload, {
                            onSelect: selectImage,
                            allowedTypes: ['image'],
                            value: attributes.imageId,
                            render: function (mediaProps) {
                                return el(Button, {
                                    variant: hasImage ? 'secondary' : 'primary',
                                    onClick: mediaProps.open,
                                }, hasImage ? __('Replace screenshot', 'general-wp-instructions') : __('Choose screenshot', 'general-wp-instructions'));
                            },
                        })
                    )
                )
            );
        },
        save: function () {
            return null;
        },
    });
})(window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n);
