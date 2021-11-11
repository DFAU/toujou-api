<?php

declare(strict_types=1);

return [
    //Example routes
    'GET:/pages/' => [
        'numerus' => DFAU\ToujouApi\Resource\Numerus::COLLECTION,
        'operation' => DFAU\ToujouApi\Resource\Operation::READ,
        'resourceType' => 'pages',
    ],
    'GET:/pages/{id:\d+}' => [
        'numerus' => DFAU\ToujouApi\Resource\Numerus::ITEM,
        'operation' => DFAU\ToujouApi\Resource\Operation::READ,
        'resourceType' => 'pages',
    ],
    'PATCH:/pages/{id:\d+}' => [
        'numerus' => DFAU\ToujouApi\Resource\Numerus::ITEM,
        'operation' => DFAU\ToujouApi\Resource\Operation::UPDATE,
        'resourceType' => 'pages',
    ],
    'PUT:/pages/{id:\d+}' => [
        'numerus' => DFAU\ToujouApi\Resource\Numerus::ITEM,
        'operation' => DFAU\ToujouApi\Resource\Operation::REPLACE,
        'resourceType' => 'pages',
    ],
    'GET:/content-elements/' => [
        'numerus' => DFAU\ToujouApi\Resource\Numerus::COLLECTION,
        'operation' => DFAU\ToujouApi\Resource\Operation::READ,
        'resourceType' => 'content-elements',
    ],
    'GET:/content-elements/{id:\d+}' => [
        'numerus' => DFAU\ToujouApi\Resource\Numerus::ITEM,
        'operation' => DFAU\ToujouApi\Resource\Operation::READ,
        'resourceType' => 'content-elements',
    ],
];
