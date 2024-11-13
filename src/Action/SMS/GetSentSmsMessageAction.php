<?php

namespace App\Action\SMS;

use App\Domain\SMS\Service\SentSMSMessageGetter;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetSentSmsMessageAction
{
    /**
     * @var Responder
     */
    private Responder $responder;
    private SentSMSMessageGetter $SMSGetter;

    public function __construct(SentSMSMessageGetter $SMSGetter, Responder $responder)
    {
        $this->responder = $responder;
        $this->SMSGetter = $SMSGetter;
    }


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface      $response,
        array                  $queryParams
    ): ResponseInterface
    {

        return $this->responder->withJson($response, $this->SMSGetter->getSentSmsMessage($queryParams['id']));
    }
}