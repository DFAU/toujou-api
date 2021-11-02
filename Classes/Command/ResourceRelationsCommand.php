<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Command;

interface ResourceRelationsCommand
{
    public function getResourceRelations(): array;

    public function withResourceRelations(array $resourceRelations): self;
}
