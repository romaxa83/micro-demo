<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use App\Auth\Service\PasswordHasher;

class User
{
    private Id $id;
    private \DateTimeImmutable $date;
    private Email $email;
    private ?string $passwordHash = null;
    private ?Token $signUpConfirmToken = null;
    private ?Token $passwordResetToken = null;
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

    public function requestPasswordReset(Token $token, \DateTimeImmutable $date)
    {
        if(!$this->isActive()){
            throw new \DomainException('User is not active.');
        }
        if($this->passwordResetToken !== null && !$this->passwordResetToken->isExpiredTo($date)){
            throw new \DomainException('Resetting is already requested.');
        }
        $this->passwordResetToken = $token;
    }

    public function changePassword(string $current, string $new, PasswordHasher $hasher): void
    {
        if ($this->passwordHash === null) {
            throw new \DomainException('User does not have an old password.');
        }
        if (!$hasher->validate($current, $this->passwordHash)) {
            throw new \DomainException('Incorrect current password.');
        }
        $this->passwordHash = $hasher->hash($new);
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

    public function resetPassword(string $token, \DateTimeImmutable $date, string $hash): void
    {
        if($this->passwordResetToken === null){
            throw new \DomainException('Resetting is not requested.');
        }
        $this->passwordResetToken->validate($token, $date);
        $this->passwordResetToken = null;
        $this->passwordHash = $hash;
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

    public function getPasswordResetToken(): ?Token
    {
        return $this->passwordResetToken;
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

