<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\Repository;


use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\QueryHelper;

final class TcaResourceRepository extends AbstractDatabaseResourceRepository
{

    /**
     * @var string
     */
    protected $orderBy;

    public function __construct(string $tableName, string $identifier = self::DEFAULT_IDENTIFIER, string $orderBy = null)
    {
        $this->tableName = $tableName;
        $this->identifier = $identifier;
        if ($orderBy === null && (isset($GLOBALS['TCA'][$this->tableName]['ctrl']['sortby']) || isset($GLOBALS['TCA'][$this->tableName]['ctrl']['default_sortby']))) {
            $orderBy = $GLOBALS['TCA'][$this->tableName]['ctrl']['sortby'] ?: $GLOBALS['TCA'][$this->tableName]['ctrl']['default_sortby'];
        }
        $this->orderBy = QueryHelper::parseOrderBy((string)$orderBy);
    }

    protected function createQuery(): QueryBuilder
    {
        $queryBuilder = parent::createQuery();
        foreach ($this->orderBy as $orderPair) {
            list($fieldName, $order) = $orderPair;
            $queryBuilder->addOrderBy($fieldName, $order);
        }
        return $queryBuilder;
    }
}
