<?php

declare(strict_types=1);

use Doctrine\ORM\Tools\Console\Command\SchemaTool;
use Doctrine\Migrations;
use App\Console\FixturesLoadCommand;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;

return [
    FixturesLoadCommand::class => static function (ContainerInterface $container) {

        $config = $container->get('config')['console'];

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);

        return new FixturesLoadCommand(
            $em,
            $config['fixture_paths'],
        );
    },


    'config' => [
        'console' => [
            'commands' => [
                FixturesLoadCommand::class,
                SchemaTool\DropCommand::class,
                Migrations\Tools\Console\Command\DiffCommand::class,
                Migrations\Tools\Console\Command\GenerateCommand::class,
                \App\Console\MailCheckCommand::class
            ],
            'fixture_paths' => [
                __DIR__ . '/../../src/Auth/Fixture',
            ],
        ],
    ],
];