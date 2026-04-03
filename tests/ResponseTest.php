<?php

namespace Jinx\SapB1\Tests;

use PHPUnit\Framework\TestCase;
use Jinx\SapB1\Response;

class ResponseTest extends TestCase
{
    public function testOkStatus()
    {
        $response = new Response(200, [], [], '{"status": "ok"}');
        $this->assertTrue($response->isOk());
    }

    public function testErrorStatus()
    {
        $response = new Response(500, [], [], 'Error');
        $this->assertFalse($response->isOk());
    }

    public function testJsonParsing()
    {
        $body = json_encode(['foo' => 'bar']);
        $response = new Response(200, [], [], $body);
        $this->assertEquals('bar', $response->getJson()->foo);
    }

    public function testErrorMessageParsing()
    {
        $body = json_encode([
            'error' => [
                'message' => [
                    'value' => 'Invalid ItemCode'
                ]
            ]
        ]);
        $response = new Response(400, [], [], $body);
        $this->assertEquals('Invalid ItemCode', $response->getErrorMessage());
    }
}
