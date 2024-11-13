<?php declare(strict_types=1);

use App\Middleware\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/api/v1', function (RouteCollectorProxy $group) {
        $group->get('/sms/sent/messages/', \App\Action\SMS\GetSentSmsMessagesAction::class)->setName('sms.sent.messages');
        $group->get('/sms/sent/messages/{order:asc|desc}/', \App\Action\SMS\GetSentSmsMessagesAction::class)->setName('sms.sent.messages.ordered');
        $group->get('/sms/sent/messages/{limit:[0-9]+}/', \App\Action\SMS\GetSentSmsMessagesAction::class)->setName('sms.sent.messages.limited');
        $group->get('/sms/sent/messages/{limit:[0-9]+}/{order:asc|desc}/', \App\Action\SMS\GetSentSmsMessagesAction::class)->setName('sms.sent.messages.limited.ordered');
        $group->get('/sms/sent/message/{id:[0-9]+}/', \App\Action\SMS\GetSentSmsMessageAction::class)->setName('sms.sent.message');

        $group->post('/sms/send/', \App\Action\SMS\PostSendSmsMessageAction::class)->setName('sms.send');

        $group->get('/sms/inbox/messages/', \App\Action\SMS\GetInboxSmsMessagesAction::class)->setName('sms.inbox.messages');
        $group->get('/sms/inbox/messages/{limit:[0-9]+}/', \App\Action\SMS\GetInboxSmsMessagesAction::class)->setName('sms.inbox.messages.limited');
        $group->get('/sms/inbox/messages/{order:asc|desc}/', \App\Action\SMS\GetInboxSmsMessagesAction::class)->setName('sms.inbox.messages.ordered');
        $group->get('/sms/inbox/messages/{limit:[0-9]+}/{order:asc|desc}/', \App\Action\SMS\GetInboxSmsMessagesAction::class)->setName('sms.inbox.messages.limited.ordered');
    });
};
