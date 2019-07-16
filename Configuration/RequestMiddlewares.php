<?php

return [
    'frontend' => [
        'dfau/toujou-api/api-entrypoint' => [
            'target' => \DFAU\ToujouApi\Middleware\ApiEntrypoint::class,
            'after' => ['typo3/cms-frontend/site'],
            'before' => ['typo3/cms-frontend/base-redirect-resolver']
        ],
    ],
    'toujou_api' => [
        'dfau/toujou-api/router' => [
            'target' => \DFAU\ToujouApi\Middleware\Router::class
        ]
    ]
];
