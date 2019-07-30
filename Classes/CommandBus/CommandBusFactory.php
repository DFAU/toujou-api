<?php declare(strict_types=1);


namespace DFAU\ToujouApi\CommandBus;


use Cascader\Cascader;
use DFAU\ToujouApi\Configuration\ConfigurationManager;
use League\Tactician\CommandBus;
use TYPO3\CMS\Core\Service\DependencyOrderingService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CommandBusFactory
{

    static public function createFromCommandConfiguration(): CommandBus
    {
        static $commandBus;

        if ($commandBus === null) {
            $commandBusConfiguration = ConfigurationManager::getCommandBusConfiguration();

            $middlewares = array_map(function ($target) {
                if (is_array($target) && isset($target['__class__'])) {
                    $constructorArgs = $target;
                    unset($constructorArgs['__class__']);
                    return (new Cascader())->create($target['__class__'], $constructorArgs);
                }

                if (is_string($target)) {
                    return GeneralUtility::makeInstance($target);
                }
            }, static::sanitizeMiddlewares($commandBusConfiguration['middlewares']));

            $commandBus = new CommandBus(...$middlewares);
        }

        return $commandBus;
    }

    static protected function sanitizeMiddlewares(array $commandBusMiddlewares): array
    {
        $orderedMiddlewares = GeneralUtility::makeInstance(DependencyOrderingService::class)->orderByDependencies($commandBusMiddlewares);

        $sanitizedMiddlewares = [];
        foreach ($orderedMiddlewares as $name => $middleware) {
            if (isset($middleware['disabled']) && $middleware['disabled'] === true) {
                // Skip this middleware if disabled by configuration
                continue;
            }
            $sanitizedMiddlewares[] = $middleware['target'];
        }

        return $sanitizedMiddlewares;
    }

}
