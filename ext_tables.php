<?php

defined('TYPO3_MODE') or die();

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['toujouApiTcaResource'] = [
    \TYPO3\CMS\Backend\Form\FormDataProvider\InitializeProcessedTca::class =>
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\TYPO3\CMS\Backend\Form\FormDataProvider\InitializeProcessedTca::class],
    \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRecordTypeValue::class =>
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRecordTypeValue::class],
    \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexPrepare::class =>
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexPrepare::class],
    \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexProcess::class =>
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexProcess::class],
    \TYPO3\CMS\Backend\Form\FormDataProvider\EvaluateDisplayConditions::class =>
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\TYPO3\CMS\Backend\Form\FormDataProvider\EvaluateDisplayConditions::class],
    \TYPO3\CMS\Backend\Form\FormDataProvider\TcaColumnsProcessShowitem::class =>
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\TYPO3\CMS\Backend\Form\FormDataProvider\TcaColumnsProcessShowitem::class],
    \TYPO3\CMS\Backend\Form\FormDataProvider\TcaTypesShowitem::class =>
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\TYPO3\CMS\Backend\Form\FormDataProvider\TcaTypesShowitem::class],
    \DFAU\ToujouApi\Form\DatabaseRowDateTimeFields::class => [
        'depends' => [\TYPO3\CMS\Backend\Form\FormDataProvider\InitializeProcessedTca::class],
    ],
];
