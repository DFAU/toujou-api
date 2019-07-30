<?php

use DFAU\ToujouApi\CommandBus\ConfigurationLocator;
use DFAU\ToujouApi\Domain\Command\CreateTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\DataHandlerUnitOfWorkCommand;
use DFAU\ToujouApi\Domain\Command\DeleteTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\UpdateTcaResourceCommand;
use DFAU\ToujouApi\Domain\CommandHandler\DataHandlerCommandHandler;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameWithoutSuffixInflector;

return [
    'commands' => [
        CreateTcaResourceCommand::class => [DataHandlerCommandHandler::class, 'handleCreateTcaResource'],
        DataHandlerUnitOfWorkCommand::class => [DataHandlerCommandHandler::class, 'handleDataHandlerUnitOfWork'],
        UpdateTcaResourceCommand::class => [DataHandlerCommandHandler::class, 'handleUpdateTcaResource'],
        DeleteTcaResourceCommand::class => [DataHandlerCommandHandler::class, 'handleDeleteTcaResource'],
    ],
    'middlewares' => [
        'dfau/toujou-api/command-handler' => [
            'target' => [
                '__class__' => CommandHandlerMiddleware::class,
                'container' => ['__class__' => \DFAU\ToujouApi\CommandBus\CommandHandlerContainer::class],
                'mapping' => ['__class__' => \DFAU\ToujouApi\CommandBus\MapByConfiguration::class]
            ]
        ]
    ]
];
