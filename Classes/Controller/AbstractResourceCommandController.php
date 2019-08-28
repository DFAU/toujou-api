<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Controller;


use Cascader\Cascader;
use DFAU\ToujouApi\Command\ResourceReferencingCommand;
use DFAU\ToujouApi\CommandBus\CommandBusFactory;
use DFAU\ToujouApi\Domain\Repository\ApiResourceRepository;
use DFAU\ToujouApi\Resource\Operation;
use DFAU\ToujouApi\Resource\ResourceOperationToCommandMap;
use DFAU\ToujouApi\Resource\UnsupportedOperationException;
use DFAU\ToujouApi\Transformer\ResourceTransformerInterface;
use League\Fractal\Manager;
use League\Fractal\ParamBag;
use League\Tactician\CommandBus;
use Middlewares\Utils\HttpErrorException;
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
     * @var Manager
     */
    protected $fractal;

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
        $this->fractal = new Manager();
        $this->resourceOperationToCommandMap = GeneralUtility::makeInstance(ResourceOperationToCommandMap::class);
        $this->objectFactory = new Cascader();
    }

    abstract public function canHandleOperation(Operation $operation): bool;

    abstract public function read(ServerRequestInterface $request): ResponseInterface;

    abstract protected function fillInCommandArguments(ServerRequestInterface $request, string $commandName, array $commandArguments, array $commandInterfaces): array;

    public function issueCommandForOperation(Operation $operation, ServerRequestInterface $request): ResponseInterface
    {
        try {
            [$commandName, $commandArguments, $commandInterfaces] = $this->resourceOperationToCommandMap->getCommandConfigForResourceTypeAndOperation(
                $this->resourceType,
                $operation,
                [ResourceReferencingCommand::class]
            );

            $queryParams = new ParamBag($request->getQueryParams());
            $this->parseIncludes($queryParams);
            $this->parseFieldsets($queryParams);

            $commandArguments = $this->fillInCommandArguments($request, $commandName, $commandArguments, $commandInterfaces);

            $command = $this->objectFactory->create($commandName, $commandArguments);
            $this->commandBus->handle($command);
        } catch(UnsupportedOperationException $exception) {
            throw HttpErrorException::create(405, [], $exception);
        }

        return new Response('php://temp', 202, ['Content-Type' => 'application/vnd.api+json; charset=utf-8']);
    }

    protected function parseIncludes(ParamBag $queryParams): void
    {
        if (isset($queryParams['include'])) {
            $this->fractal->parseIncludes($queryParams['include']);
        }
        if (isset($queryParams['exclude'])) {
            $this->fractal->parseIncludes($queryParams['exclude']);
        }
    }

    protected function parseFieldsets(ParamBag $queryParams): void
    {
        if (isset($queryParams['fields'])) {
            $this->fractal->parseFieldsets($queryParams['fields']);
        }
    }

}
