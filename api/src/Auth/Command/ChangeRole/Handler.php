<?php

declare(strict_types=1);

namespace App\Auth\Command\ChangeRole;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Role;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Service\PasswordHasher;
use App\Flusher;

class Handler
{
    private UserRepository $userRepository;
    private Flusher $flusher;

    public function __construct(
        UserRepository $userRepository,
        Flusher $flusher
    )
    {
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->userRepository->getById(new Id($command->id));

        $user->changeRole(new Role($command->role));

        $this->flusher->flush();
    }
}