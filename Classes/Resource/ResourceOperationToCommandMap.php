<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Resource;


use DFAU\ToujouApi\Configuration\ConfigurationManager;

final class ResourceOperationToCommandMap
{
    /**
     * @var array
     */
    protected $commandsByResourceTypeAndOperation;

    public function findCommandByResourceTypeAndOperation(string $resourceType, Operation $operation)
    {
        $this->loadOperationsToCommandMapFromResourcesConfiguration();
        return $this->commandsByResourceTypeAndOperation[$resourceType][(string) $operation] ?? null;
    }


    protected function loadOperationsToCommandMapFromResourcesConfiguration(): void
    {
        if ($this->commandsByResourceTypeAndOperation !== null) {
            return;
        }
        $resourcesConfiguration = ConfigurationManager::getResourcesConfiguration();
        $this->commandsByResourceTypeAndOperation = array_filter(array_map(function ($resourceConfiguration) {
            return $resourceConfiguration['operationToCommandMap'] ?? null;
        }, $resourcesConfiguration));
    }
}
