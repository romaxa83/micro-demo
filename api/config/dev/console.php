<?php

declare(strict_types=1);

use Doctrine\ORM\Tools\Console\Command\SchemaTool;

return [
    'config' => [
        'console' => [
            'commands' => [
                SchemaTool\DropCommand::class,
            ]
        ],
    ],
];