<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Command\Traits;

use DFAU\ToujouApi\Command\IncludedResourcesDataCommand;

/**
 * @deprecated
 */
trait IncludedResourcesDataCommandTrait
{
    /** @var array */
    protected $includedResourcesData = [];

    public function getIncludedResourcesData(): array
    {
        return $this->includedResourcesData;
    }

    public function withIncludedResourcesData(array $includedResourceData): IncludedResourcesDataCommand
    {
        $target = clone $this;
        $target->includedResourcesData = $includedResourceData;
        return $target;
    }
}
