(function (blocks, blockEditor, components, element, i18n) {
    const el = element.createElement;
    const Fragment = element.Fragment;
    const useEffect = element.useEffect;
    const useState = element.useState;
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

    function resolveEditorImageUrl(attributes) {
        if (attributes.imageUrl) {
            return attributes.imageUrl;
        }

        if (!attributes.screenshotId || !window.gwiHighlightedScreenshot) {
            return '';
        }

        const config = window.gwiHighlightedScreenshot;
        const languages = [config.language, 'fi', 'en'].filter(function (value, index, list) {
            return value && list.indexOf(value) === index;
        });

        for (let index = 0; index < languages.length; index += 1) {
            const candidate = config.baseUrl + attributes.screenshotId + '-' + languages[index] + '.png';

            if (candidate) {
                return candidate;
            }
        }

        return '';
    }

    function hydrateLegacyAttributes(clientId, attributes, setAttributes) {
        if (attributes.screenshotId || attributes.imageUrl) {
            return;
        }

        const blockEditorStore = window.wp.data.select('core/block-editor');
        const block = blockEditorStore.getBlock(clientId);
        const raw = block && (block.originalContent || block.innerHTML || '');

        if (!raw || typeof raw !== 'string') {
            return;
        }

        try {
            const legacy = JSON.parse(raw.trim());

            if (!legacy || typeof legacy !== 'object') {
                return;
            }

            if (!legacy.screenshotId && !legacy.imageUrl) {
                return;
            }

            setAttributes(legacy);
        } catch (error) {
            return;
        }
    }

    blocks.registerBlockType('general-wp-instructions/highlighted-screenshot', {
        edit: function (props) {
            const attributes = props.attributes;
            const previewUrl = resolveEditorImageUrl(attributes);
            const hasImage = Boolean(previewUrl);
            const [imageFailed, setImageFailed] = useState(false);
            const showImage = hasImage && !imageFailed;

            useEffect(function () {
                hydrateLegacyAttributes(props.clientId, attributes, props.setAttributes);
            }, [props.clientId]);

            function selectImage(media) {
                setImageFailed(false);
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
                        { title: __('Screenshot', 'general-wp-instructions'), initialOpen: true },
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
                    showImage &&
                        el(
                            'figure',
                            { className: 'gwi-highlighted-screenshot' },
                            el(
                                'div',
                                { className: 'gwi-highlighted-screenshot__frame' },
                                el('img', {
                                    src: previewUrl,
                                    alt: attributes.alt || attributes.caption || '',
                                    onError: function () {
                                        setImageFailed(true);
                                    },
                                }),
                                el(
                                    'span',
                                    { className: 'gwi-highlighted-screenshot__box', style: percentStyle(attributes) },
                                    el('span', { className: 'gwi-highlighted-screenshot__label' }, attributes.label)
                                )
                            ),
                            attributes.caption && el('figcaption', {}, attributes.caption)
                        ),
                    !showImage &&
                        el(
                            'div',
                            { className: 'gwi-editor-screenshot-placeholder' },
                            attributes.screenshotId &&
                                el('p', {}, __('Admin screenshot loads from the library on the published page.', 'general-wp-instructions')),
                            !attributes.screenshotId &&
                                el('p', {}, __('Choose a screenshot, or set a screenshot ID in the block data.', 'general-wp-instructions'))
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
                                    variant: attributes.imageUrl ? 'secondary' : 'primary',
                                    onClick: mediaProps.open,
                                }, attributes.imageUrl
                                    ? __('Replace screenshot', 'general-wp-instructions')
                                    : __('Choose screenshot', 'general-wp-instructions'));
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
