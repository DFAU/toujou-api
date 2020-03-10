<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\CommandHandler;


use DFAU\ToujouApi\Command\TcaRecordReferencingCommand;
use DFAU\ToujouApi\Domain\Command\CreateTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\DeleteTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\UnitOfWorkTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\UpdateTcaResourceCommand;
use DFAU\ToujouApi\Resource\ResourceOperationToCommandMap;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DataHandlerCommandHandler
{

    /**
     * @var object|mixed
     */
    protected $dataHandler;

    /**
     * @var object
     */
    protected $operationToCommandMap;

    public function __construct()
    {
        $this->dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $this->operationToCommandMap = GeneralUtility::makeInstance(ResourceOperationToCommandMap::class);
    }

    public function handleCreateTcaResourceCommand(CreateTcaResourceCommand $createCommand): void
    {
        $datamap = $commandmap = [];
        $this->addCommandToDataOrCommandMap($createCommand, $datamap, $commandmap);
        $this->process($datamap, $commandmap);
    }

    public function handleDataHandlerUnitOfWorkCommand(UnitOfWorkTcaResourceCommand $unitOfWorkTcaResourceCommand): void
    {
        $datamap = $commandmap = [];
        foreach ($unitOfWorkTcaResourceCommand->getUnitOfWorkCommands() as $command) {
            $this->addCommandToDataOrCommandMap($command, $datamap, $commandmap);
        }
        $this->process($datamap, $commandmap);
    }

    public function handleUpdateTcaResourceCommand(UpdateTcaResourceCommand $updateCommand): void
    {
        $datamap = $commandmap = [];
        $this->addCommandToDataOrCommandMap($updateCommand, $datamap, $commandmap);
        $this->process($datamap, $commandmap);
    }

    public function handleDeleteTcaResourceCommand(DeleteTcaResourceCommand $deleteCommand): void
    {
        $datamap = $commandmap = [];
        $this->addCommandToDataOrCommandMap($deleteCommand, $datamap, $commandmap);
        $this->process($datamap, $commandmap);
    }

    protected function addCommandToDataOrCommandMap(TcaRecordReferencingCommand $command, array &$datamap, array &$commandmap): void
    {
        switch ($command) {
            case $command instanceof CreateTcaResourceCommand:
                $datamap[$command->getTableName()][$command->getUid()] = $command->getRecordData();
                break;
            case $command instanceof UpdateTcaResourceCommand:
                $datamap[$command->getTableName()][$command->getUid()] = $command->getRecordData();
                break;
            case $command instanceof DeleteTcaResourceCommand:
                $commandmap[$command->getTableName()][$command->getUid()]['delete'] = 1;
                break;
            default:
                throw new \BadMethodCallException('The given command "' . get_class($command) . '" is not supported yet', 1564476754);
                break;
        }
    }


    protected function process(array $datamap = [], array $commandmap = []): array
    {
        if (empty($datamap) && empty($commandmap)) {
            return [];
        }

        $this->dataHandler->isImporting = true;
        $this->dataHandler->dontProcessTransformations = true;
        $this->dataHandler->start($datamap, $commandmap);
        $this->dataHandler->process_datamap();
        $this->dataHandler->process_cmdmap();

        return $this->dataHandler->substNEWwithIDs;
    }
}