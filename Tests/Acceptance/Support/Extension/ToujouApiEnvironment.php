<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Acceptance\Support\Extension;

use TYPO3\TestingFramework\Core\Acceptance\Extension\BackendEnvironment;

class ToujouApiEnvironment extends BackendEnvironment
{
    /** @var \string[][]  */
    protected $localConfig = [
        'coreExtensionsToLoad' => [
            'core',
            'extbase',
            'fluid',
            'backend',
            'install',
            'frontend',
            'recordlist',
        ],
        'testExtensionsToLoad' => [
            'typo3conf/ext/toujou_api',
        ],
        'xmlDatabaseFixtures' => [
            'PACKAGE:typo3/testing-framework/Resources/Core/Acceptance/Fixtures/be_users.xml',
            'PACKAGE:typo3/testing-framework/Resources/Core/Acceptance/Fixtures/be_groups.xml',
        ],
    ];
}
