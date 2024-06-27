<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Transformer;

use DFAU\ToujouApi\IncludeHandler\IncludeHandler;
use DFAU\ToujouApi\TransformHandler\TransformHandler;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Scope;
use League\Fractal\TransformerAbstract;

class ComposableTransformer extends TransformerAbstract implements ResourceTransformerInterface
{
    /** @var \Closure */
    protected $availableIncludesStack;

    /** @var \Closure */
    protected $defaultIncludesStack;

    /** @var \Closure */
    protected $transformHandlerStack;

    /** @var \Closure */
    protected $includeHandlerStack;

    public function __construct(array $transformHandlers, array $includeHandlers = [])
    {
        $this->transformHandlerStack = \array_reduce($transformHandlers, fn ($next, TransformHandler $transformHandler) => $this->wrapTransformHandler($transformHandler, $next), fn ($data, array $transformedData) => $transformedData);

        $this->availableIncludesStack = \array_reduce($includeHandlers, fn ($next, IncludeHandler $includeHandler) => $this->wrapAvailableIncludesHandler($includeHandler, $next), fn ($currentIncludes) => \array_unique(\array_merge($currentIncludes, parent::getAvailableIncludes())));

        $this->defaultIncludesStack = \array_reduce($includeHandlers, fn ($next, IncludeHandler $includeHandler) => $this->wrapDefaultIncludesHandler($includeHandler, $next), fn ($currentIncludes) => \array_unique(\array_merge($currentIncludes, parent::getDefaultIncludes())));

        $this->includeHandlerStack = \array_reduce($includeHandlers, fn ($next, IncludeHandler $includeHandler) => $this->wrapIncludeHandler($includeHandler, $next), fn (Scope $scope, $includeName, $data) => parent::callIncludeMethod($scope, $includeName, $data));
    }

    public function getAvailableIncludes(): array
    {
        return ($this->availableIncludesStack)([]);
    }

    public function getDefaultIncludes(): array
    {
        return ($this->defaultIncludesStack)([]);
    }

    public function transform($data): array
    {
        return ($this->transformHandlerStack)($data, []);
    }

    protected function callIncludeMethod(Scope $scope, $includeName, $data)
    {
        return ($this->includeHandlerStack)($scope, $includeName, $data);
    }

    protected function wrapTransformHandler(TransformHandler $handler, callable $next): \Closure
    {
        return fn ($data, array $transformedData): array => $handler->handleTransform($data, $transformedData, $next);
    }

    protected function wrapAvailableIncludesHandler(IncludeHandler $handler, callable $next): \Closure
    {
        return fn (array $currentIncludes): array => $handler->getAvailableIncludes($currentIncludes, $next);
    }

    protected function wrapDefaultIncludesHandler(IncludeHandler $handler, callable $next): \Closure
    {
        return fn (array $currentIncludes): array => $handler->getDefaultIncludes($currentIncludes, $next);
    }

    protected function wrapIncludeHandler(IncludeHandler $handler, callable $next): \Closure
    {
        return fn ($scope, $includeName, $data): ?ResourceInterface => $handler->handleInclude($scope, $includeName, $data, $next);
    }
}
