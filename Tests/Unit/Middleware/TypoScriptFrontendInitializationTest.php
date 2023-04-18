<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Unit\Middleware;

use DFAU\ToujouApi\Middleware\TypoScriptFrontendInitialization;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TypoScriptFrontendInitializationTest extends UnitTestCase
{
    /** @var TypoScriptFrontendInitialization */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $contextMock = $this->createMock(Context::class);
        $this->subject = new TypoScriptFrontendInitialization($contextMock);
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
    public function it_will_init_typoscript_frontend_on_process(): void
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestHandlerMock = $this->createMock(RequestHandlerInterface::class);
        $siteMock = $this->createMock(Site::class);
        $pageArgumentsMock = $this->createMock(PageArguments::class);
        $siteLanguageMock = $this->createMock(SiteLanguage::class);

        $frontendControllerMock = $this->createMock(TypoScriptFrontendController::class);
        GeneralUtility::addInstance(TypoScriptFrontendController::class, $frontendControllerMock);

        $requestMock
            ->method('getAttribute')
            ->withConsecutive(['site'], ['language'])
            ->willReturnOnConsecutiveCalls($siteMock, $pageArgumentsMock, $siteLanguageMock);

        $this->subject->process($requestMock, $requestHandlerMock);

        self::assertEquals($GLOBALS['TSFE'], $frontendControllerMock);
        self::assertEquals($GLOBALS['TYPO3_REQUEST'], $requestMock);
    }
}
