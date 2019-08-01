<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\Command;


use DFAU\ToujouApi\Command\TcaRecordReferencingCommand;
use DFAU\ToujouApi\Command\Traits\TcaRecordDataCommandTrait;

class DeleteTcaResourceCommand implements TcaRecordReferencingCommand
{

    use TcaRecordDataCommandTrait;

    public function __construct(string $resourceIdentifier, string $tableName)
    {
        $this->uid = $resourceIdentifier;
        $this->tableName = $tableName;
    }
}
