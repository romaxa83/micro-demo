<?php

declare(strict_types=1);

namespace App\Auth\Command\ChangePassword;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\NetworkIdentity;
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
        Flusher $flusher,
        PasswordHasher $passwordHasher
    )
    {
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
        $this->passwordHasher = $passwordHasher;
    }

    public function handle(Command $command): void
    {
        $user = $this->userRepository->getById(new Id($command->id));

        $user->changePassword(
            $command->current,
            $command->new,
            $this->passwordHasher
        );

        $this->flusher->flush();
    }
}