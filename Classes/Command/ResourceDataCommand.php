<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Command;

interface ResourceDataCommand
{
    public function getResourceData(): array;

    public function withResourceData(array $resourceData): self;
}
