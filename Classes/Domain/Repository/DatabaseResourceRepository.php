<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Domain\Repository;

interface DatabaseResourceRepository
{
    public const META_UID = 'uid';

    public function getTableName(): string;
}
