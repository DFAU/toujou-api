<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Resource;

enum Operation: string
{
    case READ = 'read';
    case CREATE = 'create';
    case REPLACE = 'replace';
    case UPDATE = 'update';
    case DELETE = 'delete';
}
