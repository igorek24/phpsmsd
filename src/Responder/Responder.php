<?php declare(strict_types=1);

namespace App\Responder;

use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use \Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;
use function http_build_query;

/**
 * A generic responder.
 */
final class Responder
{

//    private RouteParserInterface $routeParser;

    private ResponseFactoryInterface $responseFactory;
    private RouteParserInterface $routeParser;
    private ?string $loggerUUID;

    /**
     * The constructor.
     *
     * @param ResponseFactoryInterface $responseFactory The response factory
     */
    public function __construct(
        App                      $app,
        ResponseFactoryInterface $responseFactory,
        LoggerFactory            $logger
    )
    {
        $this->app = $app;
        $this->responseFactory = $responseFactory;
        $this->routeParser = $app->getRouteCollector()->getRouteParser();
        $this->loggerUUID = $logger->getUUID();
    }

    /**
     * Create a new response.
     *
     * @return ResponseInterface The response
     */
    public function createResponse(
        ResponseInterface $response,
        string            $template,
        array             $data = []): ResponseInterface
    {
        return $this->twig->render($response, $template, $data);
    }

    public function createNotFoundResponse(ResponseInterface $response, $public = false): ResponseInterface
    {
        $viewData = [
            'page_title' => '404 Not Found',
            'error_code' => 404,
            'error_message' => "Page not Found!",
            'error_description' => "We're fairly sure that page used to be here, but seems to have gone missing. We do apologise on it's behalf.",
            'uuid' => $this->loggerUUID
        ];
        $public = $public ? 'public_' : null;

        return $this->twig->render($response, "errors/http_{$public}errors.twig", $viewData)->withStatus(404);
    }

    /**
     * Creates a redirect for the given url / route name.
     *
     * This method prepares the response object to return an HTTP Redirect
     * response to the client.
     *
     * @param ResponseInterface $response The response
     * @param string $destination The redirect destination (url or route name)
     * @param array $queryParams Optional query string parameters
     *
     * @return ResponseInterface The response
     */
    public function withRedirect(
        ResponseInterface $response,
        string            $destination,
        array             $queryParams = []
    ): ResponseInterface
    {
        if ($queryParams) {
            $destination = sprintf('%s?%s', $destination, http_build_query($queryParams));
        }

        return $response->withStatus(302)->withHeader('Location', $destination);
    }

    /**
     * Creates a redirect for the given url / route name.
     *
     * This method prepares the response object to return an HTTP Redirect
     * response to the client.
     *
     * @param ResponseInterface $response The response
     * @param string $routeName The redirect route name
     * @param array $data Named argument replacement data
     * @param array $queryParams Optional query string parameters
     *
     * @return ResponseInterface The response
     */
    public function withRedirectFor(
        ResponseInterface $response,
        string            $routeName,
        string            $htmlIdElement = '',
        array             $data = [],
        array             $queryParams = []
    ): ResponseInterface
    {
        return $this->withRedirect(
            $response,
            $this->routeParser->urlFor($routeName, $data, $queryParams) . '#' . $htmlIdElement
        );
    }

    /**
     * Write JSON to the response body.
     *
     * This method prepares the response object to return an HTTP JSON
     * response to the client.
     *
     * @param ResponseInterface $response The response
     * @param mixed|null $data The data
     * @param int $options Json encoding options
     *
     * @return ResponseInterface The response
     */
    public function withJson(
        ResponseInterface $response,
        mixed             $data = null,
        int               $options = 0
    ): ResponseInterface
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        if (isset($data->error)) {
            $response = $response->withStatus($data->error);
        }
//        $data['uri'] = $_SERVER['REQUEST_URI'];
        $response->getBody()->write((string)json_encode($data, $options));

        return $response;
    }
}