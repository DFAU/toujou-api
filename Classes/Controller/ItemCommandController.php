<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Controller;

use DFAU\ToujouApi\Resource\Numerus;
use League\Fractal\ParamBag;
use League\Fractal\Resource\Item;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;

final class ItemCommandController extends AbstractResourceCommandController
{

    const CONTROLLER_NUMERUS = Numerus::ITEM;

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
}
