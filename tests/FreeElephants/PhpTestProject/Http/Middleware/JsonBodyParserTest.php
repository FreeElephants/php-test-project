<?php

namespace FreeElephants\PhpTestProject\Http\Middleware;

use Helmich\Psr7Assert\Psr7Assertions;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JsonBodyParserTest extends TestCase
{

    use Psr7Assertions;

    public function testProcess()
    {
        $middleware = new JsonBodyParser();
        $request = new ServerRequest('POST', '/v1/books');
        $request->getBody()->write(<<<JSON
{
    "data": {
        "type": "books",
        "id": "1",
        "attributes": {
            "title": "foo"
        }
    }
}
JSON
        );

        $response = $middleware->process($request, $this->createRequestHandlerWithAssertions(function (ServerRequestInterface $request) {
            $this->assertEquals([
                'data' => [
                    'type'       => 'books',
                    'id'         => '1',
                    'attributes' => [
                        'title' => 'foo',
                    ],
                ],
            ], $request->getParsedBody());

            return new Response();
        }));

        $this->assertResponseHasStatus($response, 200);
        $this->assertSame(2, Assert::getCount());
    }

    protected function createRequestHandlerWithAssertions(callable $callable): RequestHandlerInterface
    {
        return new class ($callable) implements RequestHandlerInterface {
            private $callable;

            public function __construct(callable $callable)
            {
                $this->callable = $callable;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return call_user_func($this->callable, $request);
            }
        };
    }
}
