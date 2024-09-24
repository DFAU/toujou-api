<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Aspect\PreviewAspect;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class TypoScriptFrontendInitialization implements MiddlewareInterface
{
    /** @var Context */
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->getTyposcriptFrontendController($request);

        return $handler->handle($request);
    }

    protected function getTypoScriptFrontendController($request): ?TypoScriptFrontendController
    {
        $GLOBALS['TYPO3_REQUEST'] = $request;
        /** @var Site $site */
        $site = $request->getAttribute('site', null);
        $pageArguments = new PageArguments(0, '0', []);
        $this->context->setAspect('frontend.preview', GeneralUtility::makeInstance(PreviewAspect::class));

        /** @var TypoScriptFrontendController $controller */
        $controller = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            $this->context,
            $site,
            $request->getAttribute('language', $site->getDefaultLanguage()),
            $pageArguments,
            $request->getAttribute('frontend.user', null)
        );

        $controller->sys_page = GeneralUtility::makeInstance(PageRepository::class);

        return $GLOBALS['TSFE'] = $controller;
    }
}
