<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

class User
{
    /**
     * @var Id
     */
    private Id $id;
    private \DateTimeImmutable $date;
    /**
     * @var Email
     */
    private Email $email;
    private string $passwordHash;
    /**
     * @var Token
     */
    private ?Token $signUpConfirmToken;

    private Status $status;

    public function __construct(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        Token $token
    )
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->signUpConfirmToken = $token;
        $this->status = Status::wait();
    }

    public function confirmSignUp(string $token, \DateTimeImmutable $date): void
    {
        if($this->signUpConfirmToken == null){
            throw new \DomainException('Confirmation is not required.');
        }

        $this->signUpConfirmToken->validate($token, $date);
        $this->status = Status::active();
        $this->signUpConfirmToken = null;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * @return Token
     */
    public function getSignUpConfirmToken(): ?Token
    {
        return $this->signUpConfirmToken;
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }
}

