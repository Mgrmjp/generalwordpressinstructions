(function () {
    'use strict';

    var config = window.manualApp || {};
    var labels = config.labels || {};

    function bySelector(selector, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(selector));
    }

    function escapeHtml(value) {
        var div = document.createElement('div');
        div.textContent = value == null ? '' : String(value);
        return div.innerHTML;
    }

    function debounce(fn, delay) {
        var timeout;
        return function () {
            var args = arguments;
            window.clearTimeout(timeout);
            timeout = window.setTimeout(function () {
                fn.apply(null, args);
            }, delay);
        };
    }

    function normalizeSearchText(value) {
        var text = String(value == null ? '' : value).toLowerCase();

        if (text.normalize) {
            text = text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        }

        return text;
    }

    function endpoint(params) {
        var url = new URL(config.restUrl, window.location.origin);
        Object.keys(params || {}).forEach(function (key) {
            var value = params[key];
            if (value !== undefined && value !== null && value !== '') {
                url.searchParams.set(key, value);
            }
        });
        return url.toString();
    }

    function fetchInstructions(params, signal) {
        if (!config.restUrl || !window.fetch) {
            return Promise.resolve([]);
        }

        return fetch(endpoint(params), {
            credentials: 'same-origin',
            signal: signal,
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Instruction request failed');
                }
                return response.json();
            })
            .then(function (payload) {
                return Array.isArray(payload.items) ? payload.items : [];
            });
    }

    function resultCountText(count) {
        return count + ' ' + (count === 1 ? (labels.result || 'guide') : (labels.results || 'guides'));
    }

    function renderGuideRow(item) {
        return [
            '<article class="manual-doc-row">',
            '<div class="manual-doc-row__main">',
            '<h3><a href="' + escapeHtml(item.url) + '">' + escapeHtml(item.title) + '</a></h3>',
            '<p>' + escapeHtml(item.purpose) + '</p>',
            '<div class="manual-doc-row__meta" aria-label="Guide details">',
            '<span>' + escapeHtml(item.difficulty) + '</span>',
            '<span>' + escapeHtml(item.minutes) + ' min</span>',
            '<span>' + escapeHtml(item.languageLabel) + '</span>',
            '</div>',
            '</div>',
            '<a class="manual-doc-row__link" href="' + escapeHtml(item.url) + '">' + escapeHtml(labels.start || 'Open guide') + (config.linkArrowHtml || '') + '</a>',
            '</article>',
        ].join('');
    }

    function renderLibrary(resultsNode, items) {
        var groups = config.groups || {};
        var grouped = {};

        items.forEach(function (item) {
            var key = item.groupKey || 'advanced';
            if (!grouped[key]) {
                grouped[key] = [];
            }
            grouped[key].push(item);
        });

        if (!items.length) {
            resultsNode.innerHTML = '<section class="manual-empty"><p>' + escapeHtml(labels.empty || 'No guides match these filters.') + '</p></section>';
            return;
        }

        var groupKeys = Object.keys(groups);
        Object.keys(grouped).forEach(function (key) {
            if (groupKeys.indexOf(key) === -1) {
                groupKeys.push(key);
            }
        });

        resultsNode.innerHTML = groupKeys.map(function (key) {
            var groupItems = grouped[key] || [];
            var group = groups[key] || { title: key, description: '' };

            if (!groupItems.length) {
                return '';
            }

            return [
                '<section class="manual-cabinet__group" aria-labelledby="manual-group-live-' + escapeHtml(key) + '">',
                '<header class="manual-cabinet__header">',
                '<div>',
                '<h2 id="manual-group-live-' + escapeHtml(key) + '">' + escapeHtml(group.title) + '</h2>',
                '<p>' + escapeHtml(group.description) + '</p>',
                '</div>',
                '<span>' + resultCountText(groupItems.length) + '</span>',
                '</header>',
                '<div class="manual-doc-list">',
                groupItems.map(renderGuideRow).join(''),
                '</div>',
                '</section>',
            ].join('');
        }).join('');
    }

    function initLibrary() {
        var root = document.querySelector('[data-manual-library]');
        if (!root) {
            return;
        }

        var resultsNode = root.querySelector('[data-manual-library-results]');
        var countNode = root.querySelector('[data-manual-results-count]');
        var searchInput = root.querySelector('[data-manual-library-search]');
        var clearButton = root.querySelector('[data-manual-library-clear]');
        var abortController = null;
        var state = {
            language: root.getAttribute('data-language') || '',
            category: root.getAttribute('data-category') || '',
            search: searchInput ? searchInput.value.trim() : '',
        };

        function categoryBaseUrl() {
            var selector = '[data-manual-category="' + state.category.replace(/"/g, '\\"') + '"]';
            var link = root.querySelector(selector);
            return link && link.getAttribute('data-manual-url') ? link.getAttribute('data-manual-url') : config.archiveUrl;
        }

        function updateActiveFilters() {
            bySelector('[data-manual-language]', root).forEach(function (link) {
                link.classList.toggle('is-active', (link.getAttribute('data-manual-language') || '') === state.language);
            });
            bySelector('[data-manual-category]', root).forEach(function (link) {
                link.classList.toggle('is-active', (link.getAttribute('data-manual-category') || '') === state.category);
            });
        }

        function updateUrl(replace) {
            if (!window.history || !config.archiveUrl) {
                return;
            }

            var url = new URL(categoryBaseUrl(), window.location.origin);
            if (state.language) {
                url.searchParams.set('instruction_language', state.language);
            } else {
                url.searchParams.set('instruction_language', 'all');
            }

            if (state.search) {
                url.searchParams.set('s', state.search);
                url.searchParams.set('post_type', 'wp_instruction');
            } else {
                url.searchParams.delete('s');
                url.searchParams.delete('post_type');
            }

            window.history[replace ? 'replaceState' : 'pushState'](state, '', url.toString());
        }

        function load(replaceUrl) {
            if (!resultsNode) {
                return;
            }

            if (abortController) {
                abortController.abort();
            }

            abortController = window.AbortController ? new AbortController() : null;
            root.classList.add('is-loading');
            updateActiveFilters();
            updateUrl(replaceUrl);

            fetchInstructions({
                language: state.language,
                category: state.category,
                search: state.search,
                limit: 100,
            }, abortController ? abortController.signal : undefined)
                .then(function (items) {
                    renderLibrary(resultsNode, items);
                    if (countNode) {
                        countNode.textContent = resultCountText(items.length);
                    }
                })
                .catch(function (error) {
                    if (!error || error.name !== 'AbortError') {
                        resultsNode.classList.add('has-load-error');
                    }
                })
                .finally(function () {
                    root.classList.remove('is-loading');
                });
        }

        root.addEventListener('click', function (event) {
            var languageLink = event.target.closest('[data-manual-language]');
            var categoryLink = event.target.closest('[data-manual-category]');

            if (languageLink && root.contains(languageLink)) {
                event.preventDefault();
                state.language = languageLink.getAttribute('data-manual-language') || '';
                load(false);
            }

            if (categoryLink && root.contains(categoryLink)) {
                event.preventDefault();
                state.category = categoryLink.getAttribute('data-manual-category') || '';
                load(false);
            }
        });

        if (searchInput) {
            searchInput.addEventListener('input', debounce(function () {
                state.search = searchInput.value.trim();
                load(true);
            }, 180));
        }

        if (clearButton) {
            clearButton.addEventListener('click', function () {
                if (searchInput) {
                    searchInput.value = '';
                }
                state.search = '';
                load(false);
            });
        }

        window.addEventListener('popstate', function (event) {
            if (!event.state) {
                return;
            }
            state = {
                language: event.state.language || '',
                category: event.state.category || '',
                search: event.state.search || '',
            };
            if (searchInput) {
                searchInput.value = state.search;
            }
            load(true);
        });
    }

    function initGlossary() {
        var root = document.querySelector('[data-manual-glossary]');
        if (!root) {
            return;
        }

        var searchInput = root.querySelector('[data-manual-glossary-search]');
        var clearButton = root.querySelector('[data-manual-glossary-clear]');
        var countNode = root.querySelector('[data-manual-glossary-count]');
        var emptyNode = root.querySelector('[data-manual-glossary-empty]');
        var items = bySelector('[data-manual-glossary-item]', root);
        var groups = bySelector('[data-manual-glossary-group]', root);
        var singular = root.getAttribute('data-count-singular') || 'term';
        var plural = root.getAttribute('data-count-plural') || 'terms';

        function searchableText(item) {
            return normalizeSearchText(item.getAttribute('data-glossary-text') || item.textContent || '');
        }

        function countText(count) {
            return count + ' ' + (count === 1 ? singular : plural);
        }

        function filter() {
            var query = searchInput ? normalizeSearchText(searchInput.value.trim()) : '';
            var visibleCount = 0;

            items.forEach(function (item) {
                var isVisible = query === '' || searchableText(item).indexOf(query) !== -1;
                item.hidden = !isVisible;
                if (isVisible) {
                    visibleCount += 1;
                }
            });

            groups.forEach(function (group) {
                var groupItems = bySelector('[data-manual-glossary-item]', group);
                group.hidden = !groupItems.some(function (item) {
                    return !item.hidden;
                });
            });

            if (countNode) {
                countNode.textContent = countText(visibleCount);
            }

            if (emptyNode) {
                emptyNode.hidden = visibleCount !== 0;
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', filter);
        }

        if (clearButton) {
            clearButton.addEventListener('click', function () {
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                }
                filter();
            });
        }
    }

    function initSearchSuggestions() {
        bySelector('form[role="search"]').forEach(function (form, index) {
            var postType = form.querySelector('input[name="post_type"][value="wp_instruction"]');
            var input = form.querySelector('input[type="search"]');
            if (!postType || !input || form.dataset.manualSuggestions === 'true') {
                return;
            }

            form.dataset.manualSuggestions = 'true';
            var panel = document.createElement('div');
            var panelId = 'manual-search-suggestions-' + index;
            var abortController = null;
            panel.className = 'manual-search-suggestions';
            panel.id = panelId;
            panel.setAttribute('role', 'listbox');
            panel.setAttribute('aria-label', labels.suggestions || 'Suggested guides');
            panel.hidden = true;
            input.setAttribute('aria-controls', panelId);
            input.setAttribute('aria-expanded', 'false');
            form.appendChild(panel);

            function hide() {
                panel.hidden = true;
                input.setAttribute('aria-expanded', 'false');
            }

            function show(items) {
                if (!items.length) {
                    hide();
                    return;
                }

                panel.innerHTML = [
                    '<p>' + escapeHtml(labels.suggestions || 'Suggested guides') + '</p>',
                    items.map(function (item) {
                        return '<a role="option" href="' + escapeHtml(item.url) + '"><strong>' + escapeHtml(item.title) + '</strong><span>' + escapeHtml(item.purpose) + '</span></a>';
                    }).join(''),
                ].join('');
                panel.hidden = false;
                input.setAttribute('aria-expanded', 'true');
            }

            input.addEventListener('input', debounce(function () {
                var query = input.value.trim();
                if (query.length < 2) {
                    hide();
                    return;
                }

                if (abortController) {
                    abortController.abort();
                }

                abortController = window.AbortController ? new AbortController() : null;
                fetchInstructions({
                    search: query,
                    language: config.currentLanguage || '',
                    limit: 6,
                }, abortController ? abortController.signal : undefined)
                    .then(show)
                    .catch(function (error) {
                        if (!error || error.name !== 'AbortError') {
                            hide();
                        }
                    });
            }, 160));

            input.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    hide();
                }
            });

            document.addEventListener('click', function (event) {
                if (!form.contains(event.target)) {
                    hide();
                }
            });
        });
    }

    function fallbackCopy(text) {
        return new Promise(function (resolve, reject) {
            var textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.setAttribute('readonly', 'readonly');
            textarea.style.position = 'fixed';
            textarea.style.top = '-9999px';
            document.body.appendChild(textarea);
            textarea.select();

            try {
                if (document.execCommand('copy')) {
                    resolve();
                } else {
                    reject(new Error('Copy command failed'));
                }
            } catch (error) {
                reject(error);
            } finally {
                document.body.removeChild(textarea);
            }
        });
    }

    function initCopyLink() {
        bySelector('[data-copy-link]').forEach(function (button) {
            button.addEventListener('click', function () {
                var copiedLabel = button.getAttribute('data-copied-label') || 'Copied';
                var copyLabel = button.getAttribute('data-copy-label') || button.textContent;
                var write = navigator.clipboard
                    ? navigator.clipboard.writeText(window.location.href).catch(function () {
                        return fallbackCopy(window.location.href);
                    })
                    : fallbackCopy(window.location.href);

                write.then(function () {
                    button.textContent = copiedLabel;
                    window.setTimeout(function () {
                        button.textContent = copyLabel;
                    }, 1800);
                }).catch(function () {});
            });
        });
    }

    function initOnPageState() {
        var sections = bySelector('.manual-document-section[id]');
        var links = bySelector(
            '.manual-on-page a[href^="#"], .manual-progress a[href^="#"], .manual-doc-contents a[href^="#"]'
        );
        var here = document.querySelector('[data-manual-you-are-here]');
        var hereLabel = document.querySelector('[data-manual-you-are-here-label]');

        if (!sections.length || !links.length) {
            return;
        }

        function sectionLabel(section) {
            var heading = section.querySelector('h2');
            return heading ? heading.textContent.trim() : section.id;
        }

        function setActive(id) {
            links.forEach(function (link) {
                link.classList.toggle('is-active', link.getAttribute('href') === '#' + id);
            });

            if (here && hereLabel) {
                var activeSection = document.getElementById(id);
                if (activeSection) {
                    hereLabel.textContent = sectionLabel(activeSection);
                    here.hidden = false;
                } else {
                    here.hidden = true;
                }
            }
        }

        function resolveActiveSectionId() {
            var scrollY = window.scrollY || window.pageYOffset;
            var viewportHeight = window.innerHeight;
            var documentHeight = Math.max(
                document.documentElement.scrollHeight,
                document.body ? document.body.scrollHeight : 0
            );
            var bottomSlack = 64;
            var headingLine = Math.round(viewportHeight * 0.22);
            var lastSection = sections[sections.length - 1];

            if (lastSection && scrollY + viewportHeight >= documentHeight - bottomSlack) {
                return lastSection.id;
            }

            var activeId = sections[0].id;

            sections.forEach(function (section) {
                if (section.getBoundingClientRect().top <= headingLine) {
                    activeId = section.id;
                }
            });

            return activeId;
        }

        var scrollTicking = false;

        function updateActiveSection() {
            setActive(resolveActiveSectionId());
        }

        function onScrollOrResize() {
            if (scrollTicking) {
                return;
            }

            scrollTicking = true;
            window.requestAnimationFrame(function () {
                updateActiveSection();
                scrollTicking = false;
            });
        }

        window.addEventListener('scroll', onScrollOrResize, { passive: true });
        window.addEventListener('resize', onScrollOrResize, { passive: true });
        updateActiveSection();
    }

    function initCodeCopy() {
        var blocks = bySelector('.manual-content pre');

        blocks.forEach(function (pre) {
            if (pre.closest('.manual-code-block')) {
                return;
            }

            var wrapper = document.createElement('div');
            wrapper.className = 'manual-code-block';
            pre.parentNode.insertBefore(wrapper, pre);
            wrapper.appendChild(pre);

            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'manual-code-copy';
            button.textContent = labels.copyCode || 'Copy';
            wrapper.appendChild(button);

            button.addEventListener('click', function () {
                var code = pre.textContent || '';
                var write = navigator.clipboard
                    ? navigator.clipboard.writeText(code).catch(function () {
                        return fallbackCopy(code);
                    })
                    : fallbackCopy(code);

                write.then(function () {
                    button.textContent = labels.codeCopied || 'Copied';
                    window.setTimeout(function () {
                        button.textContent = labels.copyCode || 'Copy';
                    }, 1600);
                }).catch(function () {});
            });
        });
    }

    function initDarkMode() {
        if (!window.localStorage) {
            return;
        }

        var storageKey = 'manual-site-theme';
        var legacyKey = 'manual-reader-theme';
        var toggles = document.querySelectorAll('[data-manual-theme-toggle]');

        function applyTheme(isDark) {
            document.documentElement.classList.toggle('manual-theme-dark', isDark);

            toggles.forEach(function (toggle) {
                var darkLabel = toggle.getAttribute('data-label-dark') || labels.darkModeOn || 'Dark mode';
                var lightLabel = toggle.getAttribute('data-label-light') || labels.darkModeOff || 'Light mode';
                toggle.textContent = isDark ? darkLabel : lightLabel;
                toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
            });
        }

        var stored = window.localStorage.getItem(storageKey);

        if (stored === null) {
            stored = window.localStorage.getItem(legacyKey);
        }

        applyTheme(stored === 'dark');

        toggles.forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                var isDark = !document.documentElement.classList.contains('manual-theme-dark');
                applyTheme(isDark);
                window.localStorage.setItem(storageKey, isDark ? 'dark' : 'light');
            });
        });
    }

    function initStepCompletion() {
        var steps = bySelector('.gwi-step-list__item');
        if (!steps.length || !window.localStorage) {
            return;
        }

        var key = 'manual-completed-steps:' + window.location.pathname;
        var completed = {};

        try {
            completed = JSON.parse(window.localStorage.getItem(key) || '{}') || {};
        } catch (error) {
            completed = {};
        }

        function persist() {
            window.localStorage.setItem(key, JSON.stringify(completed));
        }

        steps.forEach(function (step, index) {
            var button = document.createElement('button');
            var stepKey = String(index);
            button.type = 'button';
            button.className = 'manual-step-toggle';
            button.textContent = completed[stepKey] ? (labels.undo || 'Undo') : (labels.done || 'Done');
            button.setAttribute('aria-pressed', completed[stepKey] ? 'true' : 'false');
            step.classList.toggle('is-complete', Boolean(completed[stepKey]));
            step.appendChild(button);

            button.addEventListener('click', function () {
                completed[stepKey] = !completed[stepKey];
                if (!completed[stepKey]) {
                    delete completed[stepKey];
                }
                step.classList.toggle('is-complete', Boolean(completed[stepKey]));
                button.textContent = completed[stepKey] ? (labels.undo || 'Undo') : (labels.done || 'Done');
                button.setAttribute('aria-pressed', completed[stepKey] ? 'true' : 'false');
                persist();
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initLibrary();
        initGlossary();
        initSearchSuggestions();
        initCopyLink();
        initOnPageState();
        initStepCompletion();
        initCodeCopy();
        initDarkMode();
    });
})();
