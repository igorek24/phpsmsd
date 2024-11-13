<?php

namespace App\Action\SMS;

use App\Domain\SMS\Service\InboxSMSMessagesGetter;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetInboxSmsMessagesAction
{
    /**
     * @var Responder
     */
    private Responder $responder;
    private InboxSMSMessagesGetter $SMSGetter;

    public function __construct(InboxSMSMessagesGetter $SMSGetter, Responder $responder)
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

//        return $this->responder->withJson($response, $this->SMSGetter->getInboxSmsMessages($queryParams['id']));
        $order = !empty($queryParams['order']) ? $queryParams['order'] : 'asc';
        $limit = !empty($queryParams['limit']) ? $queryParams['limit'] : 100;
        return $this->responder->withJson($response, $this->SMSGetter->getInboxSmsMessages($order, $limit));
    }
}