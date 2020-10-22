<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Command\Traits;

trait ResourceReferencingCommandTrait
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
