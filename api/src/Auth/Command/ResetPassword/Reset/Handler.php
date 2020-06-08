<?php

declare(strict_types=1);

namespace App\Auth\Command\ResetPassword\Reset;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Service\PasswordHasher;
use App\Flusher;

class Handler
{
    private UserRepository $userRepository;
    private Flusher $flusher;
    private PasswordHasher $passwordHasher;

    public function __construct(
        UserRepository $userRepository,
        PasswordHasher $passwordHasher,
        Flusher $flusher
    )
    {
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
        $this->passwordHasher = $passwordHasher;
    }

    public function handle(Command $command): void
    {
        if(!$user = $this->userRepository->findByPasswordResetToken($command->token)){
            throw new \DomainException('Token is not found');
        }

        $user->resetPassword(
            $command->token,
            new \DateTimeImmutable(),
            $this->passwordHasher->hash($command->password)
        );

        $this->flusher->flush();
    }
}