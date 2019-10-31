<?php

return [
    'session' => [
        'storage' => $env['SESSION_STORAGE'] ?? 'fs',
        'redis' => [
            'prefix' => 'two_sessions:',
            'service' => 'redis',
        ],
        'fs' => [
            'path' => null,
        ],
        'options' => [],
    ],
];