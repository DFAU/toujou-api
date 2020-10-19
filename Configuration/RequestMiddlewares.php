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
            'after' => ['typo3/cms-frontend/authentication', 'dfau/toujou-oauth2-server/authorization-server'],
            'before' => ['typo3/cms-frontend/base-redirect-resolver']
        ],
    ],
//    'frontend' => [
//        'dfau/toujou-api/api-entrypoint' => [
//            'target' => ApiEntrypoint::class,
//            'after' => ['dfau/toujou-oauth2-server/authorization-server', 'typo3/cms-frontend/tsfe'],
//            'before' => ['typo3/cms-adminpanel/renderer']
//        ],
//    ],
    'toujou_api' => [
        'dfau/toujou-api/resource-server' => [
            'target' => \DFAU\ToujouOauth2Server\Middleware\ResourceServerMiddleware::class,
        ],
        'dfau/toujou-api/check-be-user-authorization' => [
            'target' => \DFAU\ToujouApi\Middleware\CheckBeUserAuthorization::class,
            'after' => ['dfau/toujou-api/resource-server']
        ],
        'dfau/toujou-api/parsed-body-reset' => [
            'target' => ParsedBodyReset::class,
            'after' => ['dfau/toujou-api/check-be-user-authorization']
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
