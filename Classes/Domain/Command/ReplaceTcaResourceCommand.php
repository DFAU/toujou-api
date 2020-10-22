<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Domain\Command;

use DFAU\ToujouApi\Command\AsIsResourceDataCommand;
use DFAU\ToujouApi\Command\ResourceDataCommand;
use DFAU\ToujouApi\Command\ResourceReferencingCommand;
use DFAU\ToujouApi\Command\TcaRecordReferencingCommand;
use DFAU\ToujouApi\Command\Traits\AsIsResourceDataCommandTrait;
use DFAU\ToujouApi\Command\Traits\ResourceDataCommandTrait;
use DFAU\ToujouApi\Command\Traits\ResourceReferencingCommandTrait;
use DFAU\ToujouApi\Command\Traits\TcaRecordReferencingCommandTrait;

class ReplaceTcaResourceCommand implements TcaRecordReferencingCommand, ResourceReferencingCommand, ResourceDataCommand, AsIsResourceDataCommand
{
    use TcaRecordReferencingCommandTrait;
    use ResourceReferencingCommandTrait;
    use ResourceDataCommandTrait;
    use AsIsResourceDataCommandTrait;

    public function __construct(string $resourceType, string $resourceIdentifier, string $tableName, array $resourceData, ?array $asIsResourceData)
    {
        $this->resourceType = $resourceType;
        $this->resourceIdentifier = $this->uid = $resourceIdentifier;
        $this->tableName = $tableName;
        $this->resourceData = $resourceData;
        $this->asIsResourceData = $asIsResourceData;
    }
}
