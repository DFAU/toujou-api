<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Controller;

use DFAU\ToujouApi\Command\AsIsResourceDataCommand;
use DFAU\ToujouApi\Command\ResourceDataCommand;
use DFAU\ToujouApi\Deserializer\JsonApiDeserializer;
use DFAU\ToujouApi\Domain\Repository\ApiResourceRepository;
use DFAU\ToujouApi\Resource\Operation;
use DFAU\ToujouApi\Transformer\ResourceTransformerInterface;
use League\Fractal\ParamBag;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\JsonApiSerializer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class JsonApiItemCommandController extends AbstractResourceCommandController
{

    /**
     * @var JsonApiDeserializer
     */
    protected $deserializer;

    public function __construct(?string $resourceType, ApiResourceRepository $repository, ResourceTransformerInterface $transformer)
    {
        parent::__construct($resourceType, $repository, $transformer);
        $this->fractal->setSerializer(new class extends JsonApiSerializer {
            public function getMandatoryFields() { return ['id', 'meta']; }
        });
        $this->deserializer = GeneralUtility::makeInstance(JsonApiDeserializer::class);
    }


    public function canHandleOperation(Operation $operation): bool
    {
        return $operation->equals(Operation::READ)
        || $operation->equals(Operation::CREATE)
        || $operation->equals(Operation::REPLACE)
        || $operation->equals(Operation::UPDATE)
        || $operation->equals(Operation::DELETE);
    }

    public function read(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = new ParamBag($request->getQueryParams());
        $this->parseIncludes($queryParams);
        $this->parseFieldsets($queryParams);

        $data = $this->fetchAndTransformData($request->getAttribute('variables')['id']);

        return new JsonResponse($data, 200, ['Content-Type' => 'application/vnd.api+json; charset=utf-8']);
    }

    protected function fetchAndTransformData(string $resourceIdentifier): ?array
    {
        $resource = $this->repository->findOneByIdentifier($resourceIdentifier);

        if ($resource === null) {
            return null;
        }

        $item = new Item($resource, $this->transformer, $this->resourceType);

        return $this->fractal->createData($item)->toArray();
    }

    protected function fillInCommandArguments(ServerRequestInterface $request, string $commandName, array $commandArguments, array $commandInterfaces): array
    {
        $resourceIdentifier = $request->getAttribute('variables')['id'] ?: '';
        $commandArguments['resourceIdentifier'] = $resourceIdentifier;
        $commandArguments['resourceType'] = $this->resourceType;

        if (in_array(AsIsResourceDataCommand::class, $commandInterfaces)) {
            $asIsResourceData = $this->fetchAndTransformData($resourceIdentifier);
            $commandArguments['asIsResourceData'] = $asIsResourceData ? $this->deserializer->item($asIsResourceData, $this->deserializer::OPTION_KEEP_META) : null;
        }

        if (in_array(ResourceDataCommand::class, $commandInterfaces)) {
            $resourceData = $request->getParsedBody();
            $commandArguments['resourceData'] = $resourceData ? $this->deserializer->item($resourceData) : null;
        }
        return $commandArguments;
    }
}
