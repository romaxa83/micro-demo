<?php

declare(strict_types=1);

namespace Test\Unit\Http;

use App\Http\JsonResponse;
use PHPUnit\Framework\TestCase;

/**
 * аннотация указывает ,чтоб проверить покрытие только класса JsonResponse
 * и не учиывать другие ,вспомагательные классы, которые могут встретиться в тестах
 * @covers \App\Http\JsonResponse
 */
class JsonResponseTest extends TestCase
{
    /**
     * @test
     * @dataProvider getCases
     * @param mixed $source
     * @param mixed $expect
     */
    public function response($source, $expect): void
    {
        $response = new JsonResponse($source);

        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals($expect, $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    public function getCases(): array
    {
        $object = new \stdClass();
        $object->str = 'value';
        $object->int = 1;
        $object->none = null;

        $array = ['str' => 'value', 'int' => 1, 'none' => null];

        return [
            'null' => [null, 'null'],
            'empty' => ['', '""'],
            'number' => [12, 12],
            'string' => ['12', '"12"'],
            'object' => [$object, '{"str":"value","int":1,"none":null}'],
            'array' => [$array, '{"str":"value","int":1,"none":null}'],
        ];
    }

    /** @test */
    public function check_response_with_status_code(): void
    {
        $response = new JsonResponse(12,201);

        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals(201, $response->getStatusCode());
    }
}