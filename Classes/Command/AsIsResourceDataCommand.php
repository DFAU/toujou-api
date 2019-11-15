<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Command;


interface AsIsResourceDataCommand
{

    public function getAsIsResourceData(): ?array;

    public function withAsIsResourceData(?array $asIsResourceData): self;

}
