<?php
declare(strict_types=1);

namespace DFAU\ToujouApi\Controller;

use DFAU\ToujouApi\Resource\Numerus;
use League\Fractal\Resource\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;

final class CollectionCommandController extends AbstractResourceCommandController
{

    const CONTROLLER_NUMERUS = Numerus::COLLECTION;

    public function read(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $this->parseIncludes($queryParams);

        $currentCursor = $queryParams['cursor'];
        $previousCursor = $queryParams['previous'];
        $limit = $queryParams['limit'] ? (int)$queryParams['limit'] : 10;

        $data = $this->fetchAndTransformData($limit, $currentCursor, $previousCursor);

        return new JsonResponse($data);
    }

    protected function deserializeResourceData(array $resourceData): array
    {
        return $this->deserializer->collection($resourceData);
    }

    protected function fetchAndTransformData(int $limit, $currentCursor, $previousCursor): ?array
    {
        [$resources, $cursor] = $this->repository->findWithCursor($limit, $currentCursor, $previousCursor);

        $collection = new Collection($resources, $this->transformer, $this->resourceType);
        $collection->setCursor($cursor);

        return $this->fractal->createData($collection)->toArray();
    }
}
