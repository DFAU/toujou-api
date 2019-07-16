<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\Repository;


interface DatabaseResourceRepository
{

    public function getTableName(): string;

}
