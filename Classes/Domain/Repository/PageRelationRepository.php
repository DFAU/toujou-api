<?php


namespace DFAU\ToujouApi\Domain\Repository;


interface PageRelationRepository
{

    public function findByPageIdentifier($pageIdentifier): array;
}
