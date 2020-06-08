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
    private ?string $passwordHash = null;
    /**
     * @var Token
     */
    private ?Token $signUpConfirmToken = null;

    private Status $status;

    private \ArrayObject $networks;

    public function __construct(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        Status $status
    )
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->status = $status;
        $this->networks = new \ArrayObject();
    }

    public static function requestSignUpByEmail(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        Token $token
    ):self
    {
        $user = new self($id, $date, $email, Status::wait());
        $user->passwordHash = $passwordHash;
        $user->signUpConfirmToken = $token;

        return $user;
    }

    public static function signUpByNetwork(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        NetworkIdentity $identity
    ):self
    {
        $user = new self($id, $date, $email, Status::active());
        $user->networks->append($identity);

        return $user;
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

    public function attachNetwork(NetworkIdentity $identity): void
    {
        /** @var $existing NetworkIdentity */
        foreach ($this->networks as $existing){
            if($existing->isEqual($identity)){
                throw new \DomainException('Network is already attached.');
            }
        }
        $this->networks->append($identity);
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

    /**
     * @return NetworkIdentity[]
     */
    public function getNetworks(): array
    {
        /** @var NetworkIdentity[] */
        return $this->networks->getArrayCopy();
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

