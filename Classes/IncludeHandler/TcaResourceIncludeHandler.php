<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\IncludeHandler;

use Cascader\Cascader;
use DFAU\ToujouApi\Configuration\ConfigurationManager;
use DFAU\ToujouApi\Transformer\ResourceTransformerInterface;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Scope;
use TYPO3\CMS\Core\Database\RelationHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaResourceIncludeHandler implements IncludeHandler
{
    protected const REFERENCE_TABLE_NAME = '__referenceTableName__';

    /** @var string */
    protected $tableName;

    /** @var array */
    protected $tcaIncludes;

    protected $resourceDefinitionsByTableName;

    public function __construct(string $tableName, array $tableNameToResourceMap = [])
    {
        $this->tableName = $tableName;
        $this->tcaIncludes = $this->buildTcaIncludes($this->tableName);
        $this->resourceDefinitionsByTableName = $this->buildResourceDefinitions($tableNameToResourceMap);
    }

    public function getAvailableIncludes(array $currentIncludes, callable $next): array
    {
        return $next(\array_merge($currentIncludes, \array_keys($this->tcaIncludes)));
    }

    public function getDefaultIncludes(array $currentIncludes, callable $next): array
    {
        return $next($currentIncludes);
    }

    public function handleInclude(Scope $scope, string $includeName, $data, callable $next): ?ResourceInterface
    {
        if (!isset($this->tcaIncludes[$includeName])) {
            return $next($scope, $includeName, $data);
        }

        $columnConfig = $this->tcaIncludes[$includeName];
        $fieldValue = $data[$includeName];
        $uid = $data['uid'];

        // TODO elaborate whether a guard against multi table "allowed" configurations actually are a problem
        $allowedTableName = 'group' === $columnConfig['type'] ? $columnConfig['allowed'] : $columnConfig['foreign_table'];
        if (!isset($this->resourceDefinitionsByTableName[$allowedTableName])) {
            return $next($scope, $includeName, $data);
        }
        $mmTableName = $columnConfig['MM'] ?? '';

        $relationHandler = GeneralUtility::makeInstance(RelationHandler::class);
        $relationHandler->start($fieldValue, $allowedTableName, $mmTableName, $uid, $this->tableName, $columnConfig);
        $result = \array_filter($relationHandler->itemArray, fn ($item) => $item['table'] === $allowedTableName);

        $resourceType = (isset($columnConfig['maxitems']) && 1 == $columnConfig['maxitems']) || (isset($columnConfig['renderType']) && 'selectSingle' === $columnConfig['renderType']) ? Item::class : Collection::class;

        if ([] !== $result) {
            $resourceDefinition = $this->resourceDefinitionsByTableName[$allowedTableName];

            // Override any custom Identifier here for our database record identifier
            unset($resourceDefinition['repository']['identifier']);

            $cascader = new Cascader();

            if (!empty($columnConfig['foreign_sortby'])) {
                // Some tca fields might override the sorting
                $resourceDefinition['repository']['orderBy'] = $columnConfig['foreign_sortby'];
            }

            $repository = $cascader->create($resourceDefinition['repository'][Cascader::ARGUMENT_CLASS], $resourceDefinition['repository']);

            /** @var ResourceTransformerInterface $transformer */
            $transformer = $cascader->create($resourceDefinition['transformer'][Cascader::ARGUMENT_CLASS], $resourceDefinition['transformer']);

            if (Item::class === $resourceType) {
                $data = $repository->findOneByIdentifier(\reset($result)['id']);
                if ($data) {
                    return new $resourceType(
                        $data,
                        $transformer,
                        $resourceDefinition['resourceType']
                    );
                }

                return null;
            }

            return new $resourceType(
                $repository->findByIdentifiers(\array_column($result, 'id')),
                $transformer,
                $resourceDefinition['resourceType']
            );
        }

        return new $resourceType([]);
    }

    protected function buildTcaIncludes($tableName): array
    {
        return \array_filter(\array_map(function ($columnDefinition) {
            $columnConfig = $columnDefinition['config'];

            if (isset($columnConfig['type'])) {
                switch ($columnConfig['type']) {
                    case 'select':
                    case 'inline':
                    case 'category':
                        if (!empty($columnConfig['foreign_table'])) {
                            return \array_merge($columnConfig, [static::REFERENCE_TABLE_NAME => [$columnConfig['foreign_table']]]);
                        }

                        break;
                    case 'group':
                        if ('db' === ($columnConfig['internal_type'] ?? null) && !empty($columnConfig['allowed']) && false === \strpos($columnConfig['allowed'], ',')) {
                            return \array_merge($columnConfig, [static::REFERENCE_TABLE_NAME => GeneralUtility::trimExplode(',', $columnConfig['allowed'], true)]);
                        }

                        break;
                }
            }

            return null;
        }, $GLOBALS['TCA'][$tableName]['columns'] ?? []));
    }

    protected function buildResourceDefinitions(array $tableNameToResourceMap): array
    {
        $allResourceDefinitions = ConfigurationManager::getResourcesConfiguration();

        return \array_filter(\array_map(function (string $resourceType) use ($allResourceDefinitions): ?array {
            if (isset($allResourceDefinitions[$resourceType])) {
                $resourceDefinition = $allResourceDefinitions[$resourceType];
                $resourceDefinition['resourceType'] = $resourceType;

                return $resourceDefinition;
            }

            return null;
        }, $tableNameToResourceMap));
    }
}
