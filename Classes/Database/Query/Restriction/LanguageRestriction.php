<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Database\Query\Restriction;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LanguageRestriction implements QueryRestrictionInterface
{
    /** @var string[] */
    private $excludedTables = [
        'pages',
        'sys_file_reference',
        'tx_toujou_accordion',
        'tx_toujou_tripdescription_days',
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

        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connection = $connectionPool->getConnectionForTable($tableName);

        if (empty($GLOBALS['TCA'][$tableName]['ctrl']['languageField'])) {
            return '';
        }

        // Select all entries for the current language
        // If any language is set -> get those entries which are not translated yet
        // They will be removed by \TYPO3\CMS\Core\Domain\Repository\PageRepository::getRecordOverlay if not matching overlay mode
        $languageField = $GLOBALS['TCA'][$tableName]['ctrl']['languageField'];

        $transOrigPointerField = $GLOBALS['TCA'][$tableName]['ctrl']['transOrigPointerField'] ?? '';
        if (!$transOrigPointerField || !$languageAspect->getContentId()) {
            return $expressionBuilder->in(
                $tableAlias . '.' . $languageField,
                [(int) $languageAspect->getContentId(), -1]
            );
        }

        $mode = $languageAspect->getLegacyOverlayType();
        if (!$mode) {
            return $expressionBuilder->in(
                $tableAlias . '.' . $languageField,
                [(int) $languageAspect->getContentId(), -1]
            );
        }

        $defLangTableAlias = $tableAlias . '_dl';
        $defaultLanguageRecordsSubSelect = $connection->createQueryBuilder();
        $defaultLanguageRecordsSubSelect
            ->select($defLangTableAlias . '.uid')
            ->from($tableName, $defLangTableAlias)
            ->where(
                $defaultLanguageRecordsSubSelect->expr()->andX(
                    $defaultLanguageRecordsSubSelect->expr()->eq($defLangTableAlias . '.' . $transOrigPointerField, 0),
                    $defaultLanguageRecordsSubSelect->expr()->eq($defLangTableAlias . '.' . $languageField, 0)
                )
            );

        $andConditions = [];
        // records in language 'all'
        $andConditions[] = $expressionBuilder->eq($tableAlias . '.' . $languageField, -1);
        // translated records where a default language exists
        $andConditions[] = $expressionBuilder->andX(
            $expressionBuilder->eq($tableAlias . '.' . $languageField, (int) $languageAspect->getContentId()),
            $expressionBuilder->in(
                $tableAlias . '.' . $transOrigPointerField,
                $defaultLanguageRecordsSubSelect->getSQL()
            )
        );
        if ('hideNonTranslated' !== $mode) {
            // $mode = TRUE
            // returns records from current language which have default language
            // together with not translated default language records
            $translatedOnlyTableAlias = $tableAlias . '_to';
            $queryBuilderForSubselect = $connection->createQueryBuilder();
            $queryBuilderForSubselect
                ->select($translatedOnlyTableAlias . '.' . $transOrigPointerField)
                ->from($tableName, $translatedOnlyTableAlias)
                ->where(
                    $queryBuilderForSubselect->expr()->andX(
                        $queryBuilderForSubselect->expr()->gt($translatedOnlyTableAlias . '.' . $transOrigPointerField, 0),
                        $queryBuilderForSubselect->expr()->eq($translatedOnlyTableAlias . '.' . $languageField, (int) $languageAspect->getContentId())
                    )
                );
            // records in default language, which do not have a translation
            $andConditions[] = $expressionBuilder->andX(
                $expressionBuilder->eq($tableAlias . '.' . $languageField, 0),
                $expressionBuilder->notIn(
                    $tableAlias . '.uid',
                    $queryBuilderForSubselect->getSQL()
                )
            );
        }

        return (string) $expressionBuilder->orX(...$andConditions);
    }
}
