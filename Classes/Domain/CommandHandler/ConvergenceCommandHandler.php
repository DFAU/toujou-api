<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Domain\CommandHandler;

use League\Tactician\CommandBus;
use Cascader\Cascader;
use DFAU\Convergence\Operations\AbstractResourceOperation;
use DFAU\Convergence\Operations\AddResource;
use DFAU\Convergence\Operations\RemoveResource;
use DFAU\Convergence\Operations\UpdateResource;
use DFAU\Convergence\OperationsFactory;
use DFAU\Convergence\Schema;
use DFAU\ToujouApi\CommandBus\CommandBusFactory;
use DFAU\ToujouApi\Configuration\ConfigurationManager;
use DFAU\ToujouApi\Domain\Command\ReplaceTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\UnitOfWorkTcaResourceCommand;
use DFAU\ToujouApi\Resource\Operation;
use DFAU\ToujouApi\Resource\ResourceOperationToCommandMap;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConvergenceCommandHandler
{
    /** @var CommandBus */
    protected $commandBus;

    /** @var ResourceOperationToCommandMap */
    protected $resourceOperationToCommandMap;

    /** @var Cascader */
    protected $objectFactory;

    public function __construct()
    {
        $this->commandBus = CommandBusFactory::createFromCommandConfiguration();
        $this->resourceOperationToCommandMap = GeneralUtility::makeInstance(ResourceOperationToCommandMap::class);
        $this->objectFactory = new Cascader();
    }

    public function handleReplaceTcaResourceCommand(ReplaceTcaResourceCommand $replaceTcaResourceCommand): void
    {
        $schema = $this->getSchemaForResourceType($replaceTcaResourceCommand->getResourceType());
        $toBeResources = $replaceTcaResourceCommand->getResourceData() ?? [];
        $asIsResources = $replaceTcaResourceCommand->getAsIsResourceData() ?? [];

        $operations = GeneralUtility::makeInstance(OperationsFactory::class)->buildFromSchemaAndResources($schema, $toBeResources, $asIsResources);

        $convergenceOperationsToApiOperationsMap = [
            AddResource::class => new Operation(Operation::CREATE),
            UpdateResource::class => new Operation(Operation::UPDATE),
            RemoveResource::class => new Operation(Operation::DELETE),
        ];

        $unitOfWorkCommands = \array_map(function (AbstractResourceOperation $operation) use ($convergenceOperationsToApiOperationsMap) {
            $resource = $operation->getResource();
            switch ($operation) {
                case $operation instanceof AddResource:
                    [$commandName, $commandArguments] = $this->resourceOperationToCommandMap->getCommandConfigForResourceTypeAndOperation($resource['type'], $convergenceOperationsToApiOperationsMap[AddResource::class]);
                    $commandArguments['resourceData'] = $resource['attributes'];
                    break;
                case $operation instanceof UpdateResource:
                    [$commandName, $commandArguments] = $this->resourceOperationToCommandMap->getCommandConfigForResourceTypeAndOperation($resource['type'], $convergenceOperationsToApiOperationsMap[UpdateResource::class]);
                    $commandArguments['resourceData'] = $operation->getResourceUpdates()['attributes'];
                    break;
                case $operation instanceof RemoveResource:
                    [$commandName, $commandArguments] = $this->resourceOperationToCommandMap->getCommandConfigForResourceTypeAndOperation($resource['type'], $convergenceOperationsToApiOperationsMap[RemoveResource::class]);
                    $commandArguments['resourceData'] = $resource['attributes'];
                    break;
                default:
                    throw new \BadMethodCallException('The given operation "' . \get_class($operation) . '" is not supported yet', 1564062825);
            }

            $commandArguments['resourceIdentifier'] = $resource['id'];
            $commandArguments['resourceType'] = $resource['type'];

            return $this->objectFactory->create($commandName, $commandArguments);
        }, $operations);

        $command = GeneralUtility::makeInstance(UnitOfWorkTcaResourceCommand::class, $unitOfWorkCommands);
        $this->commandBus->handle($command);
    }

    protected function getSchemaForResourceType(string $resourceType): Schema
    {
        $resourceConfiguration = ConfigurationManager::getResourcesConfiguration();
        if (!isset($resourceConfiguration[$resourceType]['convergenceSchema'])) {
            throw new \InvalidArgumentException('No convergenceSchema is specified for resource type "' . $resourceType . '"', 1564648569);
        }

        $schemaConfig = \is_array($resourceConfiguration[$resourceType]['convergenceSchema']) ? $resourceConfiguration[$resourceType]['convergenceSchema'] : ['__class__' => $resourceConfiguration[$resourceType]['convergenceSchema']];

        $schemaClassName = $schemaConfig['__class__'];
        $schemaArguments = $schemaConfig;
        unset($schemaArguments['__class__']);

        /** @var Schema $schema */
        $schema = $this->objectFactory->create($schemaClassName, $schemaArguments);
        return $schema;
    }
}
