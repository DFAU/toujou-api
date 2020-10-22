<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\IncludeHandler;

use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Scope;

// Include handlers MUST abort the chain when they match
interface IncludeHandler
{
    public function getAvailableIncludes(array $currentIncludes, callable $next): array;

    public function getDefaultIncludes(array $currentIncludes, callable $next): array;

    public function handleInclude(Scope $scope, string $includeName, $data, callable $next): ?ResourceInterface;
}
