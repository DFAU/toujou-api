<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\Command;


use DFAU\ToujouApi\Command\ResourceReferencingCommand;
use DFAU\ToujouApi\Command\ResourceReferencingTrait;

class DeleteTcaResourceCommand implements ResourceReferencingCommand
{
    use ResourceReferencingTrait;
}
