<?php

// Configure a new simple required input field to site
$GLOBALS['SiteConfiguration']['site']['columns']['toujouApiPathPrefix'] = [
    'label' => 'LLL:EXT:toujou_api/Resources/Private/Language/locallang_siteconfiguration_tca.xlf:site.toujouApiPathPrefix.label',
    'description' => 'LLL:EXT:toujou_api/Resources/Private/Language/locallang_siteconfiguration_tca.xlf:site.toujouApiPathPrefix.description',
    'config' => [
        'type' => 'input',
        'eval' => 'trim',
        'default' => '_api/',
        'valuePicker' => [
            'items' => [
                ['_api/', '_api/'],
            ],
        ],
    ],
];
// And add it to showitem
if (strpos($GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'], '--div--;API,') === false) {
    $GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= ', --div--;API';
}
$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] = str_replace(
    '--div--;API',
    '--div--;API, toujouApiPathPrefix,',
    $GLOBALS['SiteConfiguration']['site']['types']['0']['showitem']
);
