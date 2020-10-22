<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Command;

interface TcaRecordReferencingCommand
{
    public function getUid(): string;

    public function getTableName(): string;
}
