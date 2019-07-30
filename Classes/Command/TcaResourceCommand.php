<?php


namespace DFAU\ToujouApi\Command;


interface TcaResourceCommand extends ResourceReferencingCommand
{

    public function getTableName(): string;

    public function getResourceData(): ?array;
}
