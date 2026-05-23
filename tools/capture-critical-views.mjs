#!/usr/bin/env node

import { chromium } from '@playwright/test';
import { existsSync, mkdirSync, readFileSync } from 'node:fs';
import { resolve } from 'node:path';
import process from 'node:process';

const DEFAULT_CONFIG = 'config/critical-views.json';
const DEFAULT_OUTPUT = 'wordpress/wp-content/uploads/instruction-screenshots';

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
        headed: false,
        help: false,
    };

    for (let index = 0; index < argv.length; index += 1) {
        const arg = argv[index];

        if (arg === '--help' || arg === '-h') {
            args.help = true;
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
  WP_USER       WordPress username
  WP_PASSWORD   WordPress password

Options:
  --config <path>      JSON config path. Default: ${DEFAULT_CONFIG}
  --output <path>      Screenshot output directory. Default: ${DEFAULT_OUTPUT}
  --language <en|fi>   Label language. Default: en
  --headed             Run Chromium visibly for debugging
  --help               Show this help
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

async function login(page, baseUrl, username, password) {
    await page.goto(adminUrl(baseUrl, '/wp-login.php'), { waitUntil: 'domcontentloaded' });
    await page.fill('#user_login', username);
    await page.fill('#user_pass', password);
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle' }).catch(() => null),
        page.click('#wp-submit'),
    ]);

    if (page.url().includes('wp-login.php')) {
        throw new Error('WordPress login failed. Check WP_USER and WP_PASSWORD.');
    }
}

async function injectHighlightStyles(page) {
    await page.addStyleTag({
        content: `
            .__gwi_capture_target {
                outline: 6px solid #ffd400 !important;
                outline-offset: 4px !important;
                box-shadow: 0 0 0 4px #111, 0 0 0 12px rgba(255, 212, 0, 0.45) !important;
                border-radius: 6px !important;
            }

            .__gwi_capture_overlay {
                border: 6px solid #ffd400 !important;
                border-radius: 8px !important;
                box-shadow: 0 0 0 4px #111, 0 0 0 12px rgba(255, 212, 0, 0.45) !important;
                box-sizing: border-box !important;
                pointer-events: none !important;
                position: absolute !important;
                z-index: 2147483647 !important;
            }

            .__gwi_capture_label {
                background: #111 !important;
                border: 3px solid #ffd400 !important;
                border-radius: 999px !important;
                color: #fff !important;
                font: 900 16px/1.1 Arial, sans-serif !important;
                left: -6px !important;
                padding: 8px 12px !important;
                position: absolute !important;
                top: calc(100% + 10px) !important;
                white-space: nowrap !important;
            }
        `,
    });
}

async function findFirstVisibleLocator(page, selector) {
    const selectors = selector.split(',').map((item) => item.trim()).filter(Boolean);

    for (const currentSelector of selectors) {
        const locator = page.locator(currentSelector).first();

        if ((await locator.count()) === 0) {
            continue;
        }

        try {
            await locator.waitFor({ state: 'visible', timeout: 2000 });
            return locator;
        } catch {
            continue;
        }
    }

    return null;
}

async function applyHighlight(page, highlight, language) {
    const locator = await findFirstVisibleLocator(page, highlight.selector);

    if (!locator) {
        if (highlight.optional) {
            console.warn(`Optional selector not found: ${highlight.selector}`);
            return;
        }

        throw new Error(`Required selector not found: ${highlight.selector}`);
    }

    const label = localized(highlight.label, language);
    await locator.scrollIntoViewIfNeeded();
    await locator.evaluate((element, currentLabel) => {
        element.classList.add('__gwi_capture_target');

        const rect = element.getBoundingClientRect();
        const overlay = document.createElement('div');
        overlay.className = '__gwi_capture_overlay';
        overlay.style.left = `${window.scrollX + rect.left - 8}px`;
        overlay.style.top = `${window.scrollY + rect.top - 8}px`;
        overlay.style.width = `${rect.width + 16}px`;
        overlay.style.height = `${rect.height + 16}px`;

        if (currentLabel) {
            const labelElement = document.createElement('div');
            labelElement.className = '__gwi_capture_label';
            labelElement.textContent = currentLabel;
            overlay.appendChild(labelElement);
        }

        document.body.appendChild(overlay);
    }, label);
}

async function captureView(page, baseUrl, view, language, outputDir) {
    const title = localized(view.title, language) || view.id;
    console.log(`Capturing: ${title}`);

    await page.goto(adminUrl(baseUrl, view.url), { waitUntil: 'domcontentloaded' });

    if (view.waitFor) {
        await page.locator(view.waitFor).first().waitFor({ state: 'visible', timeout: 15000 });
    }

    await injectHighlightStyles(page);

    for (const highlight of view.highlights || []) {
        await applyHighlight(page, highlight, language);
    }

    await page.waitForTimeout(250);

    const outputPath = resolve(outputDir, `${view.id}-${language}.png`);
    await page.screenshot({ path: outputPath, fullPage: true });
    console.log(`Saved: ${outputPath}`);
}

async function main() {
    loadEnvFile();

    const args = parseArgs(process.argv.slice(2));

    if (args.help) {
        printHelp();
        return;
    }

    const baseUrl = requiredEnv('WP_BASE_URL');
    const username = requiredEnv('WP_USER');
    const password = requiredEnv('WP_PASSWORD');
    const views = readConfig(args.config);
    const outputDir = resolve(args.output);

    mkdirSync(outputDir, { recursive: true });

    const browser = await chromium.launch({ headless: !args.headed });
    const context = await browser.newContext({
        ignoreHTTPSErrors: true,
        viewport: { width: 1440, height: 1000 },
    });
    const page = await context.newPage();

    try {
        await login(page, baseUrl, username, password);

        for (const view of views) {
            await captureView(page, baseUrl, view, args.language, outputDir);
        }
    } finally {
        await browser.close();
    }
}

main().catch((error) => {
    console.error(error.message);
    process.exit(1);
});
