<?php
declare(strict_types=1);

namespace DFAU\ToujouApi\Controller;

use League\Fractal\Resource\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;

final class CollectionController extends AbstractResourceController
{

    public function read(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $this->parseIncludes($queryParams);

        $currentCursor = $queryParams['cursor'];
        $previousCursor = $queryParams['previous'];
        $limit = $queryParams['limit'] ? (int)$queryParams['limit'] : 10;

        $data = $this->fetchAndTransform($limit, $currentCursor, $previousCursor);

        return new JsonResponse($data);
    }

    protected function fetchAndTransform(int $limit, $currentCursor, $previousCursor): array
    {
        [$resources, $cursor] = $this->repository->findWithCursor($limit, $currentCursor, $previousCursor);

        $collection = new Collection($resources, $this->transformer, $this->resourceType);
        $collection->setCursor($cursor);

        return $this->fractal->createData($collection)->toArray();
    }
}
