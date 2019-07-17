<?php declare(strict_types=1);


namespace DFAU\ToujouApi\IncludeHandler;


use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Scope;

class StaticDefaultIncludesIncludeHandler implements IncludeHandler
{
    /**
     * @var array
     */
    protected $defaultIncludes;

    public function __construct(array $defaultIncludes = [])
    {
        $this->defaultIncludes = $defaultIncludes;
    }

    public function getAvailableIncludes(array $currentIncludes, callable $next): array
    {
        return $next($currentIncludes);
    }

    public function getDefaultIncludes(array $currentIncludes, callable $next): array
    {
        return $next(array_merge($currentIncludes, $this->defaultIncludes));
    }

    public function handleInclude(Scope $scope, string $includeName, $data, callable $next): ?ResourceInterface
    {
        return $next($scope, $includeName, $data);
    }
}
