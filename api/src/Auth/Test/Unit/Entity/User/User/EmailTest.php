<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @covers Email */
class EmailTest extends TestCase
{
    /** @test */
    public function success(): void
    {
        $email = new Email($value = 'email@app.test');

        self::assertEquals('email@app.test', $email->getValue());
    }

    /** @test */
    public function case(): void
    {
        $email = new Email($value = 'EmAil@app.test');

        self::assertEquals('email@app.test', $email->getValue());
    }

    /** @test */
    public function incorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Email('not-email');
    }

    /** @test */
    public function empty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Email('');
    }
}
