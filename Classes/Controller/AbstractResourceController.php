<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Controller;


use DFAU\ToujouApi\Domain\Repository\ApiResourceRepository;
use DFAU\ToujouApi\Transformer\ResourceTransformerInterface;
use League\Fractal\Manager;
use League\Fractal\Serializer\SerializerAbstract;

abstract class AbstractResourceController
{

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ApiResourceRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $resourceKey;

    public function __construct(ApiResourceRepository $repository, ResourceTransformerInterface $transformer, ?string $resourceKey, SerializerAbstract $serializer = null)
    {
        $this->repository = $repository;
        $this->transformer = $transformer;
        $this->resourceKey = $resourceKey;

        $this->fractal = new Manager();
        if ($serializer) {
            $this->fractal->setSerializer($serializer);
        }
    }

}
