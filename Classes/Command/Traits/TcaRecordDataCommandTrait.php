<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Command\Traits;

use DFAU\ToujouApi\Command\TcaRecordDataCommand;

trait TcaRecordDataCommandTrait
{
    use TcaRecordReferencingCommandTrait;

    /** @var array */
    protected $recordData;

    public function getRecordData(): ?array
    {
        return $this->recordData;
    }

    public function withRecordData(array $recordData): TcaRecordDataCommand
    {
        $target = clone $this;
        $target->recordData = $recordData;

        return $target;
    }
}
