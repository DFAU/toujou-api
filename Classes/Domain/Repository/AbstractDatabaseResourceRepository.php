<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Domain\Repository;

use DFAU\ToujouApi\Database\Query\Restriction\ApiRestrictionContainer;
use DFAU\ToujouApi\Domain\Value\ZuluDate;
use League\Fractal\Pagination\Cursor;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractDatabaseResourceRepository implements ApiResourceRepository, DatabaseResourceRepository, PageRelationRepository
{
    public const DEFAULT_IDENTIFIER = 'uid';

    public const DEFAULT_PARENT_PAGE_IDENTIFIER = 'pid';

    public const ALLOWED_FILTER_OPERATORS = ['eq', 'neq', 'gt', 'gte', 'lt', 'lte', 'in'];

    private const DUPLICATE_IDENTIFIER_SEPARATOR = '#';

    /** @var string */
    protected $identifier = self::DEFAULT_IDENTIFIER;

    /** @var string */
    protected $parentPageIdentifier = self::DEFAULT_PARENT_PAGE_IDENTIFIER;

    /** @var string */
    protected $tableName;

    public function getTableName(): string
    {
        return $this->tableName;
    }

    protected function createQuery(): QueryBuilder
    {
        /** @var ApiRestrictionContainer $apiRestrictionContainer */
        $apiRestrictionContainer = GeneralUtility::makeInstance(ApiRestrictionContainer::class);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->tableName)
            ->select('*')
            ->from($this->tableName);

        $queryBuilder->setRestrictions($apiRestrictionContainer);

        return $queryBuilder;
    }

    public function addFiltersToQuery(array $filters, QueryBuilder $queryBuilder): QueryBuilder
    {
        $constraints = [];
        foreach ($filters as $key => $value) {
            if (!\is_array($value)) {
                $constraints[] = $queryBuilder->expr()->in($key, $queryBuilder->createNamedParameter($value));

                continue;
            }

            $operator = \key($value);
            if (!\in_array($operator, self::ALLOWED_FILTER_OPERATORS)) {
                continue;
            }

            $filterValue = \reset($value);
            $constraints[] = $queryBuilder->expr()->{$operator}($key, $queryBuilder->createNamedParameter($filterValue));
        }

        $queryBuilder->andWhere(...$constraints);

        return $queryBuilder;
    }

    public function findByFiltersWithCursor(array $filters, int $limit, ?string $currentCursor, ?string $previousCursor, $context = null): array
    {
        $query = $this->createQuery()->setMaxResults($limit);

        if ($currentCursor) {
            $query->where($query->expr()->gt($this->identifier, $query->createNamedParameter($currentCursor)));
        }

        if ([] !== $filters) {
            $query = $this->addFiltersToQuery($filters, $query);
        }

        $query->orderBy($this->identifier, 'ASC');

        $result = $query->executeQuery()->fetchAllAssociative();

        $result = \array_map($this->createDeduplicator(), $result);
        $result = \array_map($this->createOverlayMapper($context), $result);

        $nextCursor = [] === $result ? null : \end($result)[$this->identifier];

        $result = \array_map($this->createMetaMapper(), $result);

        return [$result, new Cursor($currentCursor, $previousCursor, $nextCursor, \count($result))];
    }

    public function findOneByIdentifier($identifier, $context = null): ?array
    {
        $query = $this->createQuery()->setMaxResults(1);
        $query->where($query->expr()->eq($this->identifier, $query->quote($identifier)));

        $result = $query->executeQuery()->fetchAssociative() ?: [];

        $result = $this->createOverlayMapper($context)($result);

        if ($result) {
            return $this->createMetaMapper()($result);
        }

        return null;
    }

    public function findByIdentifiers(array $identifiers, $context = null, array $filters = []): array
    {
        $query = $this->createQuery();
        $query->where($query->expr()->in($this->identifier, \array_map([$query, 'quote'], $identifiers)));

        $result = $query->executeQuery()->fetchAllAssociative();

        $result = \array_map($this->createDeduplicator(), $result);

        $result = \array_map($this->createOverlayMapper($context), $result);

        return \array_map($this->createMetaMapper(), $result);
    }

    public function findByPageIdentifier($pageIdentifier): array
    {
        $query = $this->createQuery();
        $query->where($query->expr()->eq($this->parentPageIdentifier, $query->quote($pageIdentifier)));

        $result = $query->executeQuery()->fetchAllAssociative();

        // TODO: add overlay?

        $result = \array_map($this->createDeduplicator(), $result);

        return \array_map($this->createMetaMapper(), $result);
    }

    protected function createDeduplicator(): \Closure
    {
        $countPerId = [];

        return function (array $resource) use (&$countPerId): array {
            $identifier = $resource[$this->identifier];
            $countPerId[$identifier] = isset($countPerId[$identifier]) ? $countPerId[$identifier] + 1 : 0;

            if ($countPerId[$identifier] > 0) {
                $resource[$this->identifier] = $identifier . self::DUPLICATE_IDENTIFIER_SEPARATOR . $countPerId[$identifier];
            }

            return $resource;
        };
    }

    protected function createMetaMapper(): \Closure
    {
        $tableName = $this->tableName;

        return function (array $resource) use ($tableName): array {
            $resource[static::META_ATTRIBUTE] = [
                static::META_UID => $resource[static::DEFAULT_IDENTIFIER],
            ];
            if (!empty($GLOBALS['TCA'][$tableName]['ctrl']['crdate']) && !empty($resource[$GLOBALS['TCA'][$tableName]['ctrl']['crdate']])) {
                $resource[static::META_ATTRIBUTE][static::META_CREATED] = ZuluDate::fromTimestamp($resource[$GLOBALS['TCA'][$tableName]['ctrl']['crdate']]);
            }

            if (!empty($GLOBALS['TCA'][$tableName]['ctrl']['tstamp']) && !empty($resource[$GLOBALS['TCA'][$tableName]['ctrl']['tstamp']])) {
                $resource[static::META_ATTRIBUTE][static::META_LAST_UPDATED] = ZuluDate::fromTimestamp($resource[$GLOBALS['TCA'][$tableName]['ctrl']['tstamp']]);
            }

            return $resource;
        };
    }

    protected function createOverlayMapper(?Context $context): \Closure
    {
        return function (array $resource) use ($context): array {
            if ($context instanceof Context && $resource) {
                $pageRepository = GeneralUtility::makeInstance(
                    PageRepository::class,
                    $context
                );
                $overlayResult = $pageRepository->getLanguageOverlay($this->tableName, $resource);
            }

            return $overlayResult ?? $resource;
        };
    }
}
