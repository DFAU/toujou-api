<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Command;

interface TcaRecordDataCommand extends TcaRecordReferencingCommand
{
    public function getRecordData(): ?array;

    public function withRecordData(array $recordData): self;
}
