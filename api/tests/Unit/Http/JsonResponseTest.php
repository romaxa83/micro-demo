<?php

declare(strict_types=1);

namespace Test\Unit\Http;

use App\Http\JsonResponse;
use PHPUnit\Framework\TestCase;

class JsonResponseTest extends TestCase
{
    /** @test */
    public function check_int(): void
    {
        $response = new JsonResponse(12);

        self::assertEquals('12', $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function check_int_with_status_code(): void
    {
        $response = new JsonResponse(12,201);

        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals('12', $response->getBody()->getContents());
        self::assertEquals(201, $response->getStatusCode());
    }

    /** @test */
    public function check_null(): void
    {
        $response = new JsonResponse(null);

        self::assertEquals('null', $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function check_string(): void
    {
        $response = new JsonResponse('string');

        self::assertEquals('"string"', $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function check_object(): void
    {
        $object = new \stdClass();
        $object->str = 'value';
        $object->int = 1;
        $object->none = null;

        $response = new JsonResponse($object);

        self::assertEquals('{"str":"value","int":1,"none":null}', $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function check_array(): void
    {
        $array = ['str' => 'value', 'int' => 1, 'none' => null];

        $response = new JsonResponse($array);

        self::assertEquals('{"str":"value","int":1,"none":null}', $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }
}