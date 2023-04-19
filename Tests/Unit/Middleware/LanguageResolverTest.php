<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Unit\Middleware;

use DFAU\ToujouApi\Middleware\LanguageResolver;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class LanguageResolverTest extends UnitTestCase
{
    /** @var mixed|MockObject|Context */
    private $contextMock;

    /** @var LanguageResolver */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contextMock = $this->createMock(Context::class);
        $this->subject = new LanguageResolver($this->contextMock);
    }

    /**
     * @test
     */
    public function it_implements_correct_interface(): void
    {
        self::assertInstanceOf(MiddlewareInterface::class, $this->subject);
    }

    /**
     * @test
     */
    public function it_will_handle_unmodified_request_on_missing_site(): void
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestHandlerMock = $this->createMock(RequestHandlerInterface::class);

        $requestHandlerMock->expects(self::once())
            ->method('handle')
            ->with($requestMock);

        $this->subject->process($requestMock, $requestHandlerMock);
    }

    /**
     * @test
     */
    public function it_will_handle_unmodified_request_on_missing_language_header(): void
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestHandlerMock = $this->createMock(RequestHandlerInterface::class);
        $siteMock = $this->createMock(SiteInterface::class);

        $requestMock->expects(self::once())
            ->method('getAttribute')
            ->with('site')
            ->willReturn($siteMock);

        $requestHandlerMock->expects(self::once())
            ->method('handle')
            ->with($requestMock);

        $this->subject->process($requestMock, $requestHandlerMock);
    }

    /**
     * @test
     */
    public function it_will_handle_on_non_matching_language(): void
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestHandlerMock = $this->createMock(RequestHandlerInterface::class);
        $siteMock = $this->createMock(Site::class);
        $siteLanguageMock = $this->createMock(SiteLanguage::class);

        $siteLanguageMock
            ->expects(self::once())
            ->method('getHreflang')
            ->willReturn('de_DE');

        $siteLanguageMock
            ->expects(self::once())
            ->method('getTwoLetterIsoCode')
            ->willReturn('de');

        $siteMock->expects(self::once())
            ->method('getAllLanguages')
            ->willReturn([$siteLanguageMock]);

        $requestMock->expects(self::once())
            ->method('getAttribute')
            ->with('site')
            ->willReturn($siteMock);

        $requestMock->expects(self::once())
            ->method('getHeader')
            ->with('Accept-Language')
            ->willReturn(['de_B2B']);

        $requestHandlerMock->expects(self::once())
            ->method('handle')
            ->with($requestMock);

        $this->subject->process($requestMock, $requestHandlerMock);
    }

    /**
     * @test
     */
    public function it_will_set_language_by_href_lang(): void
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestHandlerMock = $this->createMock(RequestHandlerInterface::class);
        $siteMock = $this->createMock(Site::class);
        $siteLanguageMock = $this->createMock(SiteLanguage::class);

        $siteLanguageMock
            ->expects(self::once())
            ->method('getHreflang')
            ->willReturn('de_B2B');

        $siteLanguageMock
            ->expects(self::once())
            ->method('getTwoLetterIsoCode')
            ->willReturn('');

        $siteMock->expects(self::once())
            ->method('getAllLanguages')
            ->willReturn([$siteLanguageMock]);

        $requestMock->expects(self::once())
            ->method('getAttribute')
            ->with('site')
            ->willReturn($siteMock);

        $requestMock->expects(self::once())
            ->method('getHeader')
            ->with('Accept-Language')
            ->willReturn(['de_B2B']);

        $requestHandlerMock->expects(self::once())
            ->method('handle')
            ->with($requestMock);

        $this->contextMock->expects(self::once())
            ->method('setAspect')
            ->with('language', self::isInstanceOf(LanguageAspect::class));

        $requestMock->expects(self::exactly(2))
            ->method('withAttribute')
            ->withConsecutive(['context', $this->contextMock], ['language', $siteLanguageMock])
            ->willReturn($requestMock);

        $this->subject->process($requestMock, $requestHandlerMock);
    }

    /**
     * @test
     */
    public function it_will_set_language_by_two_letter_iso_code(): void
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestHandlerMock = $this->createMock(RequestHandlerInterface::class);
        $siteMock = $this->createMock(Site::class);
        $siteLanguageMock = $this->createMock(SiteLanguage::class);

        $siteLanguageMock
            ->expects(self::once())
            ->method('getHreflang')
            ->willReturn('');

        $siteLanguageMock
            ->expects(self::once())
            ->method('getTwoLetterIsoCode')
            ->willReturn('de_B2B');

        $siteMock->expects(self::once())
            ->method('getAllLanguages')
            ->willReturn([$siteLanguageMock]);

        $requestMock->expects(self::once())
            ->method('getAttribute')
            ->with('site')
            ->willReturn($siteMock);

        $requestMock->expects(self::once())
            ->method('getHeader')
            ->with('Accept-Language')
            ->willReturn(['de_B2B']);

        $requestHandlerMock->expects(self::once())
            ->method('handle')
            ->with($requestMock);

        $this->contextMock->expects(self::once())
            ->method('setAspect')
            ->with('language', self::isInstanceOf(LanguageAspect::class));

        $requestMock->expects(self::exactly(2))
            ->method('withAttribute')
            ->withConsecutive(['context', $this->contextMock], ['language', $siteLanguageMock])
            ->willReturn($requestMock);

        $this->subject->process($requestMock, $requestHandlerMock);
    }
}
