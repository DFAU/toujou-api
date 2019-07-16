<?php


namespace DFAU\ToujouApi\Domain\Repository;


class ContentElementRepository extends AbstractDatabaseResourceRepository
{
    const TABLE_NAME = 'tt_content';

    public function __construct(string $tableName = self::TABLE_NAME)
    {
        $this->tableName = $tableName;
    }
}
