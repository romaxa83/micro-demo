<?php

declare(strict_types=1);

namespace App\Auth\Command\AttachNetwork;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\NetworkIdentity;
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
        $identity = new NetworkIdentity($command->network, $command->identity);

        if($this->userRepository->hasByNetwork($identity)){
            throw new \DomainException('User with this network already exists.');
        }

        $user = $this->userRepository->getById(new Id($command->id));

        $user->attachNetwork($identity);

        $this->flusher->flush();
    }
}