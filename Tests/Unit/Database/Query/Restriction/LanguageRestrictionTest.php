<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Unit\Database\Query\Restriction;

use DFAU\ToujouApi\Database\Query\Restriction\LanguageRestriction;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class LanguageRestrictionTest extends UnitTestCase
{
    /** @var LanguageRestriction */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $contextMock = $this->createMock(Context::class);
        $languageAspect = new LanguageAspect(0, 13);

        $contextMock->method('getAspect')
            ->with('language')
            ->willReturn($languageAspect);

        $this->subject = new LanguageRestriction($contextMock);
    }

    #[Test]
    public function it_implements_correct_interface(): void
    {
        $this->assertInstanceOf(QueryRestrictionInterface::class, $this->subject);
    }

    #[Test]
    public function it_will_return_language_restriction_for_tables_with_translation(): void
    {
        $this->markTestSkipped('Due to copy pasted \TYPO3\CMS\Core\Domain\Repository\PageRepository::getRecordOverlay sql this test is marked as skipped');

        $GLOBALS['TCA']['tt_content']['ctrl']['languageField'] = 'sys_lang';

        $expressionBuilderMock = $this->createMock(ExpressionBuilder::class);

        $compositeExpression = $this->createStub(CompositeExpression::class);

        $expressionBuilderMock->expects($this->once())
            ->method('in')
            ->with('t.sys_lang', [13, -1])
            ->willReturn('t.sys_lang IN (13, -1)');

        $expressionBuilderMock->expects($this->once())
            ->method('andX')
            ->with('t.sys_lang IN (13, -1)')
            ->willReturn($compositeExpression);

        $result = $this->subject->buildExpression(['t' => 'tt_content'], $expressionBuilderMock);

        $this->assertEquals($compositeExpression, $result);
    }

    #[Test]
    public function it_wont_return_language_restriction_for_tables_without_translation(): void
    {
        $expressionBuilderMock = $this->createMock(ExpressionBuilder::class);

        $compositeExpression = $this->createStub(CompositeExpression::class);

        $expressionBuilderMock->expects($this->never())
            ->method('in');

        $expressionBuilderMock->expects($this->once())
            ->method('and')
            ->with()
            ->willReturn($compositeExpression);

        $result = $this->subject->buildExpression(['t' => 'tt_record'], $expressionBuilderMock);

        $this->assertEquals($compositeExpression, $result);
    }
}
