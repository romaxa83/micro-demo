<?php

declare(strict_types=1);

namespace App\Auth\Command\SignUpByNetwork;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Network;
use App\Auth\Entity\User\User;
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
        $identity = new Network($command->network, $command->identity);
        $email = new Email($command->email);

        if($this->userRepository->hasByNetwork($identity)){
            throw new \DomainException('User with this network already exists.');
        }

        if($this->userRepository->hasByEmail($email)){
            throw new \DomainException('User already exists.');
        }

        $date = new \DateTimeImmutable();

        $user = User::signUpByNetwork(
            Id::generate(),
            $date,
            $email,
            $identity
        );

        $this->userRepository->add($user);

        $this->flusher->flush();
    }
}