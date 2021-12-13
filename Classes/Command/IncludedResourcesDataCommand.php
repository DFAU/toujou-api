<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Command;

/**
 * @deprecated
 */
interface IncludedResourcesDataCommand
{
    public function getIncludedResourcesData(): array;

    public function withIncludedResourcesData(array $resourceData): self;
}
