<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Unit\Database\Query\Restriction;

use PHPUnit\Framework\MockObject\MockObject;
use DFAU\ToujouApi\Database\Query\Restriction\LanguageRestriction;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class LanguageRestrictionTest extends UnitTestCase
{
    /** @var LanguageRestriction */
    private $subject;

    /** @var MockObject|LanguageAspect */
    private $languageAspect;

    protected function setUp(): void
    {
        parent::setUp();
        $contextMock = $this->createMock(Context::class);
        $this->languageAspect = $this->createMock(LanguageAspect::class);

        $contextMock->method('getAspect')
            ->with('language')
            ->willReturn($this->languageAspect);

        $this->subject = new LanguageRestriction($contextMock);
    }

    /**
     * @test
     */
    public function it_implements_correct_interface(): void
    {
        self::assertInstanceOf(QueryRestrictionInterface::class, $this->subject);
    }

    /**
     * @test
     */
    public function it_will_return_language_restriction_for_tables_with_translation(): void
    {
        $this->markTestSkipped('Due to copy pasted \TYPO3\CMS\Core\Domain\Repository\PageRepository::getRecordOverlay sql this test is marked as skipped');

        $GLOBALS['TCA']['tt_content']['ctrl']['languageField'] = 'sys_lang';

        $this->languageAspect->method('getContentId')
            ->willReturn(13);

        $expressionBuilderMock = $this->createMock(ExpressionBuilder::class);

        $compositeExpression = $this->createMock(CompositeExpression::class);

        $expressionBuilderMock->expects(self::once())
            ->method('in')
            ->with('t.sys_lang', [13, -1])
            ->willReturn('t.sys_lang IN (13, -1)');

        $expressionBuilderMock->expects(self::once())
            ->method('andX')
            ->with('t.sys_lang IN (13, -1)')
            ->willReturn($compositeExpression);

        $result = $this->subject->buildExpression(['t' => 'tt_content'], $expressionBuilderMock);

        self::assertEquals($compositeExpression, $result);
    }

    /**
     * @test
     */
    public function it_wont_return_language_restriction_for_tables_without_translation(): void
    {
        $this->languageAspect->method('getContentId')
            ->willReturn(13);

        $expressionBuilderMock = $this->createMock(ExpressionBuilder::class);

        $compositeExpression = $this->createMock(CompositeExpression::class);

        $expressionBuilderMock->expects(self::never())
            ->method('in');

        $expressionBuilderMock->expects(self::once())
            ->method('andX')
            ->with()
            ->willReturn($compositeExpression);

        $result = $this->subject->buildExpression(['t' => 'tt_record'], $expressionBuilderMock);

        self::assertEquals($compositeExpression, $result);
    }
}
