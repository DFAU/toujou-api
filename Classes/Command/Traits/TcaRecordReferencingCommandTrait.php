<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Command\Traits;


trait TcaRecordReferencingCommandTrait
{

    /**
     * @var string
     */
    protected $uid;

    /**
     * @var string
     */
    protected $tableName;

    public function getUid(): string
    {
        return $this->uid;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

}
