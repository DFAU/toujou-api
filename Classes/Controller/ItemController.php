<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Controller;

use League\Fractal\ParamBag;
use League\Fractal\Resource\Item;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;

class ItemController extends AbstractResourceController
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

        $resource = $this->repository->findOneByIdentifier($request->getAttribute('variables')['id']);

        $item = new Item($resource, $this->transformer, $this->resourceKey);

        return new JsonResponse($this->fractal->createData($item)->toArray(), 200, ['Content-Type' => 'application/vnd.api+json; charset=utf-8']);
    }

}
