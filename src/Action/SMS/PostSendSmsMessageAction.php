<?php

namespace App\Action\SMS;

use App\Domain\SMS\Service\SMSMessageSender;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostSendSmsMessageAction
{
    private SMSMessageSender $SMSMessageSender;

    public function __construct(SMSMessageSender $SMSMessageSender, Responder $responder)
    {
        $this->responder = $responder;
        $this->SMSMessageSender = $SMSMessageSender;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface      $response,
        array                  $queryParams
    ): ResponseInterface
    {
        $bodyContent = json_decode($request->getBody()->getContents());
        return $this->responder->withJson($response, $this->SMSMessageSender->sendSmsMessage($bodyContent));
    }
}