#!/usr/bin/env node

import { chromium } from '@playwright/test';
import { execFileSync, execSync } from 'node:child_process';
import { existsSync, mkdirSync, readFileSync, statSync } from 'node:fs';
import { resolve } from 'node:path';
import process from 'node:process';
import { createScreenshotLogger } from './lib/screenshot-log.mjs';

const DEFAULT_CONFIG = 'config/critical-views.json';
const DEFAULT_OUTPUT = 'wordpress/wp-content/uploads/instruction-screenshots';
const WP_CLI_PATH = process.env.WP_CLI_PATH || '/app/wordpress';
const WORDPRESS_ADMIN_LOCALES = {
    en: 'en_US',
    fi: 'fi',
};

function loadEnvFile() {
    const envPath = resolve('.env');

    if (!existsSync(envPath)) {
        return;
    }

    const lines = readFileSync(envPath, 'utf8').split(/\r?\n/);

    for (const line of lines) {
        const trimmed = line.trim();

        if (!trimmed || trimmed.startsWith('#') || !trimmed.includes('=')) {
            continue;
        }

        const [key, ...valueParts] = trimmed.split('=');
        const value = valueParts.join('=').replace(/^['"]|['"]$/g, '');

        if (!process.env[key]) {
            process.env[key] = value;
        }
    }
}

function parseArgs(argv) {
    const args = {
        config: DEFAULT_CONFIG,
        output: process.env.WP_SCREENSHOT_OUTPUT || DEFAULT_OUTPUT,
        language: process.env.WP_SCREENSHOT_LANGUAGE || 'en',
        only: process.env.GWI_CAPTURE_ONLY || '',
        headed: false,
        help: false,
    };

    for (let index = 0; index < argv.length; index += 1) {
        const arg = argv[index];

        if (arg === '--help' || arg === '-h') {
            args.help = true;
        } else if (arg === '--only') {
            args.only = argv[index + 1] || '';
            index += 1;
        } else if (arg === '--headed') {
            args.headed = true;
        } else if (arg === '--config') {
            args.config = argv[index + 1] || args.config;
            index += 1;
        } else if (arg === '--output') {
            args.output = argv[index + 1] || args.output;
            index += 1;
        } else if (arg === '--language') {
            args.language = argv[index + 1] || args.language;
            index += 1;
        }
    }

    return args;
}

function printHelp() {
    console.log(`Capture highlighted WordPress admin screenshots.

Required environment:
  WP_BASE_URL   Example: https://generalwordpressinstructions.lndo.site
  WP_USER       WordPress username (default: maria.korhonen)
  WP_PASSWORD   WordPress password (default: admin)

Note:
  The bootstrap "admin" account is demoted to subscriber for realistic Users screenshots.
  Capture must use an administrator such as maria.korhonen (lando wp gwi ensure-screenshot-users).

Options:
  --config <path>      JSON config path. Default: ${DEFAULT_CONFIG}
  --output <path>      Screenshot output directory. Default: ${DEFAULT_OUTPUT}
  --language <en|fi>   Label language. Default: en
  --headed             Run Chromium visibly for debugging
  --help               Show this help

Config capture modes:
  viewport             Capture the browser viewport.
  fullPage             Capture the entire page.
  element              Capture captureSelector only.
  focus                Crop around highlighted elements with readable context.

Highlight options (per entry in highlights[]):
  showLabel            Render tooltip on PNG (default: off; use caption in docs instead).
  padding              Outline inset in px (default: 6).
`);
}

function requiredEnv(name) {
    const value = process.env[name];

    if (!value) {
        throw new Error(`Missing required environment variable: ${name}`);
    }

    return value;
}

function readConfig(path) {
    const configPath = resolve(path);
    const config = JSON.parse(readFileSync(configPath, 'utf8'));

    if (!Array.isArray(config.views)) {
        throw new Error(`Config must contain a "views" array: ${configPath}`);
    }

    return config.views;
}

function localized(value, language) {
    if (typeof value === 'string') {
        return value;
    }

    if (value && typeof value === 'object') {
        return value[language] || value.en || Object.values(value)[0] || '';
    }

    return '';
}

function adminUrl(baseUrl, path) {
    const normalizedBase = baseUrl.replace(/\/+$/, '');
    const normalizedPath = path.startsWith('/') ? path : `/${path}`;

    return `${normalizedBase}${normalizedPath}`;
}

function wpCli(args, options = {}) {
    return execFileSync(
        'lando',
        ['wp', ...args, `--path=${WP_CLI_PATH}`],
        {
            cwd: process.cwd(),
            encoding: 'utf8',
            stdio: options.stdio || 'pipe',
        },
    );
}

function wordpressAdminLocale(language) {
    return WORDPRESS_ADMIN_LOCALES[language] || WORDPRESS_ADMIN_LOCALES.en;
}

function ensureWordPressAdminLocale(locale, logger) {
    if (locale === 'en_US') {
        return;
    }

    try {
        wpCli(['language', 'core', 'is-installed', locale]);
    } catch {
        logger.log('info', 'Installing WordPress admin language', { locale });
        wpCli(['language', 'core', 'install', locale]);
    }
}

function getUserLocale(username) {
    try {
        return wpCli(['user', 'meta', 'get', username, 'locale']).trim();
    } catch {
        return '';
    }
}

function setUserLocale(username, locale) {
    if (locale === '') {
        try {
            wpCli(['user', 'meta', 'delete', username, 'locale']);
        } catch {
            // No locale meta existed before this capture run.
        }
        return;
    }

    wpCli(['user', 'meta', 'update', username, 'locale', locale]);
}

function configureCaptureUserLocale(language, username, logger) {
    const locale = wordpressAdminLocale(language);
    const previousLocale = getUserLocale(username);

    ensureWordPressAdminLocale(locale, logger);
    setUserLocale(username, locale);

    logger.log('info', 'Configured WordPress admin locale', {
        user: username,
        language,
        locale,
        previousLocale: previousLocale || '',
    });

    return () => {
        setUserLocale(username, previousLocale);
        logger.log('info', 'Restored WordPress admin locale', {
            user: username,
            locale: previousLocale || '',
        });
    };
}

function resolvePostEditUrl(view, language, postType, slugKey) {
    const slugSource = view[slugKey];
    const slug = typeof slugSource === 'string'
        ? slugSource
        : localized(slugSource, language);

    if (!slug) {
        return view.url;
    }

    try {
        const escapedSlug = slug.replace(/'/g, `'\\''`);
        const postId = execSync(
            postType === 'page'
                ? `lando wp eval 'echo (int) (get_page_by_path("${escapedSlug}", OBJECT, "page")->ID ?? 0);' --path=wordpress`
                : `lando wp post list --post_type=${postType} --name=${slug} --field=ID --path=wordpress`,
            { encoding: 'utf8', cwd: process.cwd() },
        ).trim();

        if (postId === '') {
            return view.url;
        }

        const classic = slugKey === 'pageSlug' ? '&classic-editor' : '';

        return `/wp-admin/post.php?post=${postId}&action=edit${classic}`;
    } catch {
        return view.url;
    }
}

function resolveViewUrl(view, language) {
    if (view.pageSlug) {
        return resolvePostEditUrl(view, language, 'page', 'pageSlug');
    }

    if (view.instructionSlug) {
        return resolvePostEditUrl(view, language, 'wp_instruction', 'instructionSlug');
    }

    return view.url;
}

function resolveScreenshotCredentials() {
    const username = (process.env.WP_USER || 'maria.korhonen').trim();
    const password = (process.env.WP_PASSWORD || 'admin').trim();

    return { username, password };
}

async function login(page, baseUrl, username, password) {
    await page.goto(adminUrl(baseUrl, '/wp-login.php'), { waitUntil: 'domcontentloaded' });
    await page.fill('#user_login', username);
    await page.fill('#user_pass', password);
    await Promise.all([
        page.waitForURL((url) => !url.toString().includes('wp-login.php'), { timeout: 20000 }).catch(() => null),
        page.click('#wp-submit'),
    ]);

    if (page.url().includes('wp-login.php')) {
        throw new Error('WordPress login failed. Check WP_USER and WP_PASSWORD.');
    }
}

async function verifyAdminSession(page, baseUrl, username) {
    await page.goto(adminUrl(baseUrl, '/wp-admin/edit.php'), { waitUntil: 'domcontentloaded' });

    if (page.url().includes('wp-login.php')) {
        throw new Error('WordPress session is not authenticated after login.');
    }

    const wpbody = await findFirstVisibleLocator(page, '#wpbody, #wpcontent', 15000);

    if (wpbody) {
        return;
    }

    const denied = await page
        .locator('body')
        .getByText(/sorry, you are not allowed|you do not have permission/i)
        .first()
        .isVisible()
        .catch(() => false);

    if (denied || username === 'admin') {
        throw new Error(
            `WP_USER "${username}" cannot access wp-admin list screens. `
            + 'Use WP_USER=maria.korhonen and run: lando wp gwi ensure-screenshot-users --path=wordpress',
        );
    }

    throw new Error('wp-admin did not finish loading (#wpbody missing). Check WP_USER and WP_PASSWORD.');
}

function isBlockEditorUrl(url) {
    return url.includes('post-new.php') || (url.includes('post.php') && !url.includes('classic-editor'));
}

async function suppressBlockEditorWelcomeInStorage(page) {
    await page.evaluate(() => {
        try {
            for (let index = localStorage.length - 1; index >= 0; index -= 1) {
                const key = localStorage.key(index);

                if (!key || !key.startsWith('WP_PREFERENCES_USER_')) {
                    continue;
                }

                const preferences = JSON.parse(localStorage.getItem(key) || '{}');

                preferences.core = {
                    ...(preferences.core || {}),
                    welcomeGuide: false,
                };
                preferences['core/edit-post'] = {
                    ...(preferences['core/edit-post'] || {}),
                    welcomeGuide: false,
                };

                localStorage.setItem(key, JSON.stringify(preferences));
            }
        } catch {
            // Ignore storage write failures in headless capture.
        }
    });
}

async function dismissBlockEditorWelcome(page) {
    await suppressBlockEditorWelcomeInStorage(page);

    await page.evaluate(() => {
        try {
            if (window.wp?.data?.dispatch) {
                window.wp.data.dispatch('core/preferences').set('core', 'welcomeGuide', false);
                window.wp.data.dispatch('core/preferences').set('core/edit-post', 'welcomeGuide', false);
            }
        } catch {
            // Editor scripts may not be ready yet.
        }
    }).catch(() => null);

    const closeSelectors = [
        '.edit-post-welcome-guide button[aria-label="Close"]',
        '.edit-post-welcome-guide button[aria-label="Close dialog"]',
        '.components-guide button[aria-label="Close"]',
        '.components-guide button[aria-label="Close dialog"]',
        '[role="dialog"] button[aria-label="Close"]',
        '[role="dialog"] button[aria-label="Close dialog"]',
        '.components-modal__header button[aria-label="Close"]',
        '.components-modal__header button[aria-label="Close dialog"]',
    ].join(', ');

    const closeButton = page.locator(closeSelectors).first();

    if (await closeButton.isVisible({ timeout: 1200 }).catch(() => false)) {
        await closeButton.click();
        await page.waitForTimeout(250);
        return;
    }

    const welcomeDialog = page.locator('[role="dialog"]').filter({ hasText: /welcome to the editor/i });

    if (await welcomeDialog.isVisible({ timeout: 800 }).catch(() => false)) {
        await page.keyboard.press('Escape').catch(() => null);
        await page.waitForTimeout(250);
    }
}

async function prepareAdminPage(page, url) {
    if (!isBlockEditorUrl(url)) {
        return;
    }

    await dismissBlockEditorWelcome(page);
}

async function injectHighlightStyles(page) {
    await page.addStyleTag({
        content: `
            .__gwi_capture_overlay {
                background: transparent !important;
                border: 3px solid #facc15 !important;
                border-radius: 6px !important;
                box-shadow:
                    0 0 0 2px #111827,
                    0 0 0 9999px rgba(17, 24, 39, 0.52) !important;
                box-sizing: border-box !important;
                pointer-events: none !important;
                z-index: 2147483647 !important;
            }

            .__gwi_capture_overlay--document {
                position: absolute !important;
            }

            .__gwi_capture_overlay--viewport {
                position: fixed !important;
            }

            .__gwi_capture_label {
                background: #111827 !important;
                border: 2px solid #facc15 !important;
                border-radius: 6px !important;
                color: #fff !important;
                font: 700 14px/1.2 system-ui, sans-serif !important;
                left: 0 !important;
                padding: 6px 10px !important;
                position: absolute !important;
                top: calc(100% + 8px) !important;
                white-space: nowrap !important;
            }

            .__gwi_capture_label--above {
                bottom: calc(100% + 8px) !important;
                top: auto !important;
            }
        `,
    });
}

async function findFirstVisibleLocator(page, selector, timeout = 2000) {
    const selectors = selector.split(',').map((item) => item.trim()).filter(Boolean);

    for (const currentSelector of selectors) {
        const locator = page.locator(currentSelector).first();

        if ((await locator.count()) === 0) {
            continue;
        }

        try {
            await locator.waitFor({ state: 'visible', timeout });
            return locator;
        } catch {
            continue;
        }
    }

    return null;
}

/**
 * Percent positions for CSS overlay on element-cropped screenshots (container-relative).
 */
async function measureHighlightPercents(page, containerSelector, highlightSelector, padding = 6) {
    return page.evaluate(({ containerSelector: containerSelectors, highlightSelector: highlightSelectors, padding }) => {
        const pickFirst = (root, selectors) => {
            for (const selector of selectors) {
                const node = root.querySelector(selector) || document.querySelector(selector);

                if (node) {
                    return node;
                }
            }

            return null;
        };

        const container = pickFirst(document, containerSelectors.split(',').map((value) => value.trim()));

        if (!container) {
            return null;
        }

        const target = pickFirst(container, highlightSelectors.split(',').map((value) => value.trim()));

        if (!target) {
            return null;
        }

        const containerRect = container.getBoundingClientRect();
        const targetRect = target.getBoundingClientRect();
        const left = targetRect.left - containerRect.left - padding;
        const top = targetRect.top - containerRect.top - padding;
        const width = targetRect.width + padding * 2;
        const height = targetRect.height + padding * 2;

        if (containerRect.width <= 0 || containerRect.height <= 0) {
            return null;
        }

        return {
            highlightX: Number(((left / containerRect.width) * 100).toFixed(2)),
            highlightY: Number(((top / containerRect.height) * 100).toFixed(2)),
            highlightWidth: Number(((width / containerRect.width) * 100).toFixed(2)),
            highlightHeight: Number(((height / containerRect.height) * 100).toFixed(2)),
        };
    }, {
        containerSelector,
        highlightSelector,
        padding,
    });
}

async function applyHighlight(page, highlight, language, captureMode, logger) {
    const locator = await findFirstVisibleLocator(page, highlight.selector);

    if (!locator) {
        if (highlight.optional) {
            logger.log('warn', 'Optional selector not found', {
                selector: highlight.selector,
            });
            logger.bumpCount('warnings');
            return null;
        }

        throw new Error(`Required selector not found: ${highlight.selector}`);
    }

    const label = localized(highlight.label, language);
    const showLabel = highlight.showLabel === true;
    const padding = Number.isFinite(highlight.padding) ? highlight.padding : 6;

    await locator.scrollIntoViewIfNeeded();

    return await locator.evaluate((element, payload) => {
        const rect = element.getBoundingClientRect();
        const overlay = document.createElement('div');
        const pad = payload.padding;
        const isDocumentCapture = payload.captureMode === 'fullPage' || payload.captureMode === 'focus';

        overlay.className = '__gwi_capture_overlay ' + (isDocumentCapture
            ? '__gwi_capture_overlay--document'
            : '__gwi_capture_overlay--viewport');

        const left = (isDocumentCapture ? window.scrollX : 0) + rect.left - pad;
        const top = (isDocumentCapture ? window.scrollY : 0) + rect.top - pad;

        overlay.style.left = `${left}px`;
        overlay.style.top = `${top}px`;
        overlay.style.width = `${rect.width + pad * 2}px`;
        overlay.style.height = `${rect.height + pad * 2}px`;

        if (payload.showLabel && payload.label) {
            const labelElement = document.createElement('div');
            const placeAbove = rect.bottom + 48 > window.innerHeight;

            labelElement.className = '__gwi_capture_label' + (placeAbove ? ' __gwi_capture_label--above' : '');
            labelElement.textContent = payload.label;
            overlay.appendChild(labelElement);
        }

        document.body.appendChild(overlay);

        return {
            x: window.scrollX + rect.left - pad,
            y: window.scrollY + rect.top - pad,
            width: rect.width + pad * 2,
            height: rect.height + pad * 2,
        };
    }, { label, captureMode, showLabel, padding });
}

function numberOption(value, fallback) {
    return Number.isFinite(value) ? value : fallback;
}

function clamp(value, min, max) {
    return Math.max(min, Math.min(max, value));
}

async function focusedClip(page, highlightRects, view) {
    const rects = highlightRects.filter(Boolean);

    if (!rects.length) {
        return null;
    }

    const metrics = await page.evaluate(() => {
        const body = document.body;
        const documentElement = document.documentElement;

        return {
            width: Math.max(
                documentElement.scrollWidth,
                body ? body.scrollWidth : 0,
                window.innerWidth
            ),
            height: Math.max(
                documentElement.scrollHeight,
                body ? body.scrollHeight : 0,
                window.innerHeight
            ),
        };
    });

    const union = rects.reduce((current, rect) => {
        if (!current) {
            return { ...rect };
        }

        const left = Math.min(current.x, rect.x);
        const top = Math.min(current.y, rect.y);
        const right = Math.max(current.x + current.width, rect.x + rect.width);
        const bottom = Math.max(current.y + current.height, rect.y + rect.height);

        return {
            x: left,
            y: top,
            width: right - left,
            height: bottom - top,
        };
    }, null);

    const paddingX = numberOption(view.focusPaddingX, numberOption(view.focusPadding, 220));
    const paddingTop = numberOption(view.focusPaddingTop, numberOption(view.focusPaddingY, 120));
    const paddingBottom = numberOption(view.focusPaddingBottom, numberOption(view.focusPaddingY, 180));
    const minWidth = Math.min(metrics.width, numberOption(view.focusMinWidth, 900));
    const minHeight = Math.min(metrics.height, numberOption(view.focusMinHeight, 360));
    const maxWidth = Math.min(metrics.width, numberOption(view.focusMaxWidth, 1180));
    const maxHeight = Math.min(metrics.height, numberOption(view.focusMaxHeight, 720));

    let x = union.x - paddingX;
    let y = union.y - paddingTop;
    let width = union.width + paddingX * 2;
    let height = union.height + paddingTop + paddingBottom;

    if (width < minWidth) {
        x -= (minWidth - width) / 2;
        width = minWidth;
    }

    if (height < minHeight) {
        y -= (minHeight - height) / 2;
        height = minHeight;
    }

    width = Math.min(width, maxWidth);
    height = Math.min(height, maxHeight);
    x = clamp(x, 0, Math.max(0, metrics.width - width));
    y = clamp(y, 0, Math.max(0, metrics.height - height));
    width = Math.min(width, metrics.width - x);
    height = Math.min(height, metrics.height - y);

    return {
        x: Math.round(x),
        y: Math.round(y),
        width: Math.max(1, Math.round(width)),
        height: Math.max(1, Math.round(height)),
    };
}

async function captureView(page, baseUrl, view, language, outputDir, logger) {
    const title = localized(view.title, language) || view.id;
    const startedMs = Date.now();
    const captureMode = view.capture || 'viewport';

    const targetUrl = resolveViewUrl(view, language);

    logger.log('info', 'Capturing view', {
        screenshotId: view.id,
        language,
        title,
        url: targetUrl,
        captureMode,
    });

    await page.goto(adminUrl(baseUrl, targetUrl), { waitUntil: 'domcontentloaded' });

    if (page.url().includes('wp-login.php')) {
        throw new Error('Redirected to wp-login.php — admin session was lost.');
    }

    if (view.waitFor) {
        const readyLocator = await findFirstVisibleLocator(page, view.waitFor, 15000);

        if (!readyLocator) {
            throw new Error(`Wait selector not visible: ${view.waitFor}`);
        }
    }

    await prepareAdminPage(page, targetUrl);

    if (view.instructionSlug || view.pageSlug) {
        await page.evaluate(() => {
            const field = document.querySelector(
                '[data-key="field_gwi_instruction_sections"], [data-name="instruction_sections"]',
            );

            if (!field) {
                return;
            }

            const postbox = field.closest('.postbox, .acf-postbox, .acf-fields');

            if (postbox) {
                postbox.classList.remove('closed');
                postbox.style.display = 'block';
            }

            const group = document.querySelector('#acf-group_group_gwi_instruction_flexible');

            if (group) {
                group.classList.remove('closed');
                group.style.display = 'block';
            }

            field.style.display = 'block';
            field.style.minHeight = '320px';
            field.scrollIntoView({ block: 'center' });
            window.scrollTo(0, document.body.scrollHeight);
        });
        await page.waitForTimeout(1200);

        const metaBoxesButton = page.locator('button[aria-label="Meta Boxes"], button[aria-label="Metalaatikot"]').first();
        if (await metaBoxesButton.isVisible({ timeout: 800 }).catch(() => false)) {
            await metaBoxesButton.click().catch(() => null);
            await page.waitForTimeout(600);
        }
    }

    const invalidPostType = await page
        .locator('#wpbody-content')
        .getByText('Invalid post type.', { exact: true })
        .isVisible()
        .catch(() => false);

    if (invalidPostType) {
        throw new Error(
            `Admin screen returned "Invalid post type." — is the plugin for ${view.id} installed and active?`,
        );
    }

    const highlightRects = [];
    const skipCaptureHighlights = captureMode === 'element';

    if (!skipCaptureHighlights) {
        await injectHighlightStyles(page);

        for (const highlight of view.highlights || []) {
            const highlightRect = await applyHighlight(page, highlight, language, captureMode, logger);

            if (highlightRect) {
                highlightRects.push(highlightRect);
            }
        }

        await page.waitForTimeout(400);
    }

    const outputPath = resolve(outputDir, `${view.id}-${language}.png`);

    if (captureMode === 'focus' && view.optional && highlightRects.length === 0) {
        throw new Error('Focused capture target not found');
    }

    if (captureMode === 'element' && view.captureSelector) {
        const el = page.locator(view.captureSelector).first();
        await el.waitFor({ state: 'attached', timeout: 15000 });
        await el.scrollIntoViewIfNeeded().catch(() => null);

        const metaboxToggle = page.locator(
            '#acf-group_group_gwi_instruction_flexible .postbox-header, #acf-group_group_gwi_instruction_flexible .hndle',
        ).first();
        if (await metaboxToggle.isVisible().catch(() => false)) {
            await metaboxToggle.click().catch(() => null);
            await page.waitForTimeout(400);
        }

        if (view.highlights?.length) {
            const highlight = view.highlights[0];
            const percents = await measureHighlightPercents(
                page,
                view.captureSelector,
                highlight.selector,
                Number.isFinite(highlight.padding) ? highlight.padding : 6,
            );

            if (percents) {
                logger.log('info', 'Element highlight percents (use in gwi_screenshot_highlight_config)', {
                    screenshotId: view.id,
                    language,
                    ...percents,
                });
            } else {
                logger.log('warn', 'Could not measure element highlight percents', {
                    screenshotId: view.id,
                    language,
                    selector: highlight.selector,
                });
                logger.bumpCount('warnings');
            }
        }

        await el.screenshot({ path: outputPath, force: true, timeout: 60000 });
    } else if (captureMode === 'focus') {
        const clip = await focusedClip(page, highlightRects, view);

        if (clip) {
            await page.screenshot({ path: outputPath, clip });
            logger.log('info', 'Applied focused crop', {
                screenshotId: view.id,
                language,
                clip,
            });
        } else {
            await page.screenshot({ path: outputPath });
            logger.log('warn', 'Focused crop fell back to viewport capture', {
                screenshotId: view.id,
                language,
            });
            logger.bumpCount('warnings');
        }
    } else {
        await page.screenshot({
            path: outputPath,
            fullPage: captureMode === 'fullPage',
        });
    }

    if (captureMode === 'focus' && highlightRects.length > 0) {
        const contextPath = resolve(outputDir, `${view.id}-${language}-context.png`);

        await page.screenshot({ path: contextPath });

        logger.log('info', 'Saved context screenshot', {
            screenshotId: view.id,
            language,
            path: contextPath,
            bytes: statSync(contextPath).size,
        });
    }

    const bytes = statSync(outputPath).size;
    const durationMs = Date.now() - startedMs;

    logger.log('info', 'Saved screenshot', {
        screenshotId: view.id,
        language,
        path: outputPath,
        captureMode,
        bytes,
        durationMs,
    });
    logger.bumpCount('success');
}

async function main() {
    loadEnvFile();

    const args = parseArgs(process.argv.slice(2));

    if (args.help) {
        printHelp();
        return;
    }

    const baseUrl = requiredEnv('WP_BASE_URL');
    const { username, password } = resolveScreenshotCredentials();
    const allViews = readConfig(args.config);
    const views = args.only
        ? allViews.filter((view) => view.id === args.only)
        : allViews;

    if (args.only && views.length === 0) {
        throw new Error(`No view with id "${args.only}" in config.`);
    }

    const outputDir = resolve(args.output);
    const logger = createScreenshotLogger({
        step: `capture-${args.language}`,
        logDir: resolve(outputDir, 'logs'),
    });

    mkdirSync(outputDir, { recursive: true });

    logger.log('info', 'Capture run started', {
        baseUrl,
        language: args.language,
        user: username,
        config: resolve(args.config),
        outputDir,
        viewCount: views.length,
        jsonlPath: logger.jsonlPath,
    });

    if (username === 'admin') {
        logger.log('warn', 'WP_USER=admin is usually demoted to subscriber after ensure-screenshot-users; prefer maria.korhonen', {
            user: username,
        });
        logger.bumpCount('warnings');
    }

    const restoreUserLocale = configureCaptureUserLocale(args.language, username, logger);
    let browser;
    let page;

    let failedViews = 0;

    try {
        browser = await chromium.launch({ headless: !args.headed });
        const context = await browser.newContext({
            ignoreHTTPSErrors: true,
            viewport: { width: 1920, height: 1080 },
            deviceScaleFactor: 2,
        });

        await context.addInitScript(() => {
            try {
                for (let index = localStorage.length - 1; index >= 0; index -= 1) {
                    const key = localStorage.key(index);

                    if (!key || !key.startsWith('WP_PREFERENCES_USER_')) {
                        continue;
                    }

                    const preferences = JSON.parse(localStorage.getItem(key) || '{}');

                    preferences.core = {
                        ...(preferences.core || {}),
                        welcomeGuide: false,
                    };
                    preferences['core/edit-post'] = {
                        ...(preferences['core/edit-post'] || {}),
                        welcomeGuide: false,
                    };

                    localStorage.setItem(key, JSON.stringify(preferences));
                }
            } catch {
                // Ignore storage write failures in headless capture.
            }
        });

        page = await context.newPage();

        await login(page, baseUrl, username, password);
        logger.log('info', 'WordPress login succeeded', { baseUrl, user: username });
        await verifyAdminSession(page, baseUrl, username);
        logger.log('info', 'WordPress admin access verified', { baseUrl, user: username });

        for (const view of views) {
            try {
                await captureView(page, baseUrl, view, args.language, outputDir, logger);
            } catch (error) {
                if (view.optional) {
                    logger.log('warn', 'Skipped optional view', {
                        screenshotId: view.id,
                        language: args.language,
                        error: error.message,
                    });
                    logger.bumpCount('warnings');
                    continue;
                }

                failedViews += 1;
                logger.log('error', 'Capture failed for view', {
                    screenshotId: view.id,
                    language: args.language,
                    error: error.message,
                });
                logger.bumpCount('errors');
            }
        }
    } finally {
        if (browser) {
            await browser.close();
        }
        restoreUserLocale();
    }

    const summary = logger.finish(failedViews > 0 ? 'failed' : 'success', {
        baseUrl,
        language: args.language,
        outputDir,
        viewCount: views.length,
        failedViews,
    });

    if (summary.status === 'failed') {
        process.exitCode = 1;
    }
}

main().catch((error) => {
    console.error(error.message);
    process.exit(1);
});
