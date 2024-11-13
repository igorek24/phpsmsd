<?php declare(strict_types=1);

//use Middlewares\TrailingSlash;
use App\Middleware\ErrorHandlerMiddleware;
use App\Middleware\HttpExceptionMiddleware;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use Slim\Views\TwigMiddleware;

return function (App $app) {

    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();


    // Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();

    $app->add(ErrorHandlerMiddleware::class);
    // Catch Http errors
    $app->add(HttpExceptionMiddleware::class);
    // Catch exceptions and errors

    $app->add(ErrorMiddleware::class);

};