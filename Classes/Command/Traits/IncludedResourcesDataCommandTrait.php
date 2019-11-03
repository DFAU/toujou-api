<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Command\Traits;


use DFAU\ToujouApi\Command\IncludedResourcesDataCommand;

trait IncludedResourcesDataCommandTrait
{

    /**
     * @var array
     */
    protected $includedResourceData;

    public function getIncludedResourcesData(): array
    {
        return $this->includedResourceData;
    }

    public function withIncludedResourcesData(array $includedResourceData): IncludedResourcesDataCommand
    {
        $target = clone $this;
        $target->includedResourceData = $includedResourceData;
        return $target;
    }

}
