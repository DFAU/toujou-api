<?php


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
