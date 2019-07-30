<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\Command;


use DFAU\ToujouApi\Command\TcaResourceCommand;
use DFAU\ToujouApi\Command\UnitOfWorkCommand;

class DataHandlerUnitOfWorkCommand implements UnitOfWorkCommand
{

    /**
     * @var TcaResourceCommand[]
     */
    protected $unitOfWork = [];

    public function __construct(array $unitOfWork)
    {
        $this->unitOfWork = array_map(function(TcaResourceCommand $command) { return $command; }, $unitOfWork);
    }

    public function getUnitOfWorkCommands(): array
    {
        return $this->unitOfWork;
    }
}
