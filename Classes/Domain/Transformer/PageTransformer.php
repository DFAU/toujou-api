<?php


namespace DFAU\ToujouApi\Domain\Transformer;


use Cascader\Cascader;
use DFAU\ToujouApi\Domain\Repository\PageRelationRepository;
use DFAU\ToujouApi\Domain\Repository\PageRepository;
use DFAU\ToujouApi\Transformer\ComposableTransformer;
use DFAU\ToujouApi\Transformer\IncludesToResourceDefinitionMapping;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Scope;

final class PageTransformer extends ComposableTransformer
{

    /**
     * @var array<string, array>
     */
    protected $allowedPageIncludes = [];

    protected $availableIncludes = [
        'contentElements',
        'media'
    ];

    public function __construct(array $transformHandlers, array $includeHandlers = [])
    {
        parent::__construct($transformHandlers, $includeHandlers);
    }

    protected function includeRelation(Scope $scope, string $includeName, array $data): ResourceInterface
    {
        if ($this->allowedPageIncludes->hasDefinition($includeName)) {
            return $this->includePageRelation($scope, $includeName, $data);
        }

        return parent::includeRelation($scope, $includeName, $data);
    }

    protected function includePageRelation(Scope $scope, string $resourceKey, array $data): ResourceInterface
    {
        $resourceDefinition = $this->allowedPageIncludes->getResourceDefinition($resourceKey);
        $cascader = new Cascader();

        $repository = $cascader->create($resourceDefinition['repository'][\Cascader\Cascader::ARGUMENT_CLASS], $resourceDefinition['repository']);
        if (!$repository instanceof PageRelationRepository) {
            throw new \InvalidArgumentException('The given repository "' . get_class($repository) . '" has to implement the "' . \DFAU\ToujouApi\Domain\Repository\PageRelationRepository::class .'".', 1563210118);
        }

        /** @var ResourceInterface $transformer */
        $transformer = $cascader->create($resourceDefinition['transformer'][\Cascader\Cascader::ARGUMENT_CLASS], $resourceDefinition['transformer']);

        return $this->collection(
            $repository->findByPageIdentifier($data[PageRepository::DEFAULT_IDENTIFIER]),
            $transformer,
            $resourceKey
        );
    }

//        $allowedTableList = $GLOBALS['PAGES_TYPES'][$doktype]['allowedTables'] ?? $GLOBALS['PAGES_TYPES']['default']['allowedTables'];

//    public function includeMedia(array $page): Collection
//    {
//        $fileReferences = GeneralUtility::makeInstance(FileReferenceRepository::class)->findByRelation(PageRepository::TABLE_NAME, 'media', $page[PageRepository::DEFAULT_IDENTIFIER]);
//        return $this->collection($fileReferences, new FileReferenceTransformer, 'FileReference');
//    }

//    public function includeContentElements(array $page): Collection
//    {
//        $contentElements = GeneralUtility::makeInstance(ContentElementRepository::class)->findByPageIdentifier($page[PageRepository::DEFAULT_IDENTIFIER]);
//        return $this->collection($contentElements, new ContentElementTransformer(ContentElementRepository::TABLE_NAME), 'ContentElement');
//    }

}
