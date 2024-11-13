<?php declare(strict_types=1);

use App\Middleware\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/', \App\Action\HomeAction::class)->setName('home');
        $group->get('/status', \App\Action\StatusAction::class)->setName('status');
    });
};
