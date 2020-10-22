<?php

use DFAU\ToujouApi\Domain\Command\CreateTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\DeleteTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\ReplaceTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\UnitOfWorkTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\UpdateTcaResourceCommand;
use DFAU\ToujouApi\Domain\CommandHandler\ConvergenceCommandHandler;
use DFAU\ToujouApi\Domain\CommandHandler\DataHandlerCommandHandler;
use League\Tactician\Handler\CommandHandlerMiddleware;

return [
    'commands' => [
        CreateTcaResourceCommand::class => [DataHandlerCommandHandler::class, 'handleCreateTcaResourceCommand'],
        UpdateTcaResourceCommand::class => [DataHandlerCommandHandler::class, 'handleUpdateTcaResourceCommand'],
        DeleteTcaResourceCommand::class => [DataHandlerCommandHandler::class, 'handleDeleteTcaResourceCommand'],
        UnitOfWorkTcaResourceCommand::class => [DataHandlerCommandHandler::class, 'handleDataHandlerUnitOfWorkCommand'],
        ReplaceTcaResourceCommand::class => [ConvergenceCommandHandler::class, 'handleReplaceTcaResourceCommand'],
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
