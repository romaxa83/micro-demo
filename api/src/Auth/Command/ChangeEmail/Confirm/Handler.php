<?php

declare(strict_types=1);

namespace App\Auth\Command\ChangeEmail\Confirm;

use App\Auth\Entity\User\UserRepository;
use App\Flusher;

class Handler
{
    private UserRepository $userRepository;
    private Flusher $flusher;

    public function __construct(UserRepository $userRepository, Flusher $flusher)
    {
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
    }

    /**
     * @param Command $command
     * @throws \Exception
     */
    public function handle(Command $command): void
    {
        if(!$user = $this->userRepository->findByNewEmailToken($command->token)){
            throw new \Exception('Incorrect token.');
        }

        $user->confirmEmailChanging($command->token, new \DateTimeImmutable());

        $this->flusher->flush();
    }
}