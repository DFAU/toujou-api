<?php

declare(strict_types=1);

use DFAU\ToujouApi\Domain\Command;
use DFAU\ToujouApi\Domain\Repository;
use DFAU\ToujouApi\Domain\Transformer;
use DFAU\ToujouApi\IncludeHandler;
use DFAU\ToujouApi\Resource\Operation;
use DFAU\ToujouApi\Transformer as GenericTransformer;
use DFAU\ToujouApi\TransformHandler;

return [
    'pages' => [
        'repository' => [
            '__class__' => Repository\TcaResourceRepository::class,
            'tableName' => 'pages',
        ],
        'transformer' => [
            '__class__' => GenericTransformer\ComposableTransformer::class,
            'transformHandlers' => [
                ['__class__' => TransformHandler\MetaTransformHandler::class],
                ['__class__' => TransformHandler\TcaResourceTransformHandler::class, 'tableName' => 'pages'],
            ],
            'includeHandlers' => [
                [
                    '__class__' => IncludeHandler\PageRelationIncludeHandler::class,
                    'includeToResourceMap' => ['content-elements' => 'content-elements'],
                ],
            ],
        ],
        'operationToCommandMap' => [
            Operation::CREATE => ['__class__' => Command\CreateTcaResourceCommand::class, 'tableName' => 'pages'],
            Operation::UPDATE => ['__class__' => Command\UpdateTcaResourceCommand::class, 'tableName' => 'pages'],
            Operation::DELETE => ['__class__' => Command\DeleteTcaResourceCommand::class, 'tableName' => 'pages'],
            Operation::REPLACE => ['__class__' => Command\ReplaceTcaResourceCommand::class, 'tableName' => 'pages'],
        ],
        'convergenceSchema' => [
            '__class__' => \DFAU\ToujouApi\Schema\PagesJsonApiSchema::class,
        ],
    ],
    'content-elements' => [
        'repository' => ['__class__' => Repository\TcaResourceRepository::class, 'tableName' => 'tt_content'],
        'transformer' => [
            '__class__' => GenericTransformer\ComposableTransformer::class,
            'transformHandlers' => [
                ['__class__' => TransformHandler\MetaTransformHandler::class],
                ['__class__' => TransformHandler\TcaResourceTransformHandler::class, 'tableName' => 'tt_content'],
            ],
            'includeHandlers' => [
                [
                    '__class__' => IncludeHandler\TcaResourceIncludeHandler::class,
                    'tableName' => 'tt_content',
                    'tableNameToResourceMap' => ['sys_file_reference' => 'file-references'],
                ],
            ],
        ],
        'operationToCommandMap' => [
            Operation::CREATE => ['__class__' => Command\CreateTcaResourceCommand::class, 'tableName' => 'tt_content'],
            Operation::UPDATE => ['__class__' => Command\UpdateTcaResourceCommand::class, 'tableName' => 'tt_content'],
            Operation::DELETE => ['__class__' => Command\DeleteTcaResourceCommand::class, 'tableName' => 'tt_content'],
            Operation::REPLACE => ['__class__' => Command\ReplaceTcaResourceCommand::class, 'tableName' => 'tt_content'],
        ],
    ],
    'file-references' => [
        'repository' => [
            '__class__' => Repository\FileReferenceRepository::class,
        ],
        'transformer' => [
            '__class__' => Transformer\FileReferenceTransformer::class,
        ],
        'operationToCommandMap' => [
            Operation::CREATE => ['__class__' => Command\CreateTcaResourceCommand::class, 'tableName' => 'sys_file_reference'],
            Operation::UPDATE => ['__class__' => Command\UpdateTcaResourceCommand::class, 'tableName' => 'sys_file_reference'],
            Operation::DELETE => ['__class__' => Command\DeleteTcaResourceCommand::class, 'tableName' => 'sys_file_reference'],
            Operation::REPLACE => ['__class__' => Command\ReplaceTcaResourceCommand::class, 'tableName' => 'sys_file_reference'],
        ],
    ],
    'files' => [
        'repository' => [
            '__class__' => Repository\FileRepository::class,
        ],
        'transformer' => [
            '__class__' => Transformer\FileReferenceTransformer::class,
        ],
        'operationToCommandMap' => [
            Operation::CREATE => ['__class__' => Command\CreateTcaResourceCommand::class, 'tableName' => 'sys_file'],
            Operation::UPDATE => ['__class__' => Command\UpdateTcaResourceCommand::class, 'tableName' => 'sys_file'],
            Operation::DELETE => ['__class__' => Command\DeleteTcaResourceCommand::class, 'tableName' => 'sys_file'],
            Operation::REPLACE => ['__class__' => Command\ReplaceTcaResourceCommand::class, 'tableName' => 'sys_file'],
        ],
    ],
];
