<?php
declare(strict_types=1);

namespace DFAU\ToujouApi\Controller;

use League\Fractal\ParamBag;
use League\Fractal\Resource\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;

class CollectionController extends AbstractResourceController
{

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = new ParamBag($request->getQueryParams());
        if (isset($queryParams['include'])) {
            $this->fractal->parseIncludes($queryParams['include']);
        }
        if (isset($queryParams['exclude'])) {
            $this->fractal->parseIncludes($queryParams['exclude']);
        }

        $currentCursor = $queryParams['cursor'];
        $previousCursor = $queryParams['previous'];
        $limit = $queryParams['limit'] ? (int)$queryParams['limit'] : 10;

        [$resources, $cursor] = $this->repository->findWithCursor($limit, $currentCursor, $previousCursor);

        $collection = new Collection($resources, $this->transformer, $this->resourceKey);
        $collection->setCursor($cursor);

        return new JsonResponse($this->fractal->createData($collection)->toArray());
    }
}
