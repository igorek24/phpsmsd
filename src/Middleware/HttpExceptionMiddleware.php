<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpException;

final class HttpExceptionMiddleware implements MiddlewareInterface
{

    /**
     * @var ResponseFactoryInterface
     */
    private ResponseFactoryInterface $responseFactory;
    private \Psr\Log\LoggerInterface $logger;
    private ?string $loggerUUID;


    public function __construct(ResponseFactoryInterface $responseFactory, LoggerFactory $logger)
    {
        $this->responseFactory = $responseFactory;
        $this->loggerUUID = $logger->getUUID();
        if (filter_var($_ENV['APP_DEV_MODE'], FILTER_VALIDATE_BOOLEAN)) {
            $this->logger = $logger
                ->addFileHandler('http_errors.log')
                ->addConsoleHandler()
                ->createLogger();
        } else {
            $this->logger = $logger
                ->addFileHandler('http_errors.log')
                ->createLogger();
        }

    }

    public function process(
        ServerRequestInterface  $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (HttpException $httpException) {
            // Handle the http exception here
            $statusCode = $httpException->getCode();
            $response = $this->responseFactory->createResponse()->withStatus($statusCode);
            $errorMessage = sprintf('%s %s', $statusCode, $response->getReasonPhrase());
            // Log the error message
            $QUERY_STRING = (isset($request->getServerParams()['QUERY_STRING']))
                ? $request->getServerParams()['QUERY_STRING'] : '';

            $PATH_INFO = (isset($request->getServerParams()['PATH_INFO']))
                ? $request->getServerParams()['PATH_INFO'] : '';

            $this->logger->error($errorMessage, [
                'REMOTE_ADDR' => $request->getServerParams()['REMOTE_ADDR'],
                'SERVER_NAME' => $request->getServerParams()['SERVER_NAME'],
                'SERVER_PORT' => $request->getServerParams()['SERVER_PORT'],
                'REQUEST_URI' => $request->getServerParams()['REQUEST_URI'],
                'REQUEST_METHOD' => $request->getServerParams()['REQUEST_METHOD'],
                'PATH_INFO' => $PATH_INFO,
                'QUERY_STRING' => $QUERY_STRING,
                'HTTP_HOST' => $request->getServerParams()['HTTP_HOST'],
                'HTTP_USER_AGENT' => $request->getServerParams()['HTTP_USER_AGENT'],
                'REQUEST_TIME' => $request->getServerParams()['REQUEST_TIME'],
            ]);


            $data = [
                'error' => $httpException->getMessage(),
                'error_code' => $httpException->getCode(),
                'error_description' => $httpException->getDescription(),
                'error_uri' => $_SERVER['REQUEST_URI'],
                'error_uri' => $_SERVER['REQUEST_URI'],
            ];
            $response = $response->withHeader('Content-Type', 'application/json');
            $response = $response->withStatus($httpException->getCode());
            $response->getBody()->write((string)json_encode($data, 0));
            return $response;

        }
    }
}