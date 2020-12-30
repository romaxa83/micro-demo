<?php

declare(strict_types=1);

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

return [
    ValidatorInterface::class => function () : ValidatorInterface {
        // регистрируем аннотации (в 3 версии доктрины должны поправить)
        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');

        return Validation::createValidatorBuilder()
            ->enableAnnotationMapping() // используем аннотации для правил валидации
            ->getValidator();
    },
];
