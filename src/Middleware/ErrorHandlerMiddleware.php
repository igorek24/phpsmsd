<?php

declare(strict_types=1);

namespace App\Middleware;


use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;


final class ErrorHandlerMiddleware implements MiddlewareInterface
{

    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
//        $token = explode(' ', (string)$request->getHeaderLine('Authorization'))[1] ?? '';
//        if ($_SESSION['test' == true]) {
//            return $this->responseFactory->createResponse()
//                ->withHeader('Content-Type', 'application/json')
//                ->withStatus(401, 'Unauthorized');
//        }
        return $handler->handle($request);

    }
}