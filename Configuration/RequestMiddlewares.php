<?php

use DFAU\ToujouApi\Middleware\ApiEntrypoint;
use DFAU\ToujouApi\Middleware\JsonApiPayload;
use DFAU\ToujouApi\Middleware\ParsedBodyReset;
use DFAU\ToujouApi\Middleware\Router;
use Middlewares\JsonPayload;

return [
    'frontend' => [
        'dfau/toujou-api/api-entrypoint' => [
            'target' => ApiEntrypoint::class,
            'after' => ['typo3/cms-frontend/site', 'dfau/toujou-oauth2-server/resource-server'],
            'before' => ['typo3/cms-frontend/base-redirect-resolver']
        ],
    ],
    'toujou_api' => [
        'dfau/toujou-api/parsed-body-reset' => [
            'target' => ParsedBodyReset::class
        ],
        'middlewares/payload/jsonapi-payload' => [
            'target' => JsonApiPayload::class,
            'after' => ['dfau/toujou-api/parsed-body-reset']
        ],
        'dfau/toujou-api/router' => [
            'target' => Router::class,
            'after' => ['middlewares/payload/jsonapi-payload']
        ]
    ]
];
