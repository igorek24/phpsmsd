<?php declare(strict_types=1);


namespace App\Action;

use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class StatusAction
{

    /**
     * @var Responder
     */
    private Responder $responder;


    public function __construct(Responder $responder)
    {
        $this->responder = $responder;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface      $response
    ): ResponseInterface
    {
        $data =[
            'server_status' => 'OK',
        ];
        return $this->responder->withJson($response, $data);
    }
}