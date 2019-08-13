<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\Repository;


final class TcaResourceRepository extends AbstractDatabaseResourceRepository
{
    public function __construct(string $tableName, string $identifier = self::DEFAULT_IDENTIFIER)
    {
        $this->tableName = $tableName;
        $this->identifier = $identifier;
    }
}
