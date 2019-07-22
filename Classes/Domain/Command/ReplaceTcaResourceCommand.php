<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\Command;


use DFAU\ToujouApi\Command\IncludedResourcesCommand;
use DFAU\ToujouApi\Command\IncludedResourcesTrait;
use DFAU\ToujouApi\Command\OriginalIncludedResourcesCommand;
use DFAU\ToujouApi\Command\OriginalIncludedResourcesTrait;
use DFAU\ToujouApi\Command\OriginalTcaResourceDataCommand;
use DFAU\ToujouApi\Command\OriginalTcaResourceDataTrait;
use DFAU\ToujouApi\Command\TcaResourceDataCommand;
use DFAU\ToujouApi\Command\TcaResourceDataTrait;

class ReplaceTcaResourceCommand implements TcaResourceDataCommand, IncludedResourcesCommand, OriginalTcaResourceDataCommand, OriginalIncludedResourcesCommand
{

    use TcaResourceDataTrait, IncludedResourcesTrait, OriginalTcaResourceDataTrait, OriginalIncludedResourcesTrait;

    public function __construct(string $resourceType, string $resourceIdentifier, array $resourceData, array $originalResourceData, array $includedResources = [], array $originalIncludedResources = [])
    {
        $this->resourceType = $resourceType;
        $this->resourceIdentifier = $resourceIdentifier;
        $this->resourceData = $resourceData;
        $this->originalResourceData = $originalResourceData;
        $this->includedResources = $includedResources;
        $this->originalIncludedResources = $originalIncludedResources;
    }
}
