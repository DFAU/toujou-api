<?php

return [
    'frontend' => [
        'dfau/toujou-api/api-entrypoint' => [
            'target' => \DFAU\ToujouApi\Middleware\ApiEntrypoint::class,
            'after' => ['typo3/cms-frontend/site', 'dfau/toujou-oauth2-server/resource-server'],
            'before' => ['typo3/cms-frontend/base-redirect-resolver']
        ],
    ],
    'toujou_api' => [
        'middlewares/payload/json-payload' => [
            'target' => \Middlewares\JsonPayload::class
        ],
        'dfau/toujou-api/router' => [
            'target' => \DFAU\ToujouApi\Middleware\Router::class,
            'after' => ['middlewares/payload/json-payload']
        ]
    ]
];
