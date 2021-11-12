<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Http;

use DFAU\ToujouApi\Configuration\ConfigurationManager;
use DFAU\ToujouApi\Controller\ResourceControllerFactory;
use DFAU\ToujouApi\Resource\Operation;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use Psr\Http\Message\ServerRequestInterface;

class RouterFactory
{
    public static function createToujouApiRouter()
    {
        $routesFromPackages = ConfigurationManager::getRoutesConfiguration();

        // Build Route objects from the data
        $router = simpleDispatcher(function (RouteCollector $routeCollector) use ($routesFromPackages) {
            foreach ($routesFromPackages as $routeIdentifier => $resourceRouteDefinition) {
                [$method, $path] = \explode(':', $routeIdentifier, 2);
                if (empty($method)) {
                    throw new \InvalidArgumentException('The resource route "' . $routeIdentifier . '" does not contain a HTTP method definition like "GET:/xyz/".', 1562676017);
                }
                if (empty($path)) {
                    throw new \InvalidArgumentException('The resource route "' . $routeIdentifier . '" does contain a "path" definition like "GET:/xyz/".', 1560780540);
                }

                $handler = function (ServerRequestInterface $request) use ($routeIdentifier, $resourceRouteDefinition) {
                    $controller = ResourceControllerFactory::createFromRouteDefintinion($routeIdentifier, $resourceRouteDefinition);

                    if (empty($resourceRouteDefinition['operation'])) {
                        throw new \InvalidArgumentException('The resource route "' . $routeIdentifier . '" does not contain a "operation" definition.', 1563782786);
                    }

                    if (!empty($resourceRouteDefinition['defaultParams'])) {
                        $queryParams = \array_replace_recursive($resourceRouteDefinition['defaultParams'], $request->getQueryParams());
                        $request = $request->withQueryParams($queryParams);
                    }

                    switch (\strtolower($resourceRouteDefinition['operation'])) {
                        case Operation::READ:
                            return $controller->read($request);
                        case Operation::REPLACE:
                        case Operation::CREATE:
                        case Operation::UPDATE:
                        case Operation::DELETE:
                            return $controller->issueCommandForOperation(new Operation($resourceRouteDefinition['operation']), $request);
                        default:
                            throw new \InvalidArgumentException('The resource route "' . $routeIdentifier . '" does contain an invalid "operation" definition "' . $resourceRouteDefinition['operation'] . '" of type "' . Operation::class . '".', 1563782857);
                    }
                };

                $routeCollector->addRoute($method, $path, $handler);
            }
        });

        return $router;
    }
}
