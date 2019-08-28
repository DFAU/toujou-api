<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Command;


interface UnitOfWorkCommand
{

    public function getUnitOfWorkCommands(): array;
}
