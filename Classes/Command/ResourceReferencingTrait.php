<?php


namespace DFAU\ToujouApi\Command;


trait ResourceReferencingTrait
{

    /**
     * @var string
     */
    protected $resourceType;

    /**
     * @var string
     */
    protected $resourceIdentifier;

    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    public function getResourceIdentifier(): string
    {
        return $this->resourceIdentifier;
    }

}
