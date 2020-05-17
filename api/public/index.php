<?php

declare(strict_types=1);

use DI\Container;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//устанавливаем код если на сервере будет ошибки,т.к. при ошибки возвращаеться код 200
http_response_code(500);

require __DIR__ . '/../vendor/autoload.php';

$builder = new \DI\ContainerBuilder();

$builder->addDefinitions([
    'config' => [
        'debug' => (bool)getenv('APP_DEBUG')
    ],
    \Psr\Http\Message\ResponseFactoryInterface::class => Di\get(Slim\Psr7\Factory\ResponseFactory::class)
]);

$container = $builder->build();

$app = AppFactory::createFromContainer($container);

// устанавливаем мидлевар для оброботки ошибок
$app->addErrorMiddleware($container->get('config')['debug'], true, true);

$app->get('/', \App\Http\Action\HomeAction::class);

$app->run();
