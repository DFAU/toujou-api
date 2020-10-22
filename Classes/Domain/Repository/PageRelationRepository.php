<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Domain\Repository;

interface PageRelationRepository
{
    public function findByPageIdentifier($pageIdentifier): array;
}
