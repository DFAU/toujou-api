<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * TYPO3's ServerRequestFactory is broken in a way, that it attempts to parse every PUT/PATCH/DELETE request as form-data.
 * In toujou API we won't be using form data, but reset it to empty.
 */
class ParsedBodyReset implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!empty($request->getParsedBody()) && in_array($request->getMethod(), ['PUT', 'PATCH', 'DELETE'])) {
            $contentType = $request->hasHeader('content-type') ? $request->getHeader('content-type')[0] : 'application/x-www-form-urlencoded';
            if (!in_array($contentType, ['application/x-www-form-urlencoded', 'multipart/form-data'])) {
                $request = $request->withParsedBody(null);
            }
        }
        return $handler->handle($request);
    }
}
