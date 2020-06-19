<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repository;

    /**
     * UserRepository constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        /** @var EntityRepository $repository */
        $repository = $em->getRepository(User::class);
        $this->repository = $repository;
    }

    public function hasByEmail(Email $email): bool
    {
        return $this->repository->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.email = :email')
            ->setParameter(':email', $email->getValue())
            ->getQuery()->getSingleScalarResult() > 0;
    }

    public function hasByNetwork(Network $network): bool
    {
        return $this->repository->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->innerJoin('t.networks', 'n')
                ->andWhere('n.network = :name and n.identity = :identity')
                ->setParameter(':name', $network->getName())
                ->setParameter(':identity', $network->getIdentity())
                ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param string $token
     * @return User|object|null
     */
    public function findByConfirmToken(string $token): ?User
    {
        return $this->repository->findOneBy(['signUpConfirmToken.value' => $token]);
    }

    /**
     * @param string $token
     * @return User|object|null
     */
    public function findByNewEmailToken(string $token): ?User
    {
        return $this->repository->findOneBy(['newEmailToken.value' => $token]);
    }

    /**
     * @param string $token
     * @return User|object|null
     */
    public function findByPasswordResetToke(string $token): ?User
    {
        return $this->repository->findOneBy(['passwordResetToken.value' => $token]);
    }

    public function getById(Id $id): User
    {
        if(!$user = $this->repository->find($id->getValue())){
            throw new \DomainException('User is not found.');
        }
        /** @var $user User */
        return $user;
    }

    public function getByEmail(Email $email): User
    {
        if(!$user = $this->repository->findOneBy(['email' => $email->getValue()])){
            throw new \DomainException('User is not found.');
        }
        /** @var $user User */
        return $user;
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }

    public function remove(User $user): void
    {
        $this->em->remove($user);
    }
}
