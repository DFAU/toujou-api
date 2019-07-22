<?php


namespace DFAU\ToujouApi\Command;


trait OriginalIncludedResourcesTrait
{

    /**
     * @var array
     */
    protected $originalIncludedResources = [];

    public function getOriginalIncludedResources(): array
    {
        return $this->originalIncludedResources;
    }
}
