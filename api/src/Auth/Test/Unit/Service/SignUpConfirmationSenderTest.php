<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Service\SignUpConfirmationSender;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Twig\Environment;

/** @covers SignUpConfirmationSender */
class SignUpConfirmationSenderTest extends TestCase
{
    /** @test */
    public function success():void
    {
        $to = new Email('user@app.test');
        $token = new Token(Uuid::uuid4()->toString(), new \DateTimeImmutable());
        $confirmUrl = 'http://test/join/confirm?token=' . $token->getValue();

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())->method('render')->with(
            $this->equalTo('auth/signup/confirm.html.twig'),
            $this->equalTo(['token' => $token->getValue()]),
            )->willReturn($body = '<a href="' . $confirmUrl . '">' . $confirmUrl . '</a>');

        // создаем mock для sender
        $mailer = $this->createMock(\Swift_Mailer::class);
        // проверям что метод 'send' будет вызван один раз
        $mailer->expects($this->once())->method('send')
            ->willReturnCallback(static function (\Swift_Message $message) use ($to, $body) :int {
                self::assertEquals([$to->getValue() => null], $message->getTo());
                self::assertEquals('Sign Up Confirmation', $message->getSubject());
                self::assertEquals($body, $message->getBody());
                self::assertEquals('text/html', $message->getBodyContentType());
                return 1;
            });

        $sender = new SignUpConfirmationSender($mailer, $twig);

        $sender->send($to, $token);
    }

    /** @test */
    public function error():void
    {
        $to = new Email('user@app.test');
        $token = new Token(Uuid::uuid4()->toString(), new \DateTimeImmutable());
        $confirmUrl = 'http://test/join/confirm?token=' . $token->getValue();

        $twig = $this->createStub(Environment::class);
        $twig->method('render')->willReturn('<a href="' . $confirmUrl . '">' . $confirmUrl . '</a>');

        $mailer = $this->createStub(\Swift_Mailer::class);
        $mailer->method('send')->willReturn(0);

        $sender = new SignUpConfirmationSender($mailer, $twig);

        $this->expectException(\RuntimeException::class);
        $sender->send($to, $token);
    }
}