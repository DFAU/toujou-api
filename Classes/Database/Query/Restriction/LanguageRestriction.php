<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Database\Query\Restriction;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionInterface;

class LanguageRestriction implements QueryRestrictionInterface
{
    /** @var string[] */
    private $excludedTables = [
        'pages',
        'sys_file_reference',
    ];

    /** @var Context */
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function buildExpression(array $queriedTables, ExpressionBuilder $expressionBuilder): CompositeExpression
    {
        $constraints = [];

        foreach ($queriedTables as $tableAlias => $tableName) {
            $tableConstraint = $this->getTableLanguageExpression($tableName, $tableAlias, $expressionBuilder);
            if ($tableConstraint) {
                $constraints[] = $tableConstraint;
            }
        }

        return $expressionBuilder->andX(...$constraints);
    }

    private function getTableLanguageExpression(
        string $tableName,
        string $tableAlias,
        ExpressionBuilder $expressionBuilder
    ): ?string {
        $tcaConfig = $GLOBALS['TCA'][$tableName]['ctrl'] ?? [];

        $languageField = $tcaConfig['languageField'] ?? null;

        if (null === $languageField) {
            return null;
        }
        if (\in_array($tableName, $this->excludedTables)) {
            return null;
        }

        /** @var LanguageAspect $languageAspect */
        $languageAspect = $this->context->getAspect('language');

        return $expressionBuilder->in($tableAlias . '.' . $languageField, [$languageAspect->getContentId(), -1]);
    }
}
