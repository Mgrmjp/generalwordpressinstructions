#!/usr/bin/env node

import { appendFileSync, mkdirSync, readFileSync, writeFileSync } from 'node:fs';
import { resolve } from 'node:path';
import process from 'node:process';
import { fileURLToPath } from 'node:url';

const DEFAULT_LOG_DIR = 'wordpress/wp-content/uploads/instruction-screenshots/logs';

function utcTimestamp(date = new Date()) {
    return date.toISOString();
}

function createRunId() {
    return new Date().toISOString().replace(/[-:]/g, '').replace(/\..+$/, '');
}

function resolveLogDir(logDir) {
    return resolve(logDir || process.env.GWI_SCREENSHOT_LOG_DIR || DEFAULT_LOG_DIR);
}

function resolveRunId(step, logDir) {
    if (process.env.GWI_SCREENSHOT_RUN_ID) {
        return process.env.GWI_SCREENSHOT_RUN_ID;
    }

    const runIdFile = resolve(logDir, '../.run-id');

    try {
        const existing = readFileSync(runIdFile, 'utf8').trim();

        if (existing) {
            return existing;
        }
    } catch {
        // No existing run id yet.
    }

    return createRunId();
}

function writeJsonLine(filePath, payload) {
    appendFileSync(filePath, `${JSON.stringify(payload)}\n`, 'utf8');
}

export function createScreenshotLogger(options) {
    const step = options.step;
    const logDir = resolveLogDir(options.logDir);
    const runId = options.runId || resolveRunId(step, logDir);
    const startedAt = utcTimestamp();
    const startedMs = Date.now();

    mkdirSync(logDir, { recursive: true });

    const jsonlPath = resolve(logDir, `${runId}-${step}.jsonl`);
    const summaryPath = resolve(logDir, `${runId}-${step}-summary.json`);
    const pipelinePath = resolve(logDir, 'pipeline.jsonl');
    const runIdFile = resolve(logDir, '../.run-id');

    writeFileSync(runIdFile, `${runId}\n`, 'utf8');

    const counts = {
        success: 0,
        updated: 0,
        imported: 0,
        linked: 0,
        skipped: 0,
        warnings: 0,
        errors: 0,
    };

    function log(level, message, data = {}) {
        const entry = {
            ts: utcTimestamp(),
            runId,
            step,
            level,
            message,
            data,
        };

        writeJsonLine(jsonlPath, entry);
        writeJsonLine(pipelinePath, entry);

        const prefix = `[gwi:${step}:${level}]`;
        const detail = Object.keys(data).length > 0 ? ` ${JSON.stringify(data)}` : '';

        if (level === 'error') {
            console.error(`${prefix} ${message}${detail}`);
            return;
        }

        if (level === 'warn') {
            console.warn(`${prefix} ${message}${detail}`);
            return;
        }

        console.log(`${prefix} ${message}${detail}`);
    }

    function bumpCount(key) {
        if (Object.prototype.hasOwnProperty.call(counts, key)) {
            counts[key] += 1;
        }
    }

    function finish(status, extra = {}) {
        const finishedAt = utcTimestamp();
        const durationMs = Date.now() - startedMs;
        const summary = {
            runId,
            step,
            status,
            startedAt,
            finishedAt,
            durationMs,
            counts,
            ...extra,
        };

        writeFileSync(summaryPath, `${JSON.stringify(summary, null, 2)}\n`, 'utf8');
        log('info', 'Run complete', { status, durationMs, counts, summaryPath });

        return summary;
    }

    return {
        runId,
        jsonlPath,
        summaryPath,
        log,
        bumpCount,
        finish,
    };
}

function parseCliArgs(argv) {
    const args = {
        step: '',
        level: 'info',
        message: '',
        data: {},
        logDir: '',
        runId: '',
    };

    for (let index = 0; index < argv.length; index += 1) {
        const arg = argv[index];

        if (arg === '--step') {
            args.step = argv[index + 1] || '';
            index += 1;
        } else if (arg === '--level') {
            args.level = argv[index + 1] || args.level;
            index += 1;
        } else if (arg === '--message') {
            args.message = argv[index + 1] || '';
            index += 1;
        } else if (arg === '--data') {
            args.data = JSON.parse(argv[index + 1] || '{}');
            index += 1;
        } else if (arg === '--log-dir') {
            args.logDir = argv[index + 1] || '';
            index += 1;
        } else if (arg === '--run-id') {
            args.runId = argv[index + 1] || '';
            index += 1;
        }
    }

    return args;
}

function runCli(argv) {
    const args = parseCliArgs(argv);

    if (!args.step || !args.message) {
        throw new Error('Usage: node screenshot-log.mjs --step <step> --message <message> [--level info] [--data "{}"]');
    }

    const logger = createScreenshotLogger({
        step: args.step,
        logDir: args.logDir || undefined,
        runId: args.runId || undefined,
    });

    logger.log(args.level, args.message, args.data);
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
    runCli(process.argv.slice(2));
}
