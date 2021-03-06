<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\CommandBus;

use DFAU\ToujouApi\Configuration\ConfigurationManager;
use League\Tactician\Handler\Mapping\CommandToHandlerMapping;
use League\Tactician\Handler\Mapping\FailedToMapCommand;

class MapByConfiguration implements CommandToHandlerMapping
{
    /**
     * @var bool
     */
    protected $configurationLoaded = false;

    /**
     * @var array<string, array<string>>
     */
    protected $commandHandlerMap;

    public function getClassName(string $commandClassName): string
    {
        $this->loadConfiguration();
        if (! array_key_exists($commandClassName, $this->commandHandlerMap)) {
            throw FailedToMapCommand::className($commandClassName);
        }

        return $this->commandHandlerMap[$commandClassName][0];
    }

    public function getMethodName(string $commandClassName): string
    {
        $this->loadConfiguration();
        if (! array_key_exists($commandClassName, $this->commandHandlerMap)) {
            throw FailedToMapCommand::methodName($commandClassName);
        }

        return $this->commandHandlerMap[$commandClassName][1];
    }

    protected function loadConfiguration()
    {
        if ($this->configurationLoaded) {
            return;
        }
        $this->commandHandlerMap = ConfigurationManager::getCommandBusConfiguration()['commands'];
        $this->configurationLoaded = true;
    }
}
