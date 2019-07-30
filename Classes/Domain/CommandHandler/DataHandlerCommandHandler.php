<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\CommandHandler;


use DFAU\ToujouApi\Command\TcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\CreateTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\DataHandlerUnitOfWorkCommand;
use DFAU\ToujouApi\Domain\Command\DeleteTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\UpdateTcaResourceCommand;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DataHandlerCommandHandler
{

    public function __construct()
    {
        $this->dataHandler = GeneralUtility::makeInstance(DataHandler::class);
    }

    public function handleCreateTcaResource(CreateTcaResourceCommand $createCommand): void
    {
        $datamap = $commandmap = [];
        $this->addCommandToDataOrCommandMap($createCommand, $datamap, $commandmap);
        $this->process($datamap, $commandmap);
    }

    public function handleDataHandlerUnitOfWork(DataHandlerUnitOfWorkCommand $dataHandlerUnitOfWorkCommand): void
    {
        $datamap = $commandmap = [];
        foreach ($dataHandlerUnitOfWorkCommand->getUnitOfWorkCommands() as $command) {
            $this->addCommandToDataOrCommandMap($command, $datamap, $commandmap);
        }
        $this->process($datamap, $commandmap);
    }

    public function handleUpdateTcaResource(UpdateTcaResourceCommand $updateCommand): void
    {
        $datamap = $commandmap = [];
        $this->addCommandToDataOrCommandMap($updateCommand, $datamap, $commandmap);
        $this->process($datamap, $commandmap);
    }

    public function handleDeleteTcaResource(DeleteTcaResourceCommand $deleteCommand): void
    {
        $datamap = $commandmap = [];
        $this->addCommandToDataOrCommandMap($deleteCommand, $datamap, $commandmap);
        $this->process($datamap, $commandmap);
    }

    protected function addCommandToDataOrCommandMap(TcaResourceCommand $command, array &$datamap, array &$commandmap): void
    {
        switch ($command) {
            case $command instanceof CreateTcaResourceCommand:
                $datamap[$command->getTableName()][$command->getResourceIdentifier()] = $command->getResourceData();
                break;
            case $command instanceof UpdateTcaResourceCommand:
                $datamap[$command->getTableName()][$command->getResourceIdentifier()] = $command->getResourceData();
                break;
            case $command instanceof DeleteTcaResourceCommand:
                $commandmap[$command->getTableName()][$command->getResourceIdentifier()]['delete'] = 1;
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

        $this->dataHandler->start($datamap, $commandmap);
        $this->dataHandler->process_datamap();
        $this->dataHandler->process_cmdmap();

        return $this->dataHandler->substNEWwithIDs;
    }

}