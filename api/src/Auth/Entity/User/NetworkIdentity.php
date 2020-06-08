<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class NetworkIdentity
{
    private string $network;
    private string $identity;

    /**
     * NetworkIdentity constructor.
     * @param string $network
     * @param string $identity
     */
    public function __construct(string $network, string $identity)
    {
        Assert::notEmpty($network);
        Assert::notEmpty($identity);
        $this->network = mb_strtolower($network);
        $this->identity = mb_strtolower($identity);
    }

    public function isEqual(self $network): bool
    {
        return $this->getNetwork() === $network->getNetwork() &&
            $this->getIdentity() === $network->getIdentity();
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }
}