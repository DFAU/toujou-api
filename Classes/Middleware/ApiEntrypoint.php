<?php
declare(strict_types = 1);
namespace DFAU\ToujouApi\Middleware;


use DFAU\ToujouApi\Http\RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Http\MiddlewareDispatcher;
use TYPO3\CMS\Core\Http\MiddlewareStackResolver;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Service\DependencyOrderingService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class ApiEntrypoint implements MiddlewareInterface
{

    const API_V1_ENDPOINT = '/_api/v1/';

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
        $apiPathPrefix = $site instanceof Site ? ltrim($site->getAttribute('toujouApiPathPrefix') ?? '', '/ ') : null;

        if (!empty($apiPathPrefix) && GeneralUtility::isFirstPartOfStr($request->getUri()->getPath(), '/' . $apiPathPrefix)) {
            $tsfe = $this->getTyposcriptFrontendController();

            if (!$tsfe->isBackendUserLoggedIn()) {
                return new \TYPO3\CMS\Core\Http\Response('php://temp', '401');
            }

            $tsfe->determineId();

            $request = $request->withUri($request->getUri()->withPath('/' . substr($request->getUri()->getPath(), strlen('/' . $apiPathPrefix))));
            $middlewareDispatcher = $this->createMiddlewareDispatcher();
            return $middlewareDispatcher->handle($request);

        }

        return $handler->handle($request);
    }

    protected function initTyposcriptFrontendController(TypoScriptFrontendController $tsfe)
    {
        $tsfe->determineId();
    }


    /**
     * @return MiddlewareDispatcher
     */
    protected function createMiddlewareDispatcher(): MiddlewareDispatcher
    {
        $resolver = new MiddlewareStackResolver(
            GeneralUtility::makeInstance(PackageManager::class),
            GeneralUtility::makeInstance(DependencyOrderingService::class),
            GeneralUtility::makeInstance(CacheManager::class)->getCache('cache_core')
        );

        return new MiddlewareDispatcher(
            GeneralUtility::makeInstance(RequestHandler::class),
            $resolver->resolve('toujou_api')
        );
    }

    protected function getTyposcriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
