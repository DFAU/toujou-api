<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class CheckBeUserAuthorization implements MiddlewareInterface
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
        $tsfe = $this->getTypoScriptFrontendController();
        if (!$tsfe->getContext()->getPropertyFromAspect('backend.user', 'isLoggedIn', false)) {
            return new Response('php://temp', '401');
        }

        return $handler->handle($request);
    }

    protected function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
