<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\CommandBus;

use DFAU\ToujouApi\Configuration\ConfigurationManager;
use League\Tactician\Handler\Mapping\CommandToHandlerMapping;
use League\Tactician\Handler\Mapping\FailedToMapCommand;
use League\Tactician\Handler\Mapping\MethodToCall;

class MapByConfiguration implements CommandToHandlerMapping
{
    /** @var bool */
    protected $configurationLoaded = false;

    /** @var array<string, array<string>> */
    protected $commandHandlerMap;

    public function getClassName(string $commandClassName): string
    {
        $this->loadConfiguration();
        if (!\array_key_exists($commandClassName, $this->commandHandlerMap)) {
            throw FailedToMapCommand::className($commandClassName);
        }

        return $this->commandHandlerMap[$commandClassName][0];
    }

    public function getMethodName(string $commandClassName): string
    {
        $this->loadConfiguration();
        if (!\array_key_exists($commandClassName, $this->commandHandlerMap)) {
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

    public function findHandlerForCommand(string $commandFQCN): MethodToCall
    {
        $this->loadConfiguration();
        if (!\array_key_exists($commandFQCN, $this->commandHandlerMap)) {
            throw FailedToMapCommand::className($commandFQCN);
        }

        return new MethodToCall($this->commandHandlerMap[$commandFQCN][0], $this->commandHandlerMap[$commandFQCN][1]);
    }
}
