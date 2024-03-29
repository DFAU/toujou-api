<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Transformer;

use League\Fractal\Scope;

/**
 * Interface ResourceTransformerInterface
 * necessary for type safety in the API package
 */
interface ResourceTransformerInterface
{
    public function getAvailableIncludes();

    public function getDefaultIncludes();

    public function getCurrentScope();

    public function transform($data): array;

    public function processIncludedResources(Scope $scope, $data);

    public function setAvailableIncludes(array $availableIncludes);

    public function setDefaultIncludes(array $defaultIncludes);

    public function setCurrentScope(Scope $currentScope);
}
