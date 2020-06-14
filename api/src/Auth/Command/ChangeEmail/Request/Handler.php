<?php

declare(strict_types=1);

namespace App\Auth\Command\ChangeEmail\Request;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\NetworkIdentity;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Service\NewEmailConfirmSender;
use App\Auth\Service\PasswordHasher;
use App\Auth\Service\Tokenizer;
use App\Flusher;

class Handler
{
    private UserRepository $userRepository;
    private Flusher $flusher;
    private Tokenizer $tokenizer;
    private NewEmailConfirmSender $sender;

    public function __construct(
        UserRepository $userRepository,
        Flusher $flusher,
        Tokenizer $tokenizer,
        NewEmailConfirmSender $sender
    )
    {
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
    }

    public function handle(Command $command): void
    {
        $user = $this->userRepository->getById(new Id($command->id));

        $email = new Email($command->email);

        if($this->userRepository->hasByEmail($email)){
            throw new \DomainException('Email is already in use.');
        }

        $date = new \DateTimeImmutable();

        $user->requestEmailChanging(
            $token = $this->tokenizer->generate($date),
            $date,
            $email
        );

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}