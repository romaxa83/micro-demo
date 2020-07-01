<?php

declare(strict_types=1);

namespace Test\Functional\V1\Auth\SignUp;

use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestTest extends WebTestCase
{
    // запускаеться перед каждым тестом
    public function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            RequestFixture::class,
        ]);
    }

    /** @test */
    public function method(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/auth/signup'));

        self::assertEquals(405, $response->getStatusCode());
    }

    /** @test */
    public function success(): void
    {
        $this->mailer()->clear();

        $response = $this->app()->handle(self::json('POST', '/v1/auth/signup', [
            'email' => 'new-user@app.test',
            'password' => 'password'
        ]));

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals('', (string)$response->getBody());

        // проверяем отправку писем
        // @see https://github.com/mailhog/MailHog/blob/master/docs/APIv2/swagger-2.0.yaml
//        $json = file_get_contents('http://mailer:8025/api/v2/search?query=new-user@app.test&kind=to');
//        $data = Json::decode($json);
//
//        self::assertGreaterThan(0, $data['total']);

        self::assertTrue($this->mailer()->hasEmailSentTo('new-user@app.test'));
    }

    /** @test */
    public function existing(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/signup', [
            'email' => 'existing@app.test',
            'password' => 'password'
        ]));

        self::assertEquals(409, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());
        self::assertEquals([
            'message' => 'User already exists.'
        ], Json::decode($body));
    }

    /** @test */
    public function empty(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/signup', []));

        self::assertEquals(500, $response->getStatusCode());
    }

    /** @test */
    public function not_valid(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/signup', [
            'email' => 'not-email',
            'password' => ''
        ]));

        self::assertEquals(500, $response->getStatusCode());
    }
}