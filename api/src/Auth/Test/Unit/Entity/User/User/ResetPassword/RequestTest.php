<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\ResetPassword;

use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/** @covers User */
class RequestTest extends TestCase
{
    /** @test */
    public function success(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getPasswordResetToken());
        self::assertEquals($token, $user->getPasswordResetToken());
    }

    /**
     * токен пользователю уже отправлен
     * @test
     */
    public function already(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Resetting is already requested.');
        $user->requestPasswordReset($token, $now);
    }

    /**
     * старый токен истек , отправляем новый
     * @test
     */
    public function expired(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));
        $user->requestPasswordReset($token, $now);

        $newDate = $now->modify('+2 hour');
        $newToken = $this->createToken($newDate->modify('+1 hour'));
        $user->requestPasswordReset($newToken, $newDate);

        self::assertEquals($newToken, $user->getPasswordResetToken());
    }

    /**
     * запрос на сброс пароля для неактивированого пользователя
     * @test
     */
    public function user_not_active(): void
    {
        $user = (new UserBuilder())->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $this->expectExceptionMessage('User is not active.');
        $user->requestPasswordReset($token, $now);
    }

    private function createToken($date): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            $date
        );
    }
}