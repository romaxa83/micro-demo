<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\Role;
use PHPUnit\Framework\TestCase;
use App\Auth\Test\Builder\UserBuilder;

class ChangeRoleTest extends TestCase
{
    /** @test */
    public function success(): void
    {
        $user = (new UserBuilder())
            ->build();

        $user->changeRole($role = new Role(Role::ADMIN));

        self::assertEquals($role, $user->getRole());
    }

    /** @test */
    public function already(): void
    {
        $user = (new UserBuilder())
            ->build();

        $this->expectExceptionMessage('Role is already same.');
        $user->changeRole($role = new Role(Role::USER));
    }
}
