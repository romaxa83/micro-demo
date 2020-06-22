<?php

declare(strict_types=1);

use App\Auth;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Service\Tokenizer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Container\ContainerInterface;

return [
    UserRepository::class => function (ContainerInterface $container): UserRepository {
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        /** @var EntityRepository $repo */
        $repo = $em->getRepository(User::class);
        return new UserRepository($em, $repo);
    },

    Auth\Service\SignUpConfirmationSender::class => function (ContainerInterface $container){
        $mailer = $container->get(Swift_Mailer::class);
        $mailerConfig = $container->get('config')['mailer'];

        return new Auth\Service\SignUpConfirmationSender($mailer, $mailerConfig['from']);
    }
];