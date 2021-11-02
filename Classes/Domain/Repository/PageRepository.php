<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Domain\Repository;

class PageRepository extends AbstractDatabaseResourceRepository
{
    public const TABLE_NAME = 'pages';

    public function __construct(string $tableName = self::TABLE_NAME)
    {
        $this->tableName = $tableName;
    }
}
