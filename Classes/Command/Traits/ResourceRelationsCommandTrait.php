<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Command\Traits;


use DFAU\ToujouApi\Command\ResourceRelationsCommand;

trait ResourceRelationsCommandTrait
{

    /**
     * @var array
     */
    protected $resourceRelations = array();

    public function getResourceRelations(): array
    {
        return $this->resourceRelations;
    }

    public function withResourceRelations(array $resourceRelations): ResourceRelationsCommand
    {
        $target = clone $this;
        $target->resourceRelations = $resourceRelations;
        return $target;
    }

}
