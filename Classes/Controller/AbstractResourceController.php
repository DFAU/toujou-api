<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Controller;


use Cascader\Cascader;
use DFAU\Convergence\Operations\AbstractResourceOperation;
use DFAU\Convergence\Operations\AddResource;
use DFAU\Convergence\Operations\RemoveResource;
use DFAU\Convergence\Operations\UpdateResource;
use DFAU\Convergence\OperationsFactory;
use DFAU\Convergence\Schema;
use DFAU\ToujouApi\Command\ResourceReferencingCommand;
use DFAU\ToujouApi\CommandBus\CommandBusFactory;
use DFAU\ToujouApi\Deserializer\JsonApiDeserializer;
use DFAU\ToujouApi\Domain\Repository\ApiResourceRepository;
use DFAU\ToujouApi\Resource\Numerus;
use DFAU\ToujouApi\Resource\Operation;
use DFAU\ToujouApi\Resource\ResourceOperationToCommandMap;
use DFAU\ToujouApi\Transformer\ResourceTransformerInterface;
use League\Fractal\Manager;
use League\Fractal\ParamBag;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractResourceController
{

    /**
     * @var Cascader
     */
    protected $objectFactory;

    /**
     * @var CommandBus
     */
    protected $commandBus;

    /**
     * @var Schema
     */
    protected $convergenceSchema;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var JsonApiDeserializer
     */
    protected $deserializer;

    /**
     * @var ApiResourceRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $resourceType;

    /**
     * @var ResourceOperationToCommandMap
     */
    protected $operationToCommandMap;

    public function __construct(
        ?string $resourceType,
        ApiResourceRepository $repository,
        ResourceTransformerInterface $transformer,
        ResourceOperationToCommandMap $operationToCommandMap,
        Schema $convergenceSchema = null
    )
    {
        $this->resourceType = $resourceType;
        $this->repository = $repository;
        $this->transformer = $transformer;
        $this->operationToCommandMap = $operationToCommandMap;
        $this->convergenceSchema = $convergenceSchema;

        $this->commandBus = CommandBusFactory::createFromCommandConfiguration();
        $this->deserializer = GeneralUtility::makeInstance(JsonApiDeserializer::class);
        $this->fractal = new Manager();
        $this->fractal->setSerializer(new JsonApiSerializer());
        $this->objectFactory = new Cascader();
    }

    protected function getCommandConfigForResourceTypeAndOperation(string $resourceType, Operation $operation, array $requiredInterfaces = []): array
    {
        $commandConfig = $this->operationToCommandMap->findCommandByResourceTypeAndOperation($resourceType, $operation);

        if ($commandConfig === null) {
            throw new \InvalidArgumentException('The given operation "' . $operation . '" is not supported by the resource type "' . $resourceType . '"', 1563793877);
        }

        $commandConfig = is_array($commandConfig) ? $commandConfig : ['__class__' => $commandConfig];
        $commandInterfaces = array_fill_keys(class_implements($commandConfig['__class__']), true);

        $requiredInterfaces[] = ResourceReferencingCommand::class;

        if (!in_array($requiredInterfaces, $commandInterfaces)) {
            throw new \InvalidArgumentException('The command "' . $commandConfig['__class__'] . '" needs to implement these inferfaces "' . implode(', ', $requiredInterfaces) . '" in order to be used for the resource type "' . $resourceType . '" and operation "' . $operation . '".', 1563801127);
        }

        $commandName = $commandConfig['__class__'];
        $commandArguments = $commandConfig;
        unset($commandArguments['__class__']);

        return [$commandName, $commandArguments];
    }

    protected function parseIncludes(array $queryParams): void
    {
        $queryParams = new ParamBag($queryParams);
        if (isset($queryParams['include'])) {
            $this->fractal->parseIncludes($queryParams['include']);
        }
        if (isset($queryParams['exclude'])) {
            $this->fractal->parseIncludes($queryParams['exclude']);
        }
    }

    protected function compareRequestBodyToExistingResource(ServerRequestInterface $request): array
    {
        if ($this->convergenceSchema === null) {
            throw new \InvalidArgumentException('Cannot fullfill a replace operation without a defined convergence schema for resource type "' . $this->resourceType . '".', 1564049645);
        }

        $resourceIdentifier = $request->getAttribute('variables')['id'] ?: '';

        $toBeResources = $this->deserializer->item($request->getParsedBody());
        $asIsResources = $this->deserializer->item($this->fetchAndTransformData($resourceIdentifier));

        $operations = GeneralUtility::makeInstance(OperationsFactory::class)->buildFromSchemaAndResources($this->convergenceSchema, $toBeResources, $asIsResources);

        $convergenceOperationsToApiOperationsMap = [
            AddResource::class => new Operation(Operation::CREATE),
            UpdateResource::class => new Operation(Operation::UPDATE),
            RemoveResource::class => new Operation(Operation::DELETE),
        ];

        $unitOfWork = array_map(function (AbstractResourceOperation $operation) use($convergenceOperationsToApiOperationsMap) {
            $resource = $operation->getResource();
            switch ($operation) {
                case $operation instanceof AddResource:
                    [$commandName, $commandArguments] = $this->getCommandConfigForResourceTypeAndOperation($resource['type'], $convergenceOperationsToApiOperationsMap[AddResource::class]);
                    $commandArguments['resourceData'] = $resource['attributes'];
                    break;
                case $operation instanceof UpdateResource:
                    [$commandName, $commandArguments] = $this->getCommandConfigForResourceTypeAndOperation($resource['type'], $convergenceOperationsToApiOperationsMap[UpdateResource::class]);
                    $commandArguments['resourceData'] = $operation->getResourceUpdates()['attributes'];
                    break;
                case $operation instanceof RemoveResource:
                    [$commandName, $commandArguments] = $this->getCommandConfigForResourceTypeAndOperation($resource['type'], $convergenceOperationsToApiOperationsMap[RemoveResource::class]);
                    $commandArguments['resourceData'] = $resource['attributes'];
                    break;
                default:
                    throw new \BadMethodCallException('The given operation "' . get_class($operation) . '" is not supported yet', 1564062825);
                    break;
            }

            $commandArguments['resourceIdentifier'] = $resource['id'];
            $commandArguments['resourceType'] = $resource['type'];

            return $this->objectFactory->create($commandName, $commandArguments);
        }, $operations);
        return $unitOfWork;
    }

    abstract protected function fetchAndTransformData(string $resourceIdentifier): array;

    abstract public function read(ServerRequestInterface $request): ResponseInterface;

//    abstract public function issueCommandForOperation(Operation $operation, ServerRequestInterface $request): ResponseInterface;

}
