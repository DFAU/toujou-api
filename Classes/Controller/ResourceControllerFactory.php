<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Controller;


use Cascader\Cascader;
use DFAU\ToujouApi\Configuration\ConfigurationManager;
use DFAU\ToujouApi\Resource\Numerus;

class ResourceControllerFactory
{

    static public function createResourceControllerFromRouteDefintinion(string $routeIdentifier, array $routeDefinition) : AbstractResourceController
    {
        if (empty($routeDefinition['numerus'])) {
            throw new \InvalidArgumentException('The resource route "' . $routeIdentifier . '" does not contain a "numerus" definition.', 1562676185);
        }

        if (empty($routeDefinition['resourceType'])) {
            throw new \InvalidArgumentException('The resource route "' . $routeIdentifier . '" does not contain a "resourceType" definition.', 1562682478);
        }

        $resourcesFromPackages = ConfigurationManager::getResourcesConfiguration();
        $resourceType = $routeDefinition['resourceType'];

        if (!isset($resourcesFromPackages[$resourceType])) {
            throw new \InvalidArgumentException('The resource route "' . $routeIdentifier . '" references a resource definition "' . $resourceType . '" that does not exist.', 1562676185);
        }

        $resourceDefinition = $resourcesFromPackages[$resourceType];

        if (empty($resourceDefinition['repository'])) {
            throw new \InvalidArgumentException('The resource definition "' . $resourceType . '" does not contain a "repository" definition.', 1563206745);
        }

        if (empty($resourceDefinition['transformer'])) {
            throw new \InvalidArgumentException('The resource definition "' . $resourceType . '" does not contain a "transformer" definition.', 1563206747);
        }

        switch ($routeDefinition['numerus']) {
            case Numerus::ITEM:
                $controllerName = ItemController::class;
                break;
            case Numerus::COLLECTION:
                $controllerName = CollectionController::class;
                break;
            default:
                throw new \InvalidArgumentException('The resource route "' . $routeIdentifier . '" does contain an invalid "numerus" definition "' . $routeDefinition['numerus'] . '" of type "' . Numerus::class . '".', 1562676282);
        }

        $controllerOptions = [
            'resourceType' => $resourceType,
            'repository' => $resourceDefinition['repository'],
            'transformer' => $resourceDefinition['transformer'],
            'serializer' => $routeDefinition['serializer'] ?? null,
            'operationToCommandMap' => $resourceDefinition['operationToCommandMap'] ?? [],
        ];

        /** @var AbstractResourceController $controller */
        $controller = (new Cascader())->create($controllerName, $controllerOptions);
        return $controller;
    }
}
