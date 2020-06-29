<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Http;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HomeAction implements RequestHandlerInterface
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
//        throw new \Exception('sdas');
//        file_put_contents('php://stdout', 'Success');
//        file_put_contents('php://stderr', 'Error');
        $data = [
            'app' => 'micro-api',
            'version' => '1.0'
        ];

        return new Http\JsonResponse($data);
    }
}