<?php

declare(strict_types=1);
use DFAU\ToujouApi\Middleware\ApiEntrypoint;
use DFAU\ToujouApi\Middleware\CheckBeUserAuthorization;
use DFAU\ToujouApi\Middleware\JsonApiPayload;
use DFAU\ToujouApi\Middleware\LanguageResolver;
use DFAU\ToujouApi\Middleware\ParsedBodyReset;
use DFAU\ToujouApi\Middleware\Router;
use DFAU\ToujouApi\Middleware\TypoScriptFrontendInitialization;
use DFAU\ToujouOauth2Server\Middleware\ResourceServerMiddleware;

return [
    'frontend' => [
        'dfau/toujou-api/api-entrypoint' => [
            'target' => ApiEntrypoint::class,
            'after' => ['typo3/cms-frontend/authentication', 'dfau/toujou-oauth2-server/authorization-server'],
            'before' => ['typo3/cms-frontend/base-redirect-resolver'],
        ],
    ],
    'toujou_api' => [
        'dfau/toujou-api/resource-server' => [
            'target' => ResourceServerMiddleware::class,
        ],
        'dfau/toujou-api/tsfe' => [
            'target' => TypoScriptFrontendInitialization::class,
            'after' => ['dfau/toujou-api/resource-server'],
        ],
        'dfau/toujou-api/language-resolve' => [
            'target' => LanguageResolver::class,
            'after' => ['dfau/toujou-api/tsfe'],
        ],
        'dfau/toujou-api/check-be-user-authorization' => [
            'target' => CheckBeUserAuthorization::class,
            'after' => ['dfau/toujou-api/tsfe'],
        ],
        'dfau/toujou-api/parsed-body-reset' => [
            'target' => ParsedBodyReset::class,
            'after' => ['dfau/toujou-api/check-be-user-authorization'],
        ],
        'middlewares/payload/jsonapi-payload' => [
            'target' => JsonApiPayload::class,
            'after' => ['dfau/toujou-api/parsed-body-reset'],
        ],

        'dfau/toujou-api/router' => [
            'target' => Router::class,
            'after' => ['middlewares/payload/jsonapi-payload'],
        ],
    ],
];
