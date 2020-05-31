<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\SignUpByEmail;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/** @covers User */
class ConfirmTest extends TestCase
{
    /** @test */
    public function success(): void
    {
        $user = (new UserBuilder())
            ->withSignUpConfirmToken($token = $this->createToken())
            ->build();

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        $user->confirmSignUp(
            $token->getValue(),
            $token->getExpires()->modify('-1 day')
        );

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertNull($user->getSignUpConfirmToken());
    }

    /** @test */
    public function wrong(): void
    {
        $user = (new UserBuilder())
            ->withSignUpConfirmToken($token = $this->createToken())
            ->build();

        $this->expectExceptionMessage('Token is invalid.');

        $user->confirmSignUp(
            Uuid::uuid4()->toString(),
            $token->getExpires()->modify('-1 day')
        );
    }

    /** @test */
    public function expired(): void
    {
        $user = (new UserBuilder())
            ->withSignUpConfirmToken($token = $this->createToken())
            ->build();

        $this->expectExceptionMessage('Token is expired.');

        $user->confirmSignUp(
            $token->getValue(),
            $token->getExpires()->modify('+1 day')
        );
    }

    /** @test */
    public function already_active(): void
    {
        $token = $this->createToken();

        $user = (new UserBuilder())
            ->withSignUpConfirmToken($token)
            ->active()
            ->build();

        $this->expectExceptionMessage('Confirmation is not required.');

        $user->confirmSignUp(
            $token->getValue(),
            $token->getExpires()->modify('-1 day')
        );
    }

    private function createToken(): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            new \DateTimeImmutable('+1 day')
        );
    }
}