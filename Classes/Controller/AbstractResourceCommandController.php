<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Controller;


use Cascader\Cascader;
use DFAU\Convergence\Schema;
use DFAU\ToujouApi\Command\AsIsResourceDataCommand;
use DFAU\ToujouApi\Command\ResourceDataCommand;
use DFAU\ToujouApi\Command\ResourceReferencingCommand;
use DFAU\ToujouApi\CommandBus\CommandBusFactory;
use DFAU\ToujouApi\Deserializer\JsonApiDeserializer;
use DFAU\ToujouApi\Domain\Repository\ApiResourceRepository;
use DFAU\ToujouApi\Resource\Operation;
use DFAU\ToujouApi\Resource\ResourceOperationToCommandMap;
use DFAU\ToujouApi\Transformer\ResourceTransformerInterface;
use League\Fractal\Manager;
use League\Fractal\ParamBag;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractResourceCommandController
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
     * @var ResourceOperationToCommandMap
     */
    protected $resourceOperationToCommandMap;

    /**
     * @var string
     */
    protected $resourceType;

    public function __construct(
        ?string $resourceType,
        ApiResourceRepository $repository,
        ResourceTransformerInterface $transformer
    )
    {
        $this->resourceType = $resourceType;
        $this->repository = $repository;
        $this->transformer = $transformer;

        $this->commandBus = CommandBusFactory::createFromCommandConfiguration();
        $this->deserializer = GeneralUtility::makeInstance(JsonApiDeserializer::class);
        $this->fractal = new Manager();
        $this->fractal->setSerializer(new JsonApiSerializer());
        $this->resourceOperationToCommandMap = GeneralUtility::makeInstance(ResourceOperationToCommandMap::class);
        $this->objectFactory = new Cascader();
    }


    public function issueCommandForOperation(Operation $operation, ServerRequestInterface $request): ResponseInterface
    {
        [$commandName, $commandArguments, $commandInterfaces] = $this->resourceOperationToCommandMap->getCommandConfigForResourceTypeAndOperation(
            $this->resourceType,
            $operation,
            [ResourceReferencingCommand::class]
        );

        $resourceIdentifier = $request->getAttribute('variables')['id'] ?: '';
        $commandArguments['resourceIdentifier'] = $resourceIdentifier;
        $commandArguments['resourceType'] = $this->resourceType;

        $this->parseIncludes($request->getQueryParams());
        $includes = $this->fractal->getRequestedIncludes();



        if (in_array(AsIsResourceDataCommand::class, $commandInterfaces)) {
            $commandArguments['asIsResourceData'] = $this->deserializeResourceData($this->fetchAndTransformData($resourceIdentifier));
        }

        if (in_array(ResourceDataCommand::class, $commandInterfaces)) {
            $commandArguments['resourceData'] = $this->deserializeResourceData($request->getParsedBody());
        }

        $command = $this->objectFactory->create($commandName, $commandArguments);
        $this->commandBus->handle($command);

        return new Response('php://temp', 202, ['Content-Type' => 'application/vnd.api+json; charset=utf-8']);
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

    abstract protected function deserializeResourceData(array $resourceData): array;

    abstract protected function fetchAndTransformData(string $resourceIdentifier): array;

    abstract public function read(ServerRequestInterface $request): ResponseInterface;

}
