<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Resource;

use TYPO3\CMS\Core\Type\Enumeration;

class Operation extends Enumeration
{
    /**
     * This override is necessary as the ValidatorResolver evaluates "mixed" into null which is causing a type Exception,
     * private/typo3/sysext/extbase/Classes/Validation/ValidatorResolver.php:153
     *
     * @var string
     */
    protected $value;

    public const READ = 'read';

    public const CREATE = 'create';

    public const REPLACE = 'replace';

    public const UPDATE = 'update';

    public const DELETE = 'delete';
}
