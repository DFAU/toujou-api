<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\Command;


use DFAU\ToujouApi\Command\TcaResourceDataCommand;
use DFAU\ToujouApi\Command\TcaResourceDataTrait;

class UpdateTcaResourceCommand implements TcaResourceDataCommand
{

    use TcaResourceDataTrait;

    public function __construct(string $resourceType, string $resourceIdentifier, array $resourceData)
    {
        $this->resourceType = $resourceType;
        $this->resourceIdentifier = $resourceIdentifier;
        $this->resourceData = $resourceData;
    }
}
