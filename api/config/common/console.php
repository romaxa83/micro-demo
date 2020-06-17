<?php

declare(strict_types=1);

use App\Console\HelloCommand;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;

return [
    'config' => [
        'console' => [
            'commands' => [
                HelloCommand::class,
                ValidateSchemaCommand::class,
            ],
        ],
    ],
];