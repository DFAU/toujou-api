<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Configuration;


use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Package\PackageInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationManager
{

    protected const CONFIGURATION_TYPE_COMMAND_BUS = 'CommandBus';

    protected const CONFIGURATION_TYPE_RESOURCES = 'Resources';

    protected const CONFIGURATION_TYPE_ROUTES = 'Routes';

    public function __construct(FrontendInterface $cache = null)
    {
        $this->cache = $cache ?? GeneralUtility::makeInstance(CacheManager::class)->getCache('cache_core');
    }

    protected function getConfigurationFromPackages(string $configType): array
    {
        $cacheIdentifier = 'ToujouApi' . $configType . 'FromPackages_' . sha1(TYPO3_version . Environment::getProjectPath());

        if ($this->cache->has($cacheIdentifier)) {
            $configFromPackages = unserialize(substr($this->cache->get($cacheIdentifier), 6, -2), ['allowed_classes' => false]);
        } else {
            $packageManager = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Package\PackageManager::class);
            $packages = $packageManager->getActivePackages();

            $configFromPackages = array_replace_recursive(...array_map(function(PackageInterface $package) use ($configType) {
                $resourcesFileNameForPackage = $package->getPackagePath() . 'Configuration/ToujouApi/' . $configType . '.php';
                if (file_exists($resourcesFileNameForPackage)) {
                    return require $resourcesFileNameForPackage;
                }
                return [];
            }, array_values($packages)));

            $this->cache->set($cacheIdentifier, serialize($configFromPackages));
        }

        return $configFromPackages;
    }

    static public function getCommandBusConfiguration(): array
    {
        static $configuration;

        if ($configuration === null) {
            $configuration = (new static())->getConfigurationFromPackages(static::CONFIGURATION_TYPE_COMMAND_BUS);
        }

        return $configuration;
    }

    static public function getResourcesConfiguration(): array
    {
        static $configuration;

        if ($configuration === null) {
            $configuration = (new static())->getConfigurationFromPackages(static::CONFIGURATION_TYPE_RESOURCES);
        }

        return $configuration;
    }

    static public function getRoutesConfiguration(): array
    {
        static $configuration;

        if ($configuration === null) {
            $configuration = (new static())->getConfigurationFromPackages(static::CONFIGURATION_TYPE_ROUTES);
        }

        return $configuration;
    }

}
