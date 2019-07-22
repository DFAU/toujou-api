<?php


namespace DFAU\ToujouApi\Command;


interface TcaResourceDataCommand extends ResourceReferencingCommand
{

    public function getResourceData(): array;
}
