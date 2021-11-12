<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Resource;

use DFAU\ToujouApi\Configuration\ConfigurationManager;

final class ResourceOperationToCommandMap
{
    /** @var array */
    private $commandsByResourceTypeAndOperation;

    public function getCommandConfigForResourceTypeAndOperation(string $resourceType, Operation $operation, array $requiredInterfaces = []): array
    {
        $commandConfig = $this->findCommandConfigByResourceTypeAndOperation($resourceType, $operation);

        if (null === $commandConfig) {
            throw new UnsupportedOperationException('The given operation "' . $operation . '" is not supported by the resource type "' . $resourceType . '"', 1563793877);
        }

        $commandConfig = \is_array($commandConfig) ? $commandConfig : ['__class__' => $commandConfig];
        $commandInterfaces = \class_implements($commandConfig['__class__']);

        if (($missingInterfaces = \array_diff($requiredInterfaces, $commandInterfaces)) !== []) {
            throw new \InvalidArgumentException('The command "' . $commandConfig['__class__'] . '" needs to implement also these inferfaces "' . \implode(', ', $missingInterfaces) . '" in order to be used for the resource type "' . $resourceType . '" and operation "' . $operation . '".', 1563801127);
        }

        $commandName = $commandConfig['__class__'];
        $commandArguments = $commandConfig;
        unset($commandArguments['__class__']);

        return [$commandName, $commandArguments, $commandInterfaces];
    }

    private function findCommandConfigByResourceTypeAndOperation(string $resourceType, Operation $operation)
    {
        $this->loadOperationsToCommandMapFromResourcesConfiguration();
        return $this->commandsByResourceTypeAndOperation[$resourceType][(string) $operation] ?? null;
    }

    private function loadOperationsToCommandMapFromResourcesConfiguration(): void
    {
        if (null !== $this->commandsByResourceTypeAndOperation) {
            return;
        }
        $resourcesConfiguration = ConfigurationManager::getResourcesConfiguration();
        $this->commandsByResourceTypeAndOperation = \array_filter(\array_map(function ($resourceConfiguration) {
            return $resourceConfiguration['operationToCommandMap'] ?? null;
        }, $resourcesConfiguration));
    }
}
