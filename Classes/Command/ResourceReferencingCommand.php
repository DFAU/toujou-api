<?php


namespace DFAU\ToujouApi\Command;


interface ResourceReferencingCommand
{

    public function getResourceType(): string;

    public function getResourceIdentifier(): string;
}
