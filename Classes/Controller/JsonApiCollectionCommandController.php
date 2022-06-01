<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Controller;

use DFAU\ToujouApi\Domain\Repository\ApiResourceRepository;
use DFAU\ToujouApi\Resource\Operation;
use DFAU\ToujouApi\Transformer\ResourceTransformerInterface;
use League\Fractal\ParamBag;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\JsonApiSerializer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;

final class JsonApiCollectionCommandController extends AbstractResourceCommandController
{
    public function __construct(?string $resourceType, ApiResourceRepository $repository, ResourceTransformerInterface $transformer)
    {
        parent::__construct($resourceType, $repository, $transformer);
        $this->fractal->setSerializer(new class() extends JsonApiSerializer {
            public function getMandatoryFields(): array
            {
                return ['id', 'meta'];
            }
        });
    }

    public function canHandleOperation(Operation $operation): bool
    {
        return $operation->equals(Operation::READ);
        // TODO implement creation on collection without given identifier
         //   || $operation->equals(Operation::CREATE);
    }

    public function read(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = new ParamBag($request->getQueryParams());
        $this->parseIncludes($queryParams);
        $this->parseFieldsets($queryParams);

        $pageParams = $queryParams['page'] ?? [];
        $currentCursor = $pageParams['cursor'];
        $previousCursor = $pageParams['previous'];
        $limit = $pageParams['limit'] ? (int) $pageParams['limit'] : 10;

        $filters = isset($queryParams['filter']) && \is_array($queryParams['filter']) ? $queryParams['filter'] : [];

        $data = $this->fetchAndTransformData($filters, $limit, $currentCursor, $previousCursor);

        return new JsonResponse($data);
    }

    protected function fetchAndTransformData(array $filters, int $limit, $currentCursor, $previousCursor): ?array
    {
        [$resources, $cursor] = $this->repository->findByFiltersWithCursor($filters, $limit, $currentCursor, $previousCursor);

        $collection = new Collection($resources, $this->transformer, $this->resourceType);
        $collection->setCursor($cursor);

        return $this->fractal->createData($collection)->toArray();
    }

    protected function fillInCommandArguments(ServerRequestInterface $request, string $commandName, array $commandArguments, array $commandInterfaces): array
    {
        return $commandArguments;
    }
}
