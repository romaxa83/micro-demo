<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Network
{
    /**
     * @ORM\Column(type="string", length=16)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private string $identity;

    /**
     * Network constructor.
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
        return $this->getName() === $network->getName() &&
            $this->getIdentity() === $network->getIdentity();
    }

    public function getName(): string
    {
        return $this->network;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }
}