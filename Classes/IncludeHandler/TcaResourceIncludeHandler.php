<?php declare(strict_types=1);


namespace DFAU\ToujouApi\IncludeHandler;


use Cascader\Cascader;
use DFAU\ToujouApi\Configuration\ConfigurationManager;
use DFAU\ToujouApi\Domain\Repository\AbstractDatabaseResourceRepository;
use DFAU\ToujouApi\Domain\Repository\PageRelationRepository;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Scope;
use TYPO3\CMS\Core\Database\RelationHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaResourceIncludeHandler implements IncludeHandler
{

    protected const REFERENCE_TABLE_NAME = '__referenceTableName__';

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var array
     */
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
        return $next(array_merge($currentIncludes, array_keys($this->tcaIncludes)));
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
        $allowedTableName = $columnConfig['type'] === 'group' ? $columnConfig['allowed'] : $columnConfig['foreign_table'];
        if (!isset($this->resourceDefinitionsByTableName[$allowedTableName])) {
            return $next($scope, $includeName, $data);
        }
        $mmTableName = $columnConfig['MM'] ?? '';

        $relationHandler = GeneralUtility::makeInstance(RelationHandler::class);
        $relationHandler->start($fieldValue, $allowedTableName, $mmTableName, $uid, $this->tableName, $columnConfig);
        $result = array_filter($relationHandler->itemArray, function($item) use($allowedTableName) {
          return $item['table'] === $allowedTableName;
        });

        $resourceType = (isset($columnConfig['maxitems']) && $columnConfig['maxitems'] == 1) || (isset($columnConfig["renderType"]) && $columnConfig["renderType"] === 'selectSingle') ? Item::class : Collection::class;

        if (!empty($result)) {
            $resourceDefinition = $this->resourceDefinitionsByTableName[$allowedTableName];

            // Override any custom Identifier here for our database record identifier
            unset($resourceDefinition['repository']['identifier']);

            $cascader = new Cascader();

            if (!empty($columnConfig['foreign_sortby'])) {
                // Some tca fields might override the sorting
                $resourceDefinition['repository']['orderBy'] = $columnConfig['foreign_sortby'];
            }

            $repository = $cascader->create($resourceDefinition['repository'][\Cascader\Cascader::ARGUMENT_CLASS], $resourceDefinition['repository']);

            /** @var ResourceInterface $transformer */
            $transformer = $cascader->create($resourceDefinition['transformer'][\Cascader\Cascader::ARGUMENT_CLASS], $resourceDefinition['transformer']);

            if ($resourceType === Item::class) {
                if ($data = $repository->findOneByIdentifier(reset($result)['id'])) {
                    return new $resourceType(
                        $data,
                        $transformer,
                        $resourceDefinition['resourceType']
                    );
                }
                return null;
            }

            return new $resourceType(
                $repository->findByIdentifiers(array_column($result, 'id')),
                $transformer,
                $resourceDefinition['resourceType']
            );
        }

        return new $resourceType([]);
    }

    protected function buildTcaIncludes($tableName): array
    {
        return array_filter(array_map(function ($columnDefinition) {
            $columnConfig = $columnDefinition['config'];

            if (isset($columnConfig['type'])) {
                switch ($columnConfig['type']) {
                    case 'select':
                    case 'inline':
                        if (!empty($columnConfig['foreign_table'])) {
                            return array_merge($columnConfig, [static::REFERENCE_TABLE_NAME => [$columnConfig['foreign_table']]]);
                        }
                        break;
                    case 'group':
                        if ($columnConfig['internal_type'] === 'db' && !empty($columnConfig['allowed']) && strpos($columnConfig['allowed'], ',') === false) {
                            return array_merge($columnConfig, [static::REFERENCE_TABLE_NAME => GeneralUtility::trimExplode(',', $columnConfig['allowed'], true)]);
                        }
                        break;
                }
            }

            return null;
        }, $GLOBALS['TCA'][$tableName]['columns'] ?? []));
    }

    /**
     * @param array $tableNameToResourceMap
     */
    protected function buildResourceDefinitions(array $tableNameToResourceMap): array
    {
        $allResourceDefinitions = ConfigurationManager::getResourcesConfiguration();
        return array_filter(array_map(function (string $resourceType) use ($allResourceDefinitions): ?array {
            if (isset($allResourceDefinitions[$resourceType])) {
                $resourceDefinition = $allResourceDefinitions[$resourceType];
                $resourceDefinition['resourceType'] = $resourceType;
                return $resourceDefinition;
            }
            return null;
        }, $tableNameToResourceMap));
    }
}
