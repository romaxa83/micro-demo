<?php

declare(strict_types=1);

// переопределяем настройки doctrine для окружения test
return [
    'config' => [
        'doctrine' => [
            'dev_mode' => true,
            'cache_dir' => null,
            'proxy_dir' => __DIR__ . '/../../var/cache/' . PHP_SAPI . '/doctrine/proxy',
        ],
    ],
];