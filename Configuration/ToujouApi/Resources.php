<?php

declare(strict_types=1);
use DFAU\ToujouApi\Domain\Command\CreateTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\DeleteTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\ReplaceTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\UpdateTcaResourceCommand;
use DFAU\ToujouApi\Domain\Repository\FileReferenceRepository;
use DFAU\ToujouApi\Domain\Repository\FileRepository;
use DFAU\ToujouApi\Domain\Repository\TcaResourceRepository;
use DFAU\ToujouApi\Domain\Transformer\FileReferenceTransformer;
use DFAU\ToujouApi\Domain\Transformer\FileTransformer;
use DFAU\ToujouApi\IncludeHandler\PageRelationIncludeHandler;
use DFAU\ToujouApi\IncludeHandler\TcaResourceIncludeHandler;
use DFAU\ToujouApi\Resource\Operation;
use DFAU\ToujouApi\Schema\PagesJsonApiSchema;
use DFAU\ToujouApi\Transformer as GenericTransformer;
use DFAU\ToujouApi\TransformHandler\MetaTransformHandler;
use DFAU\ToujouApi\TransformHandler\TcaResourceTransformHandler;

return [
    'pages' => [
        'repository' => [
            '__class__' => TcaResourceRepository::class,
            'tableName' => 'pages',
        ],
        'transformer' => [
            '__class__' => GenericTransformer\ComposableTransformer::class,
            'transformHandlers' => [
                ['__class__' => MetaTransformHandler::class],
                ['__class__' => TcaResourceTransformHandler::class, 'tableName' => 'pages'],
            ],
            'includeHandlers' => [
                [
                    '__class__' => PageRelationIncludeHandler::class,
                    'includeToResourceMap' => ['content-elements' => 'content-elements'],
                ],
            ],
        ],
        'operationToCommandMap' => [
            Operation::CREATE => ['__class__' => CreateTcaResourceCommand::class, 'tableName' => 'pages'],
            Operation::UPDATE => ['__class__' => UpdateTcaResourceCommand::class, 'tableName' => 'pages'],
            Operation::DELETE => ['__class__' => DeleteTcaResourceCommand::class, 'tableName' => 'pages'],
            Operation::REPLACE => ['__class__' => ReplaceTcaResourceCommand::class, 'tableName' => 'pages'],
        ],
        'convergenceSchema' => [
            '__class__' => PagesJsonApiSchema::class,
        ],
    ],
    'content-elements' => [
        'repository' => ['__class__' => TcaResourceRepository::class, 'tableName' => 'tt_content'],
        'transformer' => [
            '__class__' => GenericTransformer\ComposableTransformer::class,
            'transformHandlers' => [
                ['__class__' => MetaTransformHandler::class],
                ['__class__' => TcaResourceTransformHandler::class, 'tableName' => 'tt_content'],
            ],
            'includeHandlers' => [
                [
                    '__class__' => TcaResourceIncludeHandler::class,
                    'tableName' => 'tt_content',
                    'tableNameToResourceMap' => ['sys_file_reference' => 'file-references'],
                ],
            ],
        ],
        'operationToCommandMap' => [
            Operation::CREATE => ['__class__' => CreateTcaResourceCommand::class, 'tableName' => 'tt_content'],
            Operation::UPDATE => ['__class__' => UpdateTcaResourceCommand::class, 'tableName' => 'tt_content'],
            Operation::DELETE => ['__class__' => DeleteTcaResourceCommand::class, 'tableName' => 'tt_content'],
            Operation::REPLACE => ['__class__' => ReplaceTcaResourceCommand::class, 'tableName' => 'tt_content'],
        ],
    ],
    'file-references' => [
        'repository' => [
            '__class__' => FileReferenceRepository::class,
        ],
        'transformer' => [
            '__class__' => FileReferenceTransformer::class,
        ],
        'operationToCommandMap' => [
            Operation::CREATE => ['__class__' => CreateTcaResourceCommand::class, 'tableName' => 'sys_file_reference'],
            Operation::UPDATE => ['__class__' => UpdateTcaResourceCommand::class, 'tableName' => 'sys_file_reference'],
            Operation::DELETE => ['__class__' => DeleteTcaResourceCommand::class, 'tableName' => 'sys_file_reference'],
            Operation::REPLACE => ['__class__' => ReplaceTcaResourceCommand::class, 'tableName' => 'sys_file_reference'],
        ],
    ],
    'files' => [
        'repository' => [
            '__class__' => FileRepository::class,
        ],
        'transformer' => [
            '__class__' => FileTransformer::class,
        ],
        'operationToCommandMap' => [
            Operation::CREATE => ['__class__' => CreateTcaResourceCommand::class, 'tableName' => 'sys_file'],
            Operation::UPDATE => ['__class__' => UpdateTcaResourceCommand::class, 'tableName' => 'sys_file'],
            Operation::DELETE => ['__class__' => DeleteTcaResourceCommand::class, 'tableName' => 'sys_file'],
            Operation::REPLACE => ['__class__' => ReplaceTcaResourceCommand::class, 'tableName' => 'sys_file'],
        ],
    ],
];
