<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Middleware;


use DFAU\ToujouApi\Http\RouterFactory;
use FastRoute\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Router implements MiddlewareInterface
{

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $apiRouter = RouterFactory::createToujouApiRouter();
        $routeInfo = $apiRouter->dispatch($request->getMethod(), $request->getUri()->getPath());

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return GeneralUtility::makeInstance(Response::class)->withStatus(404);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                $response = GeneralUtility::makeInstance(Response::class)->withStatus(405);
                return $response->withStatus(405, $response->getReasonPhrase() . '. Allowed Methods: ' . implode(',', $allowedMethods));
                break;
            case Dispatcher::FOUND:
                $request = $request->withAttribute('handler', $routeInfo[1])->withAttribute('variables', $routeInfo[2]);
                break;
        }

        return $handler->handle($request);
    }
}
