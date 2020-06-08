<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\NetworkIdentity;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/** @covers NetworkIdentity */
class NetworkIdentityTest extends TestCase
{
    /** @test */
    public function success(): void
    {
        $network = new NetworkIdentity($name = 'google', $identity = 'google-1');

        self::assertEquals($name, $network->getNetwork());
        self::assertEquals($identity, $network->getIdentity());
    }

    /** @test */
    public function emptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new NetworkIdentity($name = '', $identity = 'google-1');
    }

    /** @test */
    public function emptyIdentity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new NetworkIdentity($name = 'google', $identity = '');
    }

    /** @test */
    public function equal(): void
    {
        $network = new NetworkIdentity($name = 'google', $identity = 'google-1');

        self::assertTrue($network->isEqual(new NetworkIdentity($name, $identity)));
        self::assertFalse($network->isEqual(new NetworkIdentity($name, 'google-2')));
        self::assertFalse($network->isEqual(new NetworkIdentity('fb', 'fb-1')));
    }
}