<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\Command;


use DFAU\ToujouApi\Command\TcaRecordReferencingCommand;
use DFAU\ToujouApi\Command\UnitOfWorkCommand;

class UnitOfWorkTcaResourceCommand implements UnitOfWorkCommand
{
    /**
     * @var TcaRecordReferencingCommand[]
     */
    protected $unitOfWorkCommands;

    public function __construct(array $unitOfWorkCommands)
    {
        $this->unitOfWorkCommands = array_map(function(TcaRecordReferencingCommand $command) { return $command; }, $unitOfWorkCommands);
    }

    public function getUnitOfWorkCommands(): array
    {
        return $this->unitOfWorkCommands;
    }
}
