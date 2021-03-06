<?php

declare(strict_types=1);

use App\Http\Action;
use Slim\App;
use App\Http\Action\HomeAction;
use Slim\Routing\RouteCollectorProxy;

return static function(App $app): void {
    $app->get('/', HomeAction::class);

    $app->group('/v1', function (RouteCollectorProxy $group): void {

        $group->group('/auth', function (RouteCollectorProxy $group): void {
            $group->post('/signup', Action\V1\Auth\SignUp\RequestAction::class);
            $group->post('/signup/confirm', Action\V1\Auth\SignUp\ConfirmAction::class);
        });
    });
};