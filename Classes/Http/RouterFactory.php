<?php
declare(strict_types=1);

namespace DFAU\ToujouApi\Http;


use Cascader\Cascader;
use DFAU\ToujouApi\Configuration\ConfigurationManager;
use DFAU\ToujouApi\Controller\CollectionController;
use DFAU\ToujouApi\Controller\ItemController;
use FastRoute\RouteCollector;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Psr\Http\Message\ServerRequestInterface;
use function FastRoute\simpleDispatcher;

class RouterFactory
{

    static public function createToujouApiRouter()
    {
        $routesFromPackages = ConfigurationManager::getRoutesConfiguration();
        $resourcesFromPackages = ConfigurationManager::getResourcesConfiguration();

        // Build Route objects from the data
        $router = simpleDispatcher(function (RouteCollector $routeCollector) use ($routesFromPackages, $resourcesFromPackages) {
            forEach ($routesFromPackages as $routeIdentifier => $resourceRouteDefinition) {
                [$method, $path] = explode(':', $routeIdentifier, 2);
                if (empty($method)) {
                    throw new \InvalidArgumentException('The resource route "' . $routeIdentifier . '" does not contain a HTTP method definition like "GET:/xyz/".', 1562676017);
                }
                if (empty($path)) {
                    throw new \InvalidArgumentException('The resource route "' . $routeIdentifier . '" does contain a "path" definition like "GET:/xyz/".', 1560780540);
                }

                $handler = function (ServerRequestInterface $request) use ($routeIdentifier, $resourceRouteDefinition, $resourcesFromPackages) {
                    if (empty($resourceRouteDefinition['resourceType'])) {
                        throw new \InvalidArgumentException('The resource route "' . $routeIdentifier . '" does not contain a "resourceType" definition.', 1562676185);
                    }

                    if (empty($resourceRouteDefinition['resourceKey'])) {
                        throw new \InvalidArgumentException('The resource route "' . $routeIdentifier . '" does not contain a "resourceKey" definition.', 1562682478);
                    }

                    $resourceKey = $resourceRouteDefinition['resourceKey'];

                    if (!isset($resourcesFromPackages[$resourceKey])) {
                        throw new \InvalidArgumentException('The resource route "' . $routeIdentifier . '" references a resource definition "' . $resourceKey . '" that does not exist.', 1562676185);
                    }

                    $resourceDefinition = $resourcesFromPackages[$resourceKey];

                    if (empty($resourceDefinition['repository'])) {
                        throw new \InvalidArgumentException('The resource definition "' . $resourceKey . '" does not contain a "repository" definition.', 1563206745);
                    }

                    if (empty($resourceDefinition['transformer'])) {
                        throw new \InvalidArgumentException('The resource definition "' . $resourceKey . '" does not contain a "transformer" definition.', 1563206747);
                    }

                    switch ($resourceRouteDefinition['resourceType']) {
                        case Item::class:
                            $controllerName = ItemController::class;
                            break;
                        case Collection::class:
                            $controllerName = CollectionController::class;
                            break;
                        default:
                            throw new \InvalidArgumentException('The resource route "' . $routeIdentifier . '" does contain an invalid "resourceType" definition "' . $resourceRouteDefinition['resourceType'] . '". Allowed types are "' . Collection::class . '" or "' . Item::class . '".', 1562676282);
                    }

                    $controllerOptions = [
                        'repository' => $resourceDefinition['repository'],
                        'transformer' => $resourceDefinition['transformer'],
                        'resourceKey' => $resourceKey,
                        'serializer' => $resourceRouteDefinition['serializer'] ?? null
                    ];

                    $controller = (new Cascader())->create($controllerName, $controllerOptions);
                    $methodName = strtolower($request->getMethod());
                    if (!method_exists($controller, $methodName)) {
                        throw new \InvalidArgumentException('The resource controller for route "' . $routeIdentifier . '" does not support the given HTTP request method "' . $request->getMethod() . '".', 1562680637);
                    }

                    return $controller->{$methodName}($request);
                };

                $routeCollector->addRoute($method, $path, $handler);
            }
        });

        return $router;
    }

}
