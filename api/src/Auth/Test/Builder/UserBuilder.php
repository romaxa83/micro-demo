<?php

declare(strict_types=1);

namespace App\Auth\Test\Builder;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\NetworkIdentity;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use Ramsey\Uuid\Uuid;

class UserBuilder
{
    private Id $id;
    private Email $email;
    private string $hash;
    private \DateTimeImmutable $date;
    private Token $signUpToken;
    private bool $active = false;
    private ?NetworkIdentity $networkIdentity = null;

    public function __construct()
    {
        $this->id = Id::generate();
        $this->email = new Email('user@user.com');
        $this->hash = 'hash';
        $this->date = new \DateTimeImmutable();
        $this->signUpToken = new Token(Uuid::uuid4()->toString(), $this->date->modify('+1 day'));
    }

    public function withSignUpConfirmToken(Token $token): self
    {
        $clone = clone $this;
        $clone->signUpToken = $token;
        return $clone;
    }

    public function withEmail(Email $email):self
    {
        $clone = clone $this;
        $clone->email = $email;
        return $clone;
    }

    public function viaNetwork(NetworkIdentity $network = null): self
    {
        $clone = clone $this;
        $clone->networkIdentity = $network ?? new NetworkIdentity('fb', '0000001');
        return $clone;
    }

    public function active():self
    {
        $clone = clone $this;
        $clone->active = true;
        return $clone;
    }

    public function build(): User
    {
        if ($this->networkIdentity !== null) {
            return User::signUpByNetwork(
                $this->id,
                $this->date,
                $this->email,
                $this->networkIdentity
            );
        }

        $user = User::requestSignUpByEmail(
            $this->id,
            $this->date,
            $this->email,
            $this->hash,
            $this->signUpToken
        );

        if($this->active){
            $user->confirmSignUp(
                $this->signUpToken->getValue(),
                $this->signUpToken->getExpires()->modify('-1 day'),
            );
        }

        return $user;
    }
}