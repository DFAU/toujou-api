<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Domain\Repository;

interface ApiResourceRepository
{
    const META_ATTRIBUTE = '_meta';
    const META_CREATED = 'created';
    const META_LAST_UPDATED = 'lastUpdated';

    public function findWithCursor(int $limit, ?string $currentCursor, ?string $previousCursor): array;

    public function findOneByIdentifier($identifier): ?array;

    public function findByIdentifiers(array $identifiers): array;
}
