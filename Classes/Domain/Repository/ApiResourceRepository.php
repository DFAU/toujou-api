<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Domain\Repository;

interface ApiResourceRepository
{
    public const META_ATTRIBUTE = '_meta';

    public const META_CREATED = 'created';

    public const META_LAST_UPDATED = 'lastUpdated';

    public function findByFiltersWithCursor(array $filters, int $limit, ?string $currentCursor, ?string $previousCursor): array;

    public function findOneByIdentifier($identifier, $context = null): ?array;

    public function findByIdentifiers(array $identifiers): array;
}
