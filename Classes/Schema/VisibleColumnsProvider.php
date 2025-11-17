<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Schema;

use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormDataGroup\OnTheFly;
use TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRecordTypeValue;
use TYPO3\CMS\Backend\Form\FormDataProvider\InitializeProcessedTca;
use TYPO3\CMS\Backend\Form\FormDataProvider\TcaColumnsProcessShowitem;
use TYPO3\CMS\Backend\Form\FormDataProvider\TcaTypesShowitem;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class VisibleColumnsProvider
{
    /** @var FormDataCompiler */
    protected $formDataCompiler;

    public function __construct()
    {
        $this->formDataCompiler = GeneralUtility::makeInstance(FormDataCompiler::class);
    }

    public function getVisibleColumnsForResource(string $tableName, array $resource): array
    {
        $formDataGroup = GeneralUtility::makeInstance(OnTheFly::class);
        $formDataGroup->setProviderList([
            InitializeProcessedTca::class,
            DatabaseRecordTypeValue::class,
            TcaColumnsProcessShowitem::class,
            TcaTypesShowitem::class,
        ]);

        $result = $this->formDataCompiler->compile([
            'request' => $GLOBALS['TYPO3_REQUEST'],
            'tableName' => $tableName,
            'databaseRow' => $resource,
        ], $formDataGroup);

        return \array_filter($result['columnsToProcess'], fn ($columnName) => '-' !== $columnName[0]);
    }
}
