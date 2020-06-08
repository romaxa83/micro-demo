<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

interface UserRepository
{
    public function hasByEmail(Email $email): bool;
    public function hasByNetwork(NetworkIdentity $identity): bool;
    public function add(User $user): void;
    public function findByConfirmToken(string $token): ?User;
    /**
     * @param Id id
     * @return User
     * @throws \DomainException
     */
    public function getById(Id $id): User;
}
