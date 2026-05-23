(function (blocks, blockEditor, components, element, i18n) {
    const el = element.createElement;
    const Fragment = element.Fragment;
    const RichText = blockEditor.RichText;
    const Button = components.Button;
    const __ = i18n.__;

    function normalizeSteps(steps) {
        return Array.isArray(steps) && steps.length ? steps : [{ text: '' }];
    }

    blocks.registerBlockType('general-wp-instructions/step-list', {
        edit: function (props) {
            const attributes = props.attributes;
            const steps = normalizeSteps(attributes.steps);

            function setSteps(nextSteps) {
                props.setAttributes({ steps: nextSteps });
            }

            function updateStep(index, value) {
                setSteps(steps.map(function (step, stepIndex) {
                    return stepIndex === index ? { text: value } : step;
                }));
            }

            function addStep() {
                setSteps(steps.concat([{ text: '' }]));
            }

            function removeStep(index) {
                setSteps(steps.filter(function (_step, stepIndex) {
                    return stepIndex !== index;
                }));
            }

            return el(
                Fragment,
                {},
                el(
                    'div',
                    { className: 'gwi-editor-card gwi-editor-card--steps' },
                    el(RichText, {
                        tagName: 'h3',
                        value: attributes.title,
                        allowedFormats: [],
                        placeholder: __('Instruction title', 'general-wp-instructions'),
                        onChange: function (value) {
                            props.setAttributes({ title: value });
                        },
                    }),
                    el(
                        'ol',
                        { className: 'gwi-editor-steps' },
                        steps.map(function (step, index) {
                            return el(
                                'li',
                                { className: 'gwi-editor-step', key: index },
                                el(
                                    'div',
                                    { className: 'gwi-editor-step__row' },
                                    el(RichText, {
                                        tagName: 'span',
                                        value: step.text,
                                        placeholder: __('Describe this step', 'general-wp-instructions'),
                                        onChange: function (value) {
                                            updateStep(index, value);
                                        },
                                    }),
                                    steps.length > 1 && el(Button, {
                                        isDestructive: true,
                                        variant: 'link',
                                        onClick: function () {
                                            removeStep(index);
                                        },
                                    }, __('Remove', 'general-wp-instructions'))
                                )
                            );
                        })
                    ),
                    el(Button, {
                        variant: 'secondary',
                        onClick: addStep,
                    }, __('Add step', 'general-wp-instructions'))
                )
            );
        },
        save: function () {
            return null;
        },
    });
})(window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n);
