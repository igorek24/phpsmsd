<?php

namespace App\Action\SMS;

use App\Domain\SMS\Service\SentSMSMessagesGetter;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetSentSmsMessagesAction
{
    /**
     * @var Responder
     */
    private Responder $responder;
    private SentSMSMessagesGetter $SMSGetter;

    public function __construct(SentSMSMessagesGetter $SMSGetter, Responder $responder)
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
        $order = !empty($queryParams['order']) ? $queryParams['order'] : 'asc';
        $limit = !empty($queryParams['limit']) ? $queryParams['limit'] : 100;
        return $this->responder->withJson($response, $this->SMSGetter->getSentSmsMessages($order, $limit));
    }
}