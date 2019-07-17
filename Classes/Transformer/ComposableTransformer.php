<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Transformer;

use DFAU\ToujouApi\IncludeHandler\IncludeHandler;
use DFAU\ToujouApi\TransformHandler\TransformHandler;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Scope;
use League\Fractal\TransformerAbstract;

class ComposableTransformer extends TransformerAbstract implements ResourceTransformerInterface
{

    /**
     * @var \Closure
     */
    protected $availableIncludesStack;

    /**
     * @var \Closure
     */
    protected $defaultIncludesStack;

    /**
     * @var \Closure
     */
    protected $transformHandlerStack;

    /**
     * @var \Closure
     */
    protected $includeHandlerStack;

    public function __construct(array $transformHandlers, array $includeHandlers = [])
    {
        $this->transformHandlerStack = array_reduce($transformHandlers, function($next, TransformHandler $transformHandler) {
            return $this->wrapTransformHandler($transformHandler, $next);
        }, function($data, array $transformedData) { return $transformedData; });

        $this->availableIncludesStack = array_reduce($includeHandlers, function($next, IncludeHandler $includeHandler) {
            return $this->wrapAvailableIncludesHandler($includeHandler, $next);
        }, function($currentIncludes) { return array_unique(array_merge($currentIncludes, parent::getAvailableIncludes())); });

        $this->defaultIncludesStack = array_reduce($includeHandlers, function($next, IncludeHandler $includeHandler) {
            return $this->wrapDefaultIncludesHandler($includeHandler, $next);
        }, function($currentIncludes) { return array_unique(array_merge($currentIncludes, parent::getDefaultIncludes())); });

        $this->includeHandlerStack = array_reduce($includeHandlers, function($next, IncludeHandler $includeHandler) {
            return $this->wrapIncludeHandler($includeHandler, $next);
        }, function(Scope $scope, $includeName, $data) { return parent::callIncludeMethod($scope, $includeName, $data); });
    }

    public function getAvailableIncludes()
    {
        return ($this->availableIncludesStack)([]);
    }

    public function getDefaultIncludes()
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
        return function($data, array $transformedData) use ($handler, $next): array {
            return $handler->handleTransform($data, $transformedData, $next);
        };
    }

    protected function wrapAvailableIncludesHandler(IncludeHandler $handler, callable $next): \Closure
    {
        return function(array $currentIncludes) use ($handler, $next): array {
            return $handler->getAvailableIncludes($currentIncludes, $next);
        };
    }

    protected function wrapDefaultIncludesHandler(IncludeHandler $handler, callable $next): \Closure
    {
        return function(array $currentIncludes) use ($handler, $next): array {
            return $handler->getDefaultIncludes($currentIncludes, $next);
        };
    }

    protected function wrapIncludeHandler(IncludeHandler $handler, callable $next): \Closure
    {
        return function($scope, $includeName, $data) use ($handler, $next): ?ResourceInterface {
            return $handler->handleInclude($scope, $includeName, $data, $next);
        };
    }
}
