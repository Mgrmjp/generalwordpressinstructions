<?php

if (!function_exists('gwi_screenshot_log_init')) {
    /**
     * Initialize screenshot pipeline logging for a step.
     */
    function gwi_screenshot_log_init(string $step, string $screenshot_dir): void
    {
        $log_dir = trailingslashit($screenshot_dir) . 'logs';
        wp_mkdir_p($log_dir);

        $run_id = getenv('GWI_SCREENSHOT_RUN_ID') ?: '';

        if ($run_id === '' && file_exists(trailingslashit($screenshot_dir) . '.run-id')) {
            $run_id = trim((string) file_get_contents(trailingslashit($screenshot_dir) . '.run-id'));
        }

        if ($run_id === '') {
            $run_id = gmdate('Ymd\THis\Z');
        }

        file_put_contents(trailingslashit($screenshot_dir) . '.run-id', $run_id . PHP_EOL);

        $GLOBALS['gwi_screenshot_log'] = [
            'step' => $step,
            'run_id' => $run_id,
            'log_dir' => $log_dir,
            'jsonl_path' => trailingslashit($log_dir) . $run_id . '-' . $step . '.jsonl',
            'summary_path' => trailingslashit($log_dir) . $run_id . '-' . $step . '-summary.json',
            'pipeline_path' => trailingslashit($log_dir) . 'pipeline.jsonl',
            'started_at' => gmdate('c'),
            'started_ms' => (int) round(microtime(true) * 1000),
            'counts' => [
                'success' => 0,
                'updated' => 0,
                'imported' => 0,
                'linked' => 0,
                'skipped' => 0,
                'warnings' => 0,
                'errors' => 0,
            ],
        ];
    }

    /**
     * Write one structured log event.
     */
    function gwi_screenshot_log(string $level, string $message, array $data = []): void
    {
        if (!isset($GLOBALS['gwi_screenshot_log'])) {
            return;
        }

        $context = $GLOBALS['gwi_screenshot_log'];
        $entry = [
            'ts' => gmdate('c'),
            'runId' => $context['run_id'],
            'step' => $context['step'],
            'level' => $level,
            'message' => $message,
            'data' => $data,
        ];

        $line = wp_json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
        file_put_contents($context['jsonl_path'], $line, FILE_APPEND);
        file_put_contents($context['pipeline_path'], $line, FILE_APPEND);

        $detail = $data !== [] ? ' ' . wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '';
        $prefix = sprintf('[gwi:%s:%s]', $context['step'], $level);

        if ($level === 'error') {
            fwrite(STDERR, $prefix . ' ' . $message . $detail . PHP_EOL);
            $GLOBALS['gwi_screenshot_log']['counts']['errors']++;
            return;
        }

        if ($level === 'warn') {
            fwrite(STDERR, $prefix . ' ' . $message . $detail . PHP_EOL);
            $GLOBALS['gwi_screenshot_log']['counts']['warnings']++;
            return;
        }

        echo $prefix . ' ' . $message . $detail . PHP_EOL;
    }

    /**
     * Increment a summary counter.
     */
    function gwi_screenshot_log_count(string $key): void
    {
        if (!isset($GLOBALS['gwi_screenshot_log']['counts'][$key])) {
            return;
        }

        $GLOBALS['gwi_screenshot_log']['counts'][$key]++;
    }

    /**
     * Write run summary JSON and final log line.
     */
    function gwi_screenshot_log_finish(string $status, array $extra = []): void
    {
        if (!isset($GLOBALS['gwi_screenshot_log'])) {
            return;
        }

        $context = $GLOBALS['gwi_screenshot_log'];
        $finished_at = gmdate('c');
        $duration_ms = (int) round(microtime(true) * 1000) - $context['started_ms'];
        $summary = array_merge([
            'runId' => $context['run_id'],
            'step' => $context['step'],
            'status' => $status,
            'startedAt' => $context['started_at'],
            'finishedAt' => $finished_at,
            'durationMs' => $duration_ms,
            'counts' => $context['counts'],
            'summaryPath' => $context['summary_path'],
            'jsonlPath' => $context['jsonl_path'],
        ], $extra);

        file_put_contents(
            $context['summary_path'],
            wp_json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL
        );

        gwi_screenshot_log('info', 'Run complete', [
            'status' => $status,
            'durationMs' => $duration_ms,
            'counts' => $context['counts'],
            'summaryPath' => $context['summary_path'],
        ]);
    }
}
