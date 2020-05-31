<?php

declare(strict_types=1);

namespace App\Auth\Command\SignUpByEmail\Request;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Service\PasswordHasher;
use App\Auth\Service\SignUpConfirmationSender;
use App\Auth\Service\Tokenizer;
use App\Flusher;

class Handler
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var PasswordHasher
     */
    private PasswordHasher $hasher;
    /**
     * @var Tokenizer
     */
    private Tokenizer $tokenizer;
    /**
     * @var Flusher
     */
    private Flusher $flusher;
    /**
     * @var SIgnUpConfirmationSender
     */
    private SIgnUpConfirmationSender $sender;

    public function __construct(
        UserRepository $userRepository,
        PasswordHasher $hasher,
        Tokenizer $tokenizer,
        Flusher $flusher,
        SIgnUpConfirmationSender $sender
    )
    {

        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
        $this->tokenizer = $tokenizer;
        $this->flusher = $flusher;
        $this->sender = $sender;
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if($this->userRepository->hasByEmail($email)){
            throw new \DomainException('User already exists.');
        }

        $date = new \DateTimeImmutable();

        $user = new User(
            Id::generate(),
            $date,
            $email,
            $this->hasher->hash($command->password),
            $token = $this->tokenizer->generate($date)
        );

        $this->userRepository->add($user);

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}