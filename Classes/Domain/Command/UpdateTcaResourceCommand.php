<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\Command;

use DFAU\ToujouApi\Command\ResourceDataCommand;
use DFAU\ToujouApi\Command\ResourceReferencingCommand;
use DFAU\ToujouApi\Command\TcaRecordDataCommand;
use DFAU\ToujouApi\Command\Traits\ResourceDataCommandTrait;
use DFAU\ToujouApi\Command\Traits\ResourceReferencingCommandTrait;
use DFAU\ToujouApi\Command\Traits\TcaRecordDataCommandTrait;

class UpdateTcaResourceCommand implements TcaRecordDataCommand, ResourceDataCommand, ResourceReferencingCommand
{

    use TcaRecordDataCommandTrait;
    use ResourceReferencingCommandTrait;
    use ResourceDataCommandTrait;

    public function __construct(string $resourceIdentifier, string $resourceType, string $tableName, array $resourceData)
    {
        $this->uid = $this->resourceIdentifier = $resourceIdentifier;
        $this->resourceType = $resourceType;
        $this->tableName = $tableName;
        $this->recordData = $this->resourceData = $resourceData;
    }
}
