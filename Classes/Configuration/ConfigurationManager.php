<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Configuration;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Package\PackageInterface;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationManager
{
    protected $cache;

    protected const CONFIGURATION_TYPE_COMMAND_BUS = 'CommandBus';

    protected const CONFIGURATION_TYPE_RESOURCES = 'Resources';

    protected const CONFIGURATION_TYPE_ROUTES = 'Routes';

    public function __construct(FrontendInterface $cache = null)
    {
        $this->cache = $cache ?? GeneralUtility::makeInstance(CacheManager::class)->getCache('core');
    }

    protected function getConfigurationFromPackages(string $configType): array
    {
        $t3Version = GeneralUtility::makeInstance(Typo3Version::class);
        $cacheIdentifier = 'ToujouApi' . $configType . 'FromPackages_' . \sha1($t3Version->getVersion() . Environment::getProjectPath());

        if ($this->cache->has($cacheIdentifier)) {
            $configFromPackages = \unserialize(\substr($this->cache->get($cacheIdentifier), 6, -2), ['allowed_classes' => false]);
        } else {
            $packageManager = GeneralUtility::makeInstance(PackageManager::class);
            $packages = $packageManager->getActivePackages();

            $configFromPackages = \array_replace_recursive(...\array_map(function (PackageInterface $package) use ($configType) {
                $resourcesFileNameForPackage = $package->getPackagePath() . 'Configuration/ToujouApi/' . $configType . '.php';
                if (\file_exists($resourcesFileNameForPackage)) {
                    return require $resourcesFileNameForPackage;
                }

                return [];
            }, \array_values($packages)));

            $this->cache->set($cacheIdentifier, \serialize($configFromPackages));
        }

        return $configFromPackages;
    }

    public static function getCommandBusConfiguration(): array
    {
        static $configuration;

        if (null === $configuration) {
            $configuration = (new static())->getConfigurationFromPackages(static::CONFIGURATION_TYPE_COMMAND_BUS);
        }

        return $configuration;
    }

    public static function getResourcesConfiguration(): array
    {
        static $configuration;

        if (null === $configuration) {
            $configuration = (new static())->getConfigurationFromPackages(static::CONFIGURATION_TYPE_RESOURCES);
        }

        return $configuration;
    }

    public static function getRoutesConfiguration(): array
    {
        static $configuration;

        if (null === $configuration) {
            $configuration = (new static())->getConfigurationFromPackages(static::CONFIGURATION_TYPE_ROUTES);
        }

        return $configuration;
    }
}
