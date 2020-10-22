<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\TransformHandler;

use DFAU\ToujouApi\Domain\Repository\AbstractDatabaseResourceRepository;
use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormDataGroup\OrderedProviderList;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaResourceTransformHandler implements TransformHandler
{

    /**
     * @var FormDataCompiler
     */
    protected $formDataCompiler;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var array
     */
    protected $excludedColumns;

    public function __construct(string $tableName, array $excludedColumns = [], string $identifier = AbstractDatabaseResourceRepository::DEFAULT_IDENTIFIER)
    {
        $this->tableName = $tableName;
        $this->identifier = $identifier;
        $this->excludedColumns = array_fill_keys($excludedColumns, true);

        $orderedProviderList = GeneralUtility::makeInstance(OrderedProviderList::class);
        $orderedProviderList->setProviderList(
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['toujouApiTcaResource']
        );

        $this->formDataCompiler = GeneralUtility::makeInstance(FormDataCompiler::class, $orderedProviderList);
    }

    public function handleTransform($data, array $transformedData, callable $next): array
    {
        return $next($data, array_merge($transformedData, [
            'id' => (string)$data[$this->identifier],
            AbstractDatabaseResourceRepository::DEFAULT_PARENT_PAGE_IDENTIFIER => $data[AbstractDatabaseResourceRepository::DEFAULT_PARENT_PAGE_IDENTIFIER],
        ], $this->getVisibleAttributesOfResource($data)));
    }

    protected function getVisibleAttributesOfResource(array $resource): array
    {
        $result = $this->formDataCompiler->compile([
            'tableName' => $this->tableName,
            'databaseRow' => $resource,
        ]);

        $visibleColumns = array_filter($result['columnsToProcess'], function ($columnName) {
            return !isset($this->excludedColumns[$columnName]) && $columnName[0] !== '-';
        });

        return array_combine($visibleColumns, array_map(function ($columnName) use ($result) {
            return $result['databaseRow'][$columnName];
        }, $visibleColumns));
    }
}
