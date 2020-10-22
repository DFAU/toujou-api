<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Domain\Command;

use DFAU\ToujouApi\Command\ResourceReferencingCommand;
use DFAU\ToujouApi\Command\TcaRecordReferencingCommand;
use DFAU\ToujouApi\Command\Traits\ResourceReferencingCommandTrait;
use DFAU\ToujouApi\Command\Traits\TcaRecordDataCommandTrait;

class DeleteTcaResourceCommand implements TcaRecordReferencingCommand, ResourceReferencingCommand
{
    use TcaRecordDataCommandTrait;
    use ResourceReferencingCommandTrait;

    public function __construct(string $resourceIdentifier, string $resourceType, string $tableName)
    {
        $this->uid = $this->resourceIdentifier = $resourceIdentifier;
        $this->resourceType = $resourceType;
        $this->tableName = $tableName;
    }
}
