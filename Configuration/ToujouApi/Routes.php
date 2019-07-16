<?php

return [
    'GET:/pages/' => [
        'resourceType' => \League\Fractal\Resource\Collection::class,
        'resourceKey' => 'pages',
        'serializer' => ['__class__' => \League\Fractal\Serializer\JsonApiSerializer::class]
    ],
    'GET:/pages/{id:\d+}' => [
        'resourceType' => \League\Fractal\Resource\Item::class,
        'resourceKey' => 'pages',
        'serializer' => ['__class__' => \League\Fractal\Serializer\JsonApiSerializer::class]
    ],
    'GET:/content-elements/' => [
        'resourceType' => \League\Fractal\Resource\Collection::class,
        'resourceKey' => 'content-elements',
        'serializer' => ['__class__' => \League\Fractal\Serializer\JsonApiSerializer::class]
    ],
    'GET:/content-elements/{id:\d+}' => [
        'resourceType' => \League\Fractal\Resource\Item::class,
        'resourceKey' => 'content-elements',
        'serializer' => ['__class__' => \League\Fractal\Serializer\JsonApiSerializer::class]
    ]
];
