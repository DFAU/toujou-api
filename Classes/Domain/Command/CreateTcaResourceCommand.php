<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\Command;

use DFAU\ToujouApi\Command\TcaResourceCommand;
use DFAU\ToujouApi\Command\Traits\TcaResourceTrait;

class CreateTcaResourceCommand implements TcaResourceCommand
{

    use TcaResourceTrait;

    public function __construct(string $resourceType, string $resourceIdentifier, string $tableName, array $resourceData)
    {
        $this->resourceType = $resourceType;
        $this->resourceIdentifier = $resourceIdentifier;
        $this->tableName = $tableName;
        $this->resourceData = $resourceData;
    }
}
