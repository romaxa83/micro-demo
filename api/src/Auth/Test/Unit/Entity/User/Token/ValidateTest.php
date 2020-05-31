<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\Token;

use App\Auth\Entity\User\Token;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @covers Token::validate
 */
class ValidateTest extends TestCase
{
    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function success(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        $token->validate($value, $expires->modify('-1 secs'));
    }

    /** @test */
    public function wrong(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        $this->expectExceptionMessage('Token is invalid.');
        $token->validate(Uuid::uuid4()->toString(), $expires->modify('-1 secs'));
    }

    /** @test */
    public function expired(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        $this->expectExceptionMessage('Token is expired.');
        $token->validate($value, $expires->modify('+1 secs'));
    }
}