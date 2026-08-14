<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Domain\Repository;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileReferenceRepository extends AbstractDatabaseResourceRepository
{
    public const TABLE_NAME = 'sys_file_reference';

    protected ConnectionPool $connectionPool;

    public function __construct(string $tableName = self::TABLE_NAME)
    {
        $this->tableName = $tableName;
        $this->connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
    }

    public function findByRelation(string $foreignTableName, string $foreignField, string $foreignIdentifier): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);

        $rows = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'tablenames',
                    $queryBuilder->createNamedParameter($foreignTableName)
                ),
                $queryBuilder->expr()->eq(
                    'fieldname',
                    $queryBuilder->createNamedParameter($foreignField)
                ),
                $queryBuilder->expr()->eq(
                    'uid_foreign',
                    $queryBuilder->createNamedParameter((int) $foreignIdentifier)
                ),
                // References pointing to a missing or deleted original file (empty uid_local)
                // would throw "Incorrect reference to original file given for FileReference."
                // while instantiating TYPO3\CMS\Core\Resource\FileReference. Filter them out.
                $queryBuilder->expr()->neq(
                    'uid_local',
                    $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
                )
            )
            ->orderBy('sorting_foreign')
            ->executeQuery()
            ->fetchAllAssociative();

        return \array_map($this->createMetaMapper(), $rows);
    }
}
