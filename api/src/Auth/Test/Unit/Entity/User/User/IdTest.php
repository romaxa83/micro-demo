<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\Id;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/** @covers Id */
class IdTest extends TestCase
{
    /** @test */
    public function success(): void
    {
        $id = new Id($value = Uuid::uuid4()->toString());

        self::assertEquals($value, $id->getValue());
    }

    /** @test */
    public function case(): void
    {
        $value = Uuid::uuid4()->toString();
        $id = new Id(mb_strtoupper($value));

        self::assertEquals($value, $id->getValue());
    }

    /** @test */
    public function generate(): void
    {
        $id = Id::generate();

        self::assertNotEmpty($id->getValue());
    }

    /** @test */
    public function incorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Id('12345');
    }

    /** @test */
    public function empty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Id('');
    }
}