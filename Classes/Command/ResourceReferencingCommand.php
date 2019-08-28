<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Command;


interface ResourceReferencingCommand
{

    public function getResourceType(): string;

    public function getResourceIdentifier(): string;
}
