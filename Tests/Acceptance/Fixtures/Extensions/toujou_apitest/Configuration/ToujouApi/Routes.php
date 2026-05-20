<?php

declare(strict_types=1);

use DFAU\ToujouApi\Resource\Numerus;
use DFAU\ToujouApi\Resource\Operation;

return [
    //Example routes
    'GET:/pages/' => [
        'numerus' => Numerus::COLLECTION->value,
        'operation' => Operation::READ->value,
        'resourceType' => 'pages',
    ],
    'GET:/pages/{id:\d+}' => [
        'numerus' => Numerus::ITEM->value,
        'operation' => Operation::READ->value,
        'resourceType' => 'pages',
    ],
    'PATCH:/pages/{id:\d+}' => [
        'numerus' => Numerus::ITEM->value,
        'operation' => Operation::UPDATE->value,
        'resourceType' => 'pages',
    ],
    'PUT:/pages/{id:\d+}' => [
        'numerus' => Numerus::ITEM->value,
        'operation' => Operation::REPLACE->value,
        'resourceType' => 'pages',
    ],
    'GET:/content-elements/' => [
        'numerus' => Numerus::COLLECTION->value,
        'operation' => Operation::READ->value,
        'resourceType' => 'content-elements',
    ],
    'GET:/content-elements/{id:\d+}' => [
        'numerus' => Numerus::ITEM->value,
        'operation' => Operation::READ->value,
        'resourceType' => 'content-elements',
    ],
];
