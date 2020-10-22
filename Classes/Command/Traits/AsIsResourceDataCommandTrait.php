<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Command\Traits;

use DFAU\ToujouApi\Command\AsIsResourceDataCommand;

trait AsIsResourceDataCommandTrait
{

    /**
     * @var array
     */
    protected $asIsResourceData;

    public function getAsIsResourceData(): ?array
    {
        return $this->asIsResourceData;
    }

    public function withAsIsResourceData(?array $asIsResourceData): AsIsResourceDataCommand
    {
        $target = clone $this;
        $target->asIsResourceData = $asIsResourceData;
        return $target;
    }
}
