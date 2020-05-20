<?php

declare(strict_types=1);

use Slim\App;

//устанавливаем код если на сервере будет ошибки,т.к. при ошибки возвращаеться код 200
http_response_code(500);

require __DIR__ . '/../vendor/autoload.php';

/** @var \Psr\Container\ContainerInterface $container */
$container = require __DIR__ . '/../config/container.php';

/** @var App $app */
$app = (require __DIR__ . '/../config/app.php')($container);
$app->run();
