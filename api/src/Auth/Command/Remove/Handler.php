<?php

declare(strict_types=1);

namespace App\Auth\Command\Remove;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\UserRepository;
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

        // помечаем пользователь как удаленого
        // но не удаляем из бд
        $user->remove();

        $this->userRepository->remove($user);

        $this->flusher->flush();
    }
}