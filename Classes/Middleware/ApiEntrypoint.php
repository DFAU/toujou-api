<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Middleware;

use DFAU\ToujouApi\ErrorFormatter\JsonApiFormatter;
use DFAU\ToujouApi\Http\RequestHandler;
use Middlewares\ErrorFormatter\PlainFormatter;
use Middlewares\ErrorFormatter\XmlFormatter;
use Middlewares\ErrorHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\MiddlewareDispatcher;
use TYPO3\CMS\Core\Http\MiddlewareStackResolver;
use TYPO3\CMS\Core\Site\Entity\Site;

class ApiEntrypoint implements MiddlewareInterface
{
    public const API_V1_ENDPOINT = '/_api/v1/';

    /** @var MiddlewareStackResolver */
    protected $middlewareStackResolver;

    /** @var RequestHandler */
    protected $requestHandler;

    public function __construct(MiddlewareStackResolver $middlewareStackResolver, RequestHandler $requestHandler)
    {
        $this->middlewareStackResolver = $middlewareStackResolver;
        $this->requestHandler = $requestHandler;
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Route request through own middleware chain to the api request handler
        $site = $request ? $request->getAttribute('site') : null;

        try {
            $apiPathPrefix = $site instanceof Site ? \ltrim($site->getAttribute('toujouApiPathPrefix') ?? '', '/ ') : null;
        } catch (\InvalidArgumentException) {
            $apiPathPrefix = null;
        }

        if (!empty($apiPathPrefix) && \str_starts_with($request->getUri()->getPath(), '/' . $apiPathPrefix)) {
            $request = $request->withUri($request->getUri()->withPath('/' . \substr($request->getUri()->getPath(), \strlen('/' . $apiPathPrefix))));
            $middlewareDispatcher = $this->createMiddlewareDispatcher();

            return $middlewareDispatcher->handle($request);
        }

        return $handler->handle($request);
    }

    protected function createMiddlewareDispatcher(): MiddlewareDispatcher
    {
        $middlewares = $this->middlewareStackResolver->resolve('toujou_api');

        $errorHandler = new ErrorHandler([
            new PlainFormatter(),
            new JsonApiFormatter(),
            new XmlFormatter(),
        ]);

        $middlewares[] = $errorHandler;

        return new MiddlewareDispatcher(
            $this->requestHandler,
            $middlewares
        );
    }
}
