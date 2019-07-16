<?php declare(strict_types=1);


namespace DFAU\ToujouApi\TransformHandler;

use DFAU\ToujouApi\Domain\Repository\AbstractDatabaseResourceRepository;
use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormDataGroup\OnTheFly;
use TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRecordTypeValue;
use TYPO3\CMS\Backend\Form\FormDataProvider\InitializeProcessedTca;
use TYPO3\CMS\Backend\Form\FormDataProvider\TcaColumnsProcessShowitem;
use TYPO3\CMS\Backend\Form\FormDataProvider\TcaTypesShowitem;
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

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;

        $formDataGroup = GeneralUtility::makeInstance(OnTheFly::class);
        $formDataGroup->setProviderList([
            InitializeProcessedTca::class,
            DatabaseRecordTypeValue::class,
            TcaColumnsProcessShowitem::class,
            TcaTypesShowitem::class
        ]);
        $this->formDataCompiler = GeneralUtility::makeInstance(FormDataCompiler::class, $formDataGroup);
    }


    public function handleTransform($data, array $transformedData, \Closure $next): array
    {
        return $next($data, array_merge($transformedData, [
            'id' => (string)$data[AbstractDatabaseResourceRepository::DEFAULT_IDENTIFIER],
            'meta' => $data[AbstractDatabaseResourceRepository::META_ATTRIBUTE],
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
            return $columnName[0] !== '-';
        });

        return array_combine($visibleColumns, array_map(function ($columnName) use ($resource) {
            return $resource[$columnName];
        }, $visibleColumns));

    }
}
