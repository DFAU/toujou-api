<?php


namespace DFAU\ToujouApi\Command;


trait OriginalTcaResourceDataTrait
{

    /**
     * @var array
     */
    protected $originalResourceData = [];

    public function getOriginalResourceData(): array
    {
        return $this->originalResourceData;
    }

}
