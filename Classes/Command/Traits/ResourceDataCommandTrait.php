<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Command\Traits;


trait ResourceDataCommandTrait
{

    /**
     * @var array
     */
    protected $resourceData;

    public function getResourceData(): array
    {
        return $this->resourceData;
    }

}
