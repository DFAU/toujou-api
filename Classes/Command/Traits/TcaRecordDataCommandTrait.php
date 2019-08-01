<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Command\Traits;

trait TcaRecordDataCommandTrait
{
    use TcaRecordReferencingCommandTrait;

    /**
     * @var array
     */
    protected $recordData;

    public function getRecordData(): ?array
    {
        return $this->recordData;
    }
}
