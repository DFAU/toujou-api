<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\Command;


use DFAU\ToujouApi\Command\IncludedResourcesCommand;
use DFAU\ToujouApi\Command\IncludedResourcesTrait;
use DFAU\ToujouApi\Command\TcaResourceDataCommand;
use DFAU\ToujouApi\Command\TcaResourceDataTrait;

class CreateTcaResourceCommand implements TcaResourceDataCommand, IncludedResourcesCommand
{

    use TcaResourceDataTrait, IncludedResourcesTrait;

    public function __construct(string $resourceType, string $resourceIdentifier, array $resourceData, array $includedResources = [])
    {
        $this->resourceType = $resourceType;
        $this->resourceIdentifier = $resourceIdentifier;
        $this->resourceData = $resourceData;
        $this->includedResources = $includedResources;
    }
}
