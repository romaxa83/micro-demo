<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\SignUp;

use App\Auth\Command\SignUpByEmail\Request\Command;
use App\Auth\Command\SignUpByEmail\Request\Handler;
use App\Http\EmptyResponse;
use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

class RequestAction implements RequestHandlerInterface
{
    private Handler $commandHandler;
    private Validator $validator;

    public function __construct(Handler $commandHandler, Validator $validator)
    {
        $this->commandHandler = $commandHandler;
        $this->validator = $validator;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        $data = $request->getParsedBody();

        $command = new Command();
        $command->email = trim($data['email'] ?? '');
        $command->password = trim($data['password'] ?? '');

        $this->validator->validate($command);

        try {
            $this->commandHandler->handle($command);

            return new EmptyResponse(201);
        } catch (\DomainException $exception) {

            return new JsonResponse(['message' => $exception->getMessage()], 409);
        }
    }
}
