<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Controller;

use DFAU\ToujouApi\Command\ResourceReferencingCommand;
use DFAU\ToujouApi\Command\TcaResourceCommand;
use DFAU\ToujouApi\Command\UnitOfWorkCommand;
use DFAU\ToujouApi\CommandBus\CommandBusFactory;
use DFAU\ToujouApi\Resource\Numerus;
use DFAU\ToujouApi\Resource\Operation;
use League\Fractal\Resource\Item;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;

final class ItemController extends AbstractResourceController
{

    public function read(ServerRequestInterface $request): ResponseInterface
    {
        $this->parseIncludes($request->getQueryParams());

        $data = $this->fetchAndTransformData($request->getAttribute('variables')['id']);

        return new JsonResponse($data, 200, ['Content-Type' => 'application/vnd.api+json; charset=utf-8']);
    }

    public function replace(ServerRequestInterface $request): ResponseInterface
    {
        [$commandName, $commandArguments] = $this->getCommandConfigForResourceTypeAndOperation($this->resourceType, new Operation(Operation::REPLACE), [UnitOfWorkCommand::class]);

        $this->parseIncludes($request->getQueryParams());
        $commandArguments['unitOfWork'] = $this->compareRequestBodyToExistingResource($request);

        $command = $this->objectFactory->create($commandName, $commandArguments);

        $this->commandBus->handle($command);

        return new JsonResponse([], 202, ['Content-Type' => 'application/vnd.api+json; charset=utf-8']);
    }

//    public function issueCommandForOperation(Operation $operation, ServerRequestInterface $request): ResponseInterface
//    {
//        $operationName = (string) $operation;
//        if (!isset($this->operationToCommandMap[$operationName])) {
//            throw new \InvalidArgumentException('The given operation "' . $operationName . '" is not supported by the resource type "' . $this->resourceType . '"', 1563793877);
//        }
//
//        $commandName = $this->operationToCommandMap[$operationName]['__class__'] ?? $this->operationToCommandMap[$operationName];
//        $commandInterfaces = array_fill_keys(class_implements($commandName), true);
//
//        if (!in_array(ResourceReferencingCommand::class, $commandInterfaces)) {
//            throw new \InvalidArgumentException('This numerus "' . Numerus::ITEM . '" only issues commands implementing at least "' . ResourceReferencingCommand::class . '". Command "'. $this->operationToCommandMap[$operationName] .'" has been given.',1563801127);
//        }
//
//        $resourceIdentifier = $request->getAttribute('variables')['id'] ?: '';
//        $commandArguments = [
//            'resourceIdentifier' => $resourceIdentifier,
//            'resourceType' => $this->resourceType,
//        ];
//
//        if (isset($commandInterfaces[TcaResourceCommand::class]) || isset($commandInterfaces[IncludedResourcesCommand::class])) {
//            [$commandArguments['resourceData'], $commandArguments['includedResources']] = $this->deserializeBody($request->getParsedBody());
//        }
//
//        if (isset($commandInterfaces[OriginalTcaResourceDataCommand::class]) || isset($commandInterfaces[OriginalIncludedResourcesCommand::class])) {
//            $this->parseIncludes($request->getQueryParams());
//            [$commandArguments['originalResourceData'], $commandArguments['originalIncludedResources']] = $this->deserializeBody($this->fetchAndTransformData($resourceIdentifier));
//        }
//
//
//
//        if (is_array($this->operationToCommandMap[$operationName]) && isset($this->operationToCommandMap[$operationName]['__class__'])) {
//            $commandArguments = array_merge($this->operationToCommandMap[$operationName], $commandArguments);
//            unset($commandArguments['__class__']);
//        }
//
//        $command = $this->objectFactory->create($commandName, $commandArguments);
//
//        $commandBus = CommandBusFactory::createFromCommandConfiguration();
//        $commandBus->handle($command);
//    }

    protected function fetchAndTransformData(string $resourceIdentifier): array
    {
        $resource = $this->repository->findOneByIdentifier($resourceIdentifier);

        $item = new Item($resource, $this->transformer, $this->resourceType);

        return $this->fractal->createData($item)->toArray();
    }
}
