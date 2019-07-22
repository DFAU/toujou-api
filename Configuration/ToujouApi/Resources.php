<?php

use DFAU\ToujouApi\Domain\Command;
use DFAU\ToujouApi\Domain\Repository;
use DFAU\ToujouApi\IncludeHandler;
use DFAU\ToujouApi\Resource\Operation;
use DFAU\ToujouApi\Transformer as GenericTranformer;
use DFAU\ToujouApi\Domain\Transformer;
use DFAU\ToujouApi\TransformHandler;

return [
    'pages' => [
        'repository' => [
            '__class__' => Repository\TcaResourceRepository::class,
            'tableName' => 'pages'
        ],
        'transformer' => [
            '__class__' => GenericTranformer\ComposableTransformer::class,
            'transformHandlers' => [
                ['__class__' => TransformHandler\MetaTransformHandler::class],
                ['__class__' => TransformHandler\TcaResourceTransformHandler::class, 'tableName' => 'pages']
            ],
            'includeHandlers' => [
                [
                    '__class__' => IncludeHandler\PageRelationIncludeHandler::class,
                    'includeToResourceMap' => ['content-elements' => 'content-elements']
                ]
            ]
        ],
        'operationToCommandMap' => [
            Operation::CREATE => Command\CreateTcaResourceCommand::class,
            Operation::REPLACE => Command\ReplaceTcaResourceCommand::class,
            Operation::UPDATE => Command\UpdateTcaResourceCommand::class,
            Operation::DELETE => Command\DeleteTcaResourceCommand::class
        ]
    ],
    'content-elements' => [
        'repository' => [
            '__class__' => Repository\TcaResourceRepository::class,
            'tableName' => 'tt_content'
        ],
        'transformer' => [
            '__class__' => GenericTranformer\ComposableTransformer::class,
            'transformHandlers' => [
                ['__class__' => TransformHandler\MetaTransformHandler::class],
                ['__class__' => TransformHandler\TcaResourceTransformHandler::class, 'tableName' => 'tt_content']
            ],
            'includeHandlers' => [
                [
                    '__class__' =>  IncludeHandler\TcaResourceIncludeHandler::class,
                    'tableName' => 'tt_content',
                    'tableNameToResourceMap' => ['sys_file_reference' => 'file-references']
                ],
            ]
        ]
    ],
    'file-references' => [
        'repository' => [
            '__class__' => Repository\FileReferenceRepository::class
        ],
        'transformer' => [
            '__class__' => Transformer\FileReferenceTransformer::class,
        ]
    ],
    'files' => [
        'repository' => [
            '__class__' => Repository\FileRepository::class
        ],
        'transformer' => [
            '__class__' => Transformer\FileReferenceTransformer::class
        ]
    ]
];
