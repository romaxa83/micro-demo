<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Network;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/** @covers Network */
class NetworkTest extends TestCase
{
    /** @test */
    public function success(): void
    {
        $network = new Network($name = 'google', $identity = 'google-1');

        self::assertEquals($name, $network->getName());
        self::assertEquals($identity, $network->getIdentity());
    }

    /** @test */
    public function emptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Network($name = '', $identity = 'google-1');
    }

    /** @test */
    public function emptyIdentity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Network($name = 'google', $identity = '');
    }

    /** @test */
    public function equal(): void
    {
        $network = new Network($name = 'google', $identity = 'google-1');

        self::assertTrue($network->isEqual(new Network($name, $identity)));
        self::assertFalse($network->isEqual(new Network($name, 'google-2')));
        self::assertFalse($network->isEqual(new Network('fb', 'fb-1')));
    }
}