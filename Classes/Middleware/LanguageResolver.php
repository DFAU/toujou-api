<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspectFactory;
use TYPO3\CMS\Core\Site\Entity\Site;

class LanguageResolver implements MiddlewareInterface
{
    /** @var Context */
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $this->getLanguageAwareRequest($request);
        return $handler->handle($request);
    }

    protected function getLanguageAwareRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        /** @var Site $site */
        $site = $request->getAttribute('site');

        if (null === $site) {
            return $request;
        }

        $acceptLanguage = $request->getHeader('Accept-Language');
        if (empty($acceptLanguage)) {
            return $request;
        }

        $acceptLanguage = \reset($acceptLanguage);
        $usedSiteLanguage = null;
        foreach ($site->getAllLanguages() as $language) {
            $languageKeys = [$language->getHreflang(), $language->getTwoLetterIsoCode()];

            if (\in_array($acceptLanguage, $languageKeys, true)) {
                $usedSiteLanguage = $language;
                break;
            }
        }
        if (null === $usedSiteLanguage) {
            return $request;
        }

        $this->context->setAspect('language', LanguageAspectFactory::createFromSiteLanguage($usedSiteLanguage));

        return $request
            ->withAttribute('context', $this->context)
            ->withAttribute('language', $usedSiteLanguage);
    }
}
