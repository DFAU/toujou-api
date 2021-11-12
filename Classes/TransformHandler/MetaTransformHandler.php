<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\TransformHandler;

use DFAU\ToujouApi\Domain\Repository\AbstractDatabaseResourceRepository;

class MetaTransformHandler implements TransformHandler
{
    public function handleTransform($data, array $transformedData, callable $next): array
    {
        return $next($data, \array_merge($transformedData, [
            'meta' => $data[AbstractDatabaseResourceRepository::META_ATTRIBUTE],
        ]));
    }
}
