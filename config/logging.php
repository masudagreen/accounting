<?php

declare(strict_types=1);

/**
 * Monolog channel definitions (Phase 2).
 *
 * Each channel is a list of handler specs. The Rucaro logger factory
 * (see {@see \Rucaro\Infrastructure\Logging\LoggerFactory}) maps each
 * handler spec onto a concrete Monolog handler instance.
 *
 * Handler spec shape:
 *   - type:      "rotating_file" | "stream" | "stderr" | "errorlog"
 *   - level:     string|int log level (Monolog\Level name / value)
 *   - path:      filesystem path (rotating_file) or PHP stream URI (stream)
 *   - max_files: rotating_file retention (days)
 *   - formatter: "line" (default) | "json"
 *
 * Paths that are not absolute are resolved relative to the project root so
 * tests and CLI tools produce identical on-disk layouts regardless of CWD.
 *
 * @return array{
 *     default: string,
 *     channels: array<string, array{handlers: list<array<string, mixed>>}>,
 * }
 */
return [
    'default' => (string) ($_ENV['LOG_CHANNEL'] ?? 'app'),

    'channels' => [
        'app' => [
            'handlers' => [
                [
                    'type' => 'rotating_file',
                    'path' => (string) ($_ENV['LOG_PATH'] ?? 'storage/logs/app.log'),
                    'level' => (string) ($_ENV['LOG_LEVEL'] ?? 'debug'),
                    'max_files' => 14,
                ],
                [
                    'type' => 'stderr',
                    'level' => 'warning',
                ],
            ],
        ],
        'audit' => [
            'handlers' => [
                [
                    'type' => 'rotating_file',
                    'path' => 'storage/logs/audit.log',
                    'level' => 'info',
                    'max_files' => 30,
                ],
            ],
        ],
        'testing' => [
            'handlers' => [
                [
                    'type' => 'stream',
                    'path' => 'php://memory',
                    'level' => 'debug',
                ],
            ],
        ],
    ],
];
