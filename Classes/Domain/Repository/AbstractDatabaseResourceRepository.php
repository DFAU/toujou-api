<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\Repository;


use DFAU\ToujouApi\Database\Query\Restriction\ApiRestrictionContainer;
use DFAU\ToujouApi\Domain\Value\ZuluDate;
use League\Fractal\Pagination\Cursor;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DefaultRestrictionContainer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractDatabaseResourceRepository implements ApiResourceRepository, DatabaseResourceRepository, PageRelationRepository
{

    const DEFAULT_IDENTIFIER = 'uid';

    const DEFAULT_PARENT_PAGE_IDENTIFIER = 'pid';

    /**
     * @var
     */
    protected $identifier = self::DEFAULT_IDENTIFIER;

    /**
     * @var string
     */
    protected $parentPageIdentifier = self::DEFAULT_PARENT_PAGE_IDENTIFIER;

    /**
     * @var string
     */
    protected $tableName;

    public function getTableName(): string
    {
        return $this->tableName;
    }

    protected function createQuery(): QueryBuilder
    {
        $querybuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->tableName)
            ->select('*')
            ->from($this->tableName);
        $querybuilder->setRestrictions(GeneralUtility::makeInstance(ApiRestrictionContainer::class));
        return $querybuilder;
    }

    public function findWithCursor(int $limit, ?string $currentCursor, ?string $previousCursor) : array
    {
        $query = $this->createQuery()->setMaxResults($limit);

        if ($currentCursor) {
            $query->where($query->expr()->gt($this->identifier, $currentCursor));
        }

        $result = $query->execute()->fetchAll();
        $nextCursor = !empty($result) ? end($result)[$this->identifier] : null;

        $result = array_map($this->createMetaMapper(), $result);

        return [$result, new Cursor($currentCursor, $previousCursor, $nextCursor, count($result))];
    }

    public function findOneByIdentifier($identifier): ?array
    {
        $query = $this->createQuery()->setMaxResults(1);
        $query->where($query->expr()->eq($this->identifier, $query->quote($identifier)));

        $result = $query->execute()->fetch();

        if ($result) {
            return $this->createMetaMapper()($result);
        }

        return null;
    }

    public function findByIdentifiers(array $identifiers): array
    {
        $query = $this->createQuery();
        $query->where($query->expr()->in($this->identifier, array_map([$query, 'quote'], $identifiers)));

        $result = $query->execute()->fetchAll();

        $result = array_map($this->createMetaMapper(), $result);

        return $result;
    }

    public function findByPageIdentifier($pageIdentifier): array
    {
        $query = $this->createQuery();
        $query->where($query->expr()->eq($this->parentPageIdentifier, $query->quote($pageIdentifier)));

        $result = $query->execute()->fetchAll();

        $result = array_map($this->createMetaMapper(), $result);

        return $result;
    }

    protected function createMetaMapper(): \Closure
    {
        $tableName = $this->tableName;
        return function (array $resource) use ($tableName): array {
            $resource[static::META_ATTRIBUTE] = [
                static::META_UID => $resource[static::DEFAULT_IDENTIFIER]
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


}
