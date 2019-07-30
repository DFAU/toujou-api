<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Command\Traits;

trait TcaResourceTrait
{
    use ResourceReferencingTrait;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var array
     */
    protected $resourceData;

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getResourceData(): ?array
    {
        return $this->resourceData;
    }
}
