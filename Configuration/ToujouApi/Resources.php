<?php


return [
    'pages' => [
        'repository' => [
            '__class__' => \DFAU\ToujouApi\Domain\Repository\TcaResourceRepository::class,
            'tableName' => 'pages'
        ],
        'transformer' => [
            '__class__' => \DFAU\ToujouApi\Transformer\ComposableTransformer::class,
            'transformHandlers' => [
                ['__class__' => \DFAU\ToujouApi\TransformHandler\MetaTransformHandler::class],
                ['__class__' => \DFAU\ToujouApi\TransformHandler\TcaResourceTransformHandler::class, 'tableName' => 'pages']
            ],
            'includeHandlers' => [
                [
                    '__class__' => \DFAU\ToujouApi\IncludeHandler\PageRelationIncludeHandler::class,
                    'includeToResourceMap' => ['content-elements' => 'content-elements']
                ]
            ]
        ],
    ],
    'content-elements' => [
        'repository' => [
            '__class__' => \DFAU\ToujouApi\Domain\Repository\TcaResourceRepository::class,
            'tableName' => 'tt_content'
        ],
        'transformer' => [
            '__class__' => \DFAU\ToujouApi\Transformer\ComposableTransformer::class,
            'transformHandlers' => [
                ['__class__' => \DFAU\ToujouApi\TransformHandler\MetaTransformHandler::class],
                ['__class__' => \DFAU\ToujouApi\TransformHandler\TcaResourceTransformHandler::class, 'tableName' => 'tt_content']
            ],
            'includeHandlers' => [
                [
                    '__class__' => \DFAU\ToujouApi\IncludeHandler\TcaResourceIncludeHandler::class,
                    'tableName' => 'tt_content',
                    'tableNameToResourceMap' => ['sys_file_reference' => 'file-references']
                ],
//                [
//                    '__class__' => \DFAU\ToujouApi\IncludeHandler\StaticDefaultIncludesIncludeHandler::class,
//                    'defaultIncludes' => ['assets', 'image', 'media']
//                ]
            ]
        ]
    ],
    'file-references' => [
        'repository' => [
            '__class__' => \DFAU\ToujouApi\Domain\Repository\FileReferenceRepository::class
        ],
        'transformer' => [
            '__class__' => \DFAU\ToujouApi\Domain\Transformer\FileReferenceTransformer::class,
        ]
    ],
    'files' => [
        'repository' => [
            '__class__' => \DFAU\ToujouApi\Domain\Repository\FileRepository::class
        ],
        'transformer' => [
            '__class__' => \DFAU\ToujouApi\Domain\Transformer\FileReferenceTransformer::class
        ]
    ]
];
