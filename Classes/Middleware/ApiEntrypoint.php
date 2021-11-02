<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Middleware;

use DFAU\ToujouApi\ErrorFormatter\JsonApiFormatter;
use DFAU\ToujouApi\Http\RequestHandler;
use Middlewares\ErrorFormatter;
use Middlewares\ErrorHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspectFactory;
use TYPO3\CMS\Core\Http\MiddlewareDispatcher;
use TYPO3\CMS\Core\Http\MiddlewareStackResolver;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Aspect\PreviewAspect;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class ApiEntrypoint implements MiddlewareInterface
{
    public const API_V1_ENDPOINT = '/_api/v1/';

    /** @var Context */
    protected $context;

    /** @var ContainerInterface */
    protected $container;

    /** @var MiddlewareStackResolver */
    protected $middlewareStackResolver;

    /** @var RequestHandler */
    protected $requestHandler;

    public function __construct(Context $context, ContainerInterface $container, MiddlewareStackResolver $middlewareStackResolver, RequestHandler $requestHandler)
    {
        $this->context = $context;
        $this->container = $container;
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
        $apiPathPrefix = $site instanceof Site ? \ltrim($site->getAttribute('toujouApiPathPrefix') ?? '', '/ ') : null;

        if (!empty($apiPathPrefix) && GeneralUtility::isFirstPartOfStr($request->getUri()->getPath(), '/' . $apiPathPrefix)) {
            // @TODO
            // add lang handling
            // PageRouter l:246
            // request neu setzen withAttribute()
            // vllt: SiteResolver l:60 -- Locales::

            // ResourceControllerFactory:: --> context object mitgeben
            // createToujouApiRouter

            // request->withAttribute(context -> $context)

            $request = $this->generateContext($request, $site);

            $tsfe = $this->getTyposcriptFrontendController($request);
            $tsfe->determineId();

            $request = $request->withUri($request->getUri()->withPath('/' . \substr($request->getUri()->getPath(), \strlen('/' . $apiPathPrefix))));
            $middlewareDispatcher = $this->createMiddlewareDispatcher();
            return $middlewareDispatcher->handle($request);
        }

        return $handler->handle($request);
    }

    protected function generateContext(ServerRequestInterface $request, SiteInterface $site): ServerRequestInterface
    {
        $lang = $request->getHeader('accept-language') ?? null;
        if (null === $lang) {
            return $request;
        }

        $lang = \reset($lang);
        $usedSiteLanguage = null;
        foreach ($site->getAllLanguages() as $language) {
            if ($language->getHreflang() === $lang) {
                $usedSiteLanguage = $language;
            } elseif ($language->getTwoLetterIsoCode() === $lang) {
                $usedSiteLanguage = $language;
            }
        }
        if (null === $usedSiteLanguage) {
            return $request;
        }

        $context = GeneralUtility::makeInstance(Context::class);
        $context->setAspect('language', LanguageAspectFactory::createFromSiteLanguage($usedSiteLanguage));
        return $request
            ->withAttribute('context', $context)
            ->withAttribute('language', $usedSiteLanguage);
    }

    protected function initTyposcriptFrontendController(TypoScriptFrontendController $tsfe)
    {
        $tsfe->determineId();
    }

    protected function createMiddlewareDispatcher(): MiddlewareDispatcher
    {
        $middlewares = $this->middlewareStackResolver->resolve('toujou_api');

        $errorHandler = new ErrorHandler([
            new JsonApiFormatter(),
            new ErrorFormatter\XmlFormatter(),
        ]);
        $errorHandler->defaultFormatter(new ErrorFormatter\PlainFormatter());

        $middlewares[] = $errorHandler;

        return new MiddlewareDispatcher(
            $this->requestHandler,
            $middlewares
        );
    }

    protected function getTyposcriptFrontendController($request): ?TypoScriptFrontendController
    {
        $GLOBALS['TYPO3_REQUEST'] = $request;
        /** @var Site $site */
        $site = $request->getAttribute('site', null);
        $pageArguments = $request->getAttribute('routing', null);
        $this->context->setAspect('frontend.preview', GeneralUtility::makeInstance(PreviewAspect::class));

        $controller = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            $this->context,
            $site,
            $request->getAttribute('language', $site->getDefaultLanguage()),
            $pageArguments,
            $request->getAttribute('frontend.user', null)
        );

        return $GLOBALS['TSFE'] = $controller;
    }
}
