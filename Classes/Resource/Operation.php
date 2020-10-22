<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Resource;

use TYPO3\CMS\Core\Type\Enumeration;

class Operation extends Enumeration
{
    /**
     * This override is necessary as the ValidatorResolver evaluates "mixed" into null which is causing a type Exception,
     * private/typo3/sysext/extbase/Classes/Validation/ValidatorResolver.php:153
     * @var string
     */
    protected $value;

    const READ = 'read';

    const CREATE = 'create';

    const REPLACE = 'replace';

    const UPDATE = 'update';

    const DELETE = 'delete';
}
