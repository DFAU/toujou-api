<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\Command;

use DFAU\ToujouApi\Command\TcaRecordDataCommand;
use DFAU\ToujouApi\Command\Traits\TcaRecordDataCommandTrait;

class CreateTcaResourceCommand implements TcaRecordDataCommand
{

    use TcaRecordDataCommandTrait;

    public function __construct(string $resourceIdentifier, string $tableName, array $resourceData)
    {
        $this->uid = $resourceIdentifier;
        $this->tableName = $tableName;
        $this->recordData = $resourceData;
    }
}
