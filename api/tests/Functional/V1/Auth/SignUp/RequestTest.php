<?php

declare(strict_types=1);

namespace Test\Functional\V1\Auth\SignUp;

use Test\Functional\Json;
use Test\Functional\WebTestCase;

class RequestTest extends WebTestCase
{
    /** @test */
    public function method(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/auth/signup'));

        self::assertEquals(405, $response->getStatusCode());
    }

    /** @test */
    public function success(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/signup', [
            'email' => 'new-user@app.test',
            'password' => 'password'
        ]));
//        dd($response);

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals('', (string)$response->getBody());
    }

    /** @test */
    public function existing(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/signup', [
            'email' => 'user@app.test',
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