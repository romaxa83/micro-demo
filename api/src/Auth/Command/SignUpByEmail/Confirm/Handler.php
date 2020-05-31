<?php

declare(strict_types=1);

namespace App\Auth\Command\SignUpByEmail\Confirm;

use App\Auth\Entity\User\UserRepository;
use App\Flusher;

class Handler
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var Flusher
     */
    private Flusher $flusher;

    public function __construct(UserRepository $userRepository, Flusher $flusher)
    {
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        if(!$user = $this->userRepository->findByConfirmToken($command->token)){
            throw new \Exception('Incorrect token.');
        }

        $user->confirmSignUp($command->token, new \DateTimeImmutable());

        $this->flusher->flush();
    }
}