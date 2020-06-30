<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\SignUp;

use App\Auth\Command\SignUpByEmail\Request\Command;
use App\Auth\Command\SignUpByEmail\Request\Handler;
use App\Http\EmptyResponse;
use App\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestAction implements RequestHandlerInterface
{
    private Handler $commandHandler;

    public function __construct(Handler $commandHandler)
    {
        $this->commandHandler = $commandHandler;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = json_decode((string)$request->getBody(), true);

        $command = new Command();
        $command->email = trim($data['email'] ?? '');
        $command->password = trim($data['password'] ?? '');

        try {
            $this->commandHandler->handle($command);
            return new EmptyResponse(201);
        } catch (\DomainException $exception) {
//            dd($exception->getMessage());
            return new JsonResponse(['message' => $exception->getMessage()], 409);
        }
    }
}
