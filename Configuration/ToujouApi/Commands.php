<?php

use DFAU\ToujouApi\Domain\Command\CreateTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\DeleteTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\ReplaceTcaResourceCommand;
use DFAU\ToujouApi\Domain\Command\UpdateTcaResourceCommand;
use DFAU\ToujouApi\Domain\Repository\TcaResourceRepository;

return [
    'commands' => [
        CreateTcaResourceCommand::class => [
            'handler' => TcaResourceRepository::class
        ],
        ReplaceTcaResourceCommand::class => [
            'handler' => TcaResourceRepository::class
        ],
        UpdateTcaResourceCommand::class => [
            'handler' => TcaResourceRepository::class
        ],
        DeleteTcaResourceCommand::class => [
            'handler' => TcaResourceRepository::class
        ],
    ],
    'middleware' => [

    ]
];
