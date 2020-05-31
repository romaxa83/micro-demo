<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Service;

use App\Auth\Service\Tokenizer;
use PHPUnit\Framework\TestCase;

/** @covers ExpiringTokenizer */
class TokenizerTest extends TestCase
{
    // проверяем работу интервала нашего токенайзера

    /** @test */
    public function success():void
    {
        // интервал один час
        $interval = new \DateInterval('PT1H');
        $date = new \DateTimeImmutable('+1 day');

        $tokenizer = new Tokenizer($interval);

        $token = $tokenizer->generate($date);
        // проверяем что чения токен один час
        self::assertEquals($date->add($interval), $token->getExpires());
    }
}