<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Command\Traits;

use DFAU\ToujouApi\Command\ResourceDataCommand;

trait ResourceDataCommandTrait
{
    /** @var array */
    protected $resourceData;

    public function getResourceData(): array
    {
        return $this->resourceData;
    }

    public function withResourceData(array $resourceData): ResourceDataCommand
    {
        $target = clone $this;
        $target->resourceData = $resourceData;
        return $target;
    }
}
