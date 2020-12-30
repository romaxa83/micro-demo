<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\SignUp;


use App\Auth\Command\SignUpByEmail\Confirm\Command;
use App\Auth\Command\SignUpByEmail\Confirm\Handler;
use App\Http\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ConfirmAction implements RequestHandlerInterface
{
    private Handler $handler;

    public function __construct(Handler $handler)
    {
        $this->handler = $handler;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @psalm-var array{token:?string} $data
         */
        $data = $request->getParsedBody();

        $command = new Command();
        $command->token = $data['token'] ?? '';

        $this->handler->handle($command);

        return new EmptyResponse(200);
    }
}
