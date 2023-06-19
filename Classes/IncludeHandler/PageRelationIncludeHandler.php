<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\IncludeHandler;

use Cascader\Cascader;
use DFAU\ToujouApi\Configuration\ConfigurationManager;
use DFAU\ToujouApi\Domain\Repository\PageRelationRepository;
use DFAU\ToujouApi\Domain\Repository\PageRepository;
use DFAU\ToujouApi\Transformer\ResourceTransformerInterface;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Scope;

class PageRelationIncludeHandler implements IncludeHandler
{
    protected $allowedPageIncludes = [];

    public function __construct(array $includeToResourceMap = [])
    {
        $allResourceDefinitions = ConfigurationManager::getResourcesConfiguration();
        $this->allowedPageIncludes = \array_filter(\array_map(function (string $resourceType) use ($allResourceDefinitions): ?array {
            if (isset($allResourceDefinitions[$resourceType])) {
                $resourceDefinition = $allResourceDefinitions[$resourceType];
                $resourceDefinition['resourceType'] = $resourceType;

                return $resourceDefinition;
            }

            return null;
        }, $includeToResourceMap));
    }

    public function getAvailableIncludes(array $currentIncludes, callable $next): array
    {
        return $next(\array_merge($currentIncludes, \array_keys($this->allowedPageIncludes)));
    }

    public function getDefaultIncludes(array $currentIncludes, callable $next): array
    {
        return $next($currentIncludes);
    }

    public function handleInclude(Scope $scope, string $includeName, $data, callable $next): ?ResourceInterface
    {
        if (!isset($this->allowedPageIncludes[$includeName])) {
            return $next($scope, $includeName, $data);
        }

        $resourceDefinition = $this->allowedPageIncludes[$includeName];
        $cascader = new Cascader();

        $repository = $cascader->create($resourceDefinition['repository'][Cascader::ARGUMENT_CLASS], $resourceDefinition['repository']);
        if (!$repository instanceof PageRelationRepository) {
            throw new \InvalidArgumentException('The given repository "' . \get_class($repository) . '" has to implement the "' . PageRelationRepository::class . '".', 1563210118);
        }

        /** @var ResourceTransformerInterface $transformer */
        $transformer = $cascader->create($resourceDefinition['transformer'][Cascader::ARGUMENT_CLASS], $resourceDefinition['transformer']);

        return new Collection(
            $repository->findByPageIdentifier($data[PageRepository::DEFAULT_IDENTIFIER]),
            $transformer,
            $resourceDefinition['resourceType']
        );
    }
}
