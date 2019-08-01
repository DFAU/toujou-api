<?php
declare(strict_types=1);

namespace DFAU\ToujouApi\Http;


use Cascader\Cascader;
use DFAU\ToujouApi\Configuration\ConfigurationManager;
use DFAU\ToujouApi\Controller\AbstractResourceCommandController;
use DFAU\ToujouApi\Controller\CollectionCommandController;
use DFAU\ToujouApi\Controller\ItemCommandController;
use DFAU\ToujouApi\Controller\ResourceControllerFactory;
use DFAU\ToujouApi\Resource\Numerus;
use DFAU\ToujouApi\Resource\Operation;
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

        // Build Route objects from the data
        $router = simpleDispatcher(function (RouteCollector $routeCollector) use ($routesFromPackages) {
            forEach ($routesFromPackages as $routeIdentifier => $resourceRouteDefinition) {
                [$method, $path] = explode(':', $routeIdentifier, 2);
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

                    switch (strtolower($resourceRouteDefinition['operation'])) {
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
