<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Controller;


use DFAU\ToujouApi\Deserializer\JsonApiDeserializer;
use DFAU\ToujouApi\Domain\Repository\ApiResourceRepository;
use DFAU\ToujouApi\Resource\Operation;
use DFAU\ToujouApi\Transformer\ResourceTransformerInterface;
use League\Fractal\Manager;
use League\Fractal\ParamBag;
use League\Fractal\Serializer\JsonApiSerializer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractResourceController
{

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
     * @var array
     */
    protected $operationToCommandMap = [];

    public function __construct(
        ?string $resourceType,
        ApiResourceRepository $repository,
        ResourceTransformerInterface $transformer,
        array $operationToCommandMap = [])
    {
        $this->resourceType = $resourceType;
        $this->repository = $repository;
        $this->transformer = $transformer;
        $this->operationToCommandMap = $operationToCommandMap;

        $this->fractal = new Manager();
        $this->fractal->setSerializer(new JsonApiSerializer());

        $this->deserializer = GeneralUtility::makeInstance(JsonApiDeserializer::class);
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

    abstract public function read(ServerRequestInterface $request): ResponseInterface;

    abstract public function issueCommandForOperation(Operation $operation, ServerRequestInterface $request): ResponseInterface;

}
