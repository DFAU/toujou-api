<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Command\Traits;


trait AsIsResourceDataCommandTrait
{

    /**
     * @var array
     */
    protected $asIsResourceData;

    public function getAsIsResourceData():?array
    {
        return $this->asIsResourceData;
    }
}
