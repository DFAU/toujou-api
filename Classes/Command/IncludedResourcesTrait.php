<?php


namespace DFAU\ToujouApi\Command;


trait IncludedResourcesTrait
{

    /**
     * @var array
     */
    protected $includedResources = [];

    public function getIncludedResources(): array
    {
        return $this->includedResources;
    }
}
