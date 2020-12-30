<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

return [
    ValidatorInterface::class => function (ContainerInterface $container) : ValidatorInterface {
        // регистрируем аннотации (в 3 версии доктрины должны поправить)
        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');

        // получаем транслятор для переводов, что прокинуть и в валидатор (ниже), для переводов сообщений
        $translator = $container->get(TranslatorInterface::class);

        return Validation::createValidatorBuilder()
            ->enableAnnotationMapping() // используем аннотации для правил валидации
            ->setTranslator($translator)
            ->setTranslationDomain('validators')
            ->getValidator();
    },
];
