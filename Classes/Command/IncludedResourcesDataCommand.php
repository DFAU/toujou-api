<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Command;


interface IncludedResourcesDataCommand
{

    public function getIncludedResourcesData(): array;

    public function withIncludedResourcesData(array $resourceData): self;

}
