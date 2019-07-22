<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Controller;

use Cascader\Cascader;
use DFAU\ToujouApi\Command\IncludedResourcesCommand;
use DFAU\ToujouApi\Command\OriginalIncludedResourcesCommand;
use DFAU\ToujouApi\Command\OriginalTcaResourceDataCommand;
use DFAU\ToujouApi\Command\ResourceReferencingCommand;
use DFAU\ToujouApi\Command\TcaResourceDataCommand;
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

    public function issueCommandForOperation(Operation $operation, ServerRequestInterface $request): ResponseInterface
    {
        $operationName = (string) $operation;
        if (!isset($this->operationToCommandMap[$operationName])) {
            throw new \InvalidArgumentException('The given operation "' . $operationName . '" is not supported by the resource type "' . $this->resourceType . '"', 1563793877);
        }

        $commandInterfaces = array_fill_keys(class_implements($this->operationToCommandMap[$operationName]), true);

        if (!in_array(ResourceReferencingCommand::class, $commandInterfaces)) {
            throw new \InvalidArgumentException('This numerus "' . Numerus::ITEM . '" only issues commands implementing at least "' . ResourceReferencingCommand::class . '". Command "'. $this->operationToCommandMap[$operationName] .'" has been given.',1563801127);
        }

        $resourceIdentifier = $request->getAttribute('variables')['id'] ?: '';
        $commandArguments = [
            'resourceIdentifier' => $resourceIdentifier,
            'resourceType' => $this->resourceType,
        ];

        if (isset($commandInterfaces[TcaResourceDataCommand::class]) || isset($commandInterfaces[IncludedResourcesCommand::class])) {
            [$commandArguments['resourceData'], $commandArguments['includedResources']] = $this->deserializeBody($request->getParsedBody());
        }

        if (isset($commandInterfaces[OriginalTcaResourceDataCommand::class]) || isset($commandInterfaces[OriginalIncludedResourcesCommand::class])) {
            $this->parseIncludes($request->getQueryParams());
            [$commandArguments['originalResourceData'], $commandArguments['originalIncludedResources']] = $this->deserializeBody($this->fetchAndTransformData($resourceIdentifier));
        }

        $command = (new Cascader())->create($this->operationToCommandMap[$operationName], $commandArguments);
    }

    protected function fetchAndTransformData(string $resourceIdentifier): array
    {
        $resource = $this->repository->findOneByIdentifier($resourceIdentifier);

        $item = new Item($resource, $this->transformer, $this->resourceType);

        return $this->fractal->createData($item)->toArray();
    }

    protected function deserializeBody(array $body): array
    {
        $data = $this->deserializer->item($body);
        $resource = array_shift($data);
        $includes = $data;

        return [$resource, $includes];
    }
}
