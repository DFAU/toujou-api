<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Command;


trait TcaResourceDataTrait
{
    use ResourceReferencingTrait;

    /**
     * @var array
     */
    protected $resourceData = [];

    public function getResourceData(): array
    {
        return $this->resourceData;
    }
}
