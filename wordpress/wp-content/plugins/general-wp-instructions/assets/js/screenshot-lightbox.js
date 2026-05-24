(function () {
    'use strict';

    var config = window.gwiScreenshotLightbox || {};
    var labels = config.labels || {};

    var lightbox = null;
    var lastTrigger = null;

    function bySelector(selector, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(selector));
    }

    function createLightbox() {
        var root = document.createElement('div');
        root.className = 'gwi-screenshot-lightbox';
        root.hidden = true;
        root.setAttribute('data-gwi-screenshot-lightbox', '');

        root.innerHTML = [
            '<div class="gwi-screenshot-lightbox__backdrop" data-gwi-lightbox-close></div>',
            '<div class="gwi-screenshot-lightbox__panel" role="dialog" aria-modal="true" aria-labelledby="gwi-screenshot-lightbox-title">',
            '<div class="gwi-screenshot-lightbox__header">',
            '<p class="gwi-screenshot-lightbox__title" id="gwi-screenshot-lightbox-title"></p>',
            '<div class="gwi-screenshot-lightbox__views" data-gwi-lightbox-views hidden></div>',
            '<button type="button" class="gwi-screenshot-lightbox__close" data-gwi-lightbox-close>',
            labels.close || 'Close',
            '</button>',
            '</div>',
            '<div class="gwi-screenshot-lightbox__stage">',
            '<img class="gwi-screenshot-lightbox__image" data-gwi-lightbox-image alt="">',
            '</div>',
            '</div>',
        ].join('');

        document.body.appendChild(root);

        bySelector('[data-gwi-lightbox-close]', root).forEach(function (control) {
            control.addEventListener('click', closeLightbox);
        });

        root.addEventListener('click', function (event) {
            if (event.target === root.querySelector('.gwi-screenshot-lightbox__backdrop')) {
                closeLightbox();
            }
        });

        var views = root.querySelector('[data-gwi-lightbox-views]');

        if (views) {
            views.addEventListener('click', function (event) {
                var button = event.target.closest('[data-gwi-lightbox-view]');

                if (!button || !lightbox) {
                    return;
                }

                setActiveView(button.getAttribute('data-gwi-lightbox-view'));
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && lightbox && !lightbox.hidden) {
                closeLightbox();
            }
        });

        return root;
    }

    function setActiveView(viewName) {
        if (!lightbox) {
            return;
        }

        var detailUrl = lightbox.getAttribute('data-detail-url') || '';
        var contextUrl = lightbox.getAttribute('data-context-url') || '';
        var image = lightbox.querySelector('[data-gwi-lightbox-image]');
        var nextUrl = viewName === 'context' && contextUrl !== '' ? contextUrl : detailUrl;

        if (!image || nextUrl === '') {
            return;
        }

        image.setAttribute('src', nextUrl);
        lightbox.setAttribute('data-active-view', viewName);

        bySelector('[data-gwi-lightbox-view]', lightbox).forEach(function (button) {
            var isActive = button.getAttribute('data-gwi-lightbox-view') === viewName;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    }

    function openLightbox(trigger) {
        if (!lightbox) {
            lightbox = createLightbox();
        }

        var detailUrl = trigger.getAttribute('data-detail-url') || '';
        var contextUrl = trigger.getAttribute('data-context-url') || '';
        var caption = trigger.getAttribute('data-caption') || '';
        var defaultView = trigger.getAttribute('data-default-view') || 'detail';
        var image = lightbox.querySelector('[data-gwi-lightbox-image]');
        var title = lightbox.querySelector('.gwi-screenshot-lightbox__title');
        var views = lightbox.querySelector('[data-gwi-lightbox-views]');
        var hasContext = contextUrl !== '' && contextUrl !== detailUrl;

        if (!image || detailUrl === '') {
            return;
        }

        lastTrigger = trigger;
        lightbox.setAttribute('data-detail-url', detailUrl);
        lightbox.setAttribute('data-context-url', contextUrl);

        if (title) {
            title.textContent = caption;
        }

        if (views) {
            if (hasContext) {
                views.hidden = false;
                views.innerHTML = [
                    '<button type="button" class="gwi-screenshot-lightbox__view is-active" data-gwi-lightbox-view="context" aria-pressed="true">',
                    labels.context || 'Full admin view',
                    '</button>',
                    '<button type="button" class="gwi-screenshot-lightbox__view" data-gwi-lightbox-view="detail" aria-pressed="false">',
                    labels.detail || 'Zoom target',
                    '</button>',
                ].join('');
            } else {
                views.hidden = true;
                views.innerHTML = '';
            }
        }

        var initialView = hasContext && defaultView === 'context' ? 'context' : 'detail';

        if (hasContext && defaultView === 'context') {
            initialView = 'context';
        } else if (!hasContext) {
            initialView = 'detail';
        }

        setActiveView(initialView);
        lightbox.hidden = false;
        document.documentElement.classList.add('gwi-screenshot-lightbox-open');

        var closeButton = lightbox.querySelector('.gwi-screenshot-lightbox__close');

        if (closeButton) {
            closeButton.focus();
        }
    }

    function closeLightbox() {
        if (!lightbox) {
            return;
        }

        lightbox.hidden = true;
        document.documentElement.classList.remove('gwi-screenshot-lightbox-open');

        var image = lightbox.querySelector('[data-gwi-lightbox-image]');

        if (image) {
            image.removeAttribute('src');
        }

        if (lastTrigger) {
            lastTrigger.focus();
            lastTrigger = null;
        }
    }

    function bindTriggers() {
        bySelector('[data-gwi-screenshot-expand]').forEach(function (trigger) {
            if (trigger.getAttribute('data-gwi-bound') === '1') {
                return;
            }

            trigger.setAttribute('data-gwi-bound', '1');
            trigger.addEventListener('click', function () {
                openLightbox(trigger);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindTriggers);
    } else {
        bindTriggers();
    }
})();
