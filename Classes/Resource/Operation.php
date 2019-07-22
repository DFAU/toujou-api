<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Resource;


use TYPO3\CMS\Core\Type\Enumeration;

class Operation extends Enumeration
{

    const READ = 'read';

    const CREATE = 'create';

    const REPLACE = 'replace';

    const UPDATE = 'update';

    const DELETE = 'delete';
}
