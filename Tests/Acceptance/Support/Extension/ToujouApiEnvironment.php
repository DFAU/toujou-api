<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Acceptance\Support\Extension;

use TYPO3\TestingFramework\Core\Acceptance\Extension\BackendEnvironment;

class ToujouApiEnvironment extends BackendEnvironment
{
    /** @var \string[][] */
    protected $localConfig = [
        'coreExtensionsToLoad' => [
            'core',
            'extbase',
            'fluid',
            'backend',
            'install',
            'frontend',
        ],
        'testExtensionsToLoad' => [
            'typo3conf/ext/toujou_api',
            'typo3conf/ext/toujou_oauth2_server',
            'typo3conf/ext/toujou_api/Tests/Acceptance/Fixtures/Extensions/toujou_apitest',
        ],
        'xmlDatabaseFixtures' => [
            'typo3conf/ext/toujou_api/Tests/Acceptance/Fixtures/be_users.xml',
            'typo3conf/ext/toujou_api/Tests/Acceptance/Fixtures/tx_toujou_oauth2_server_client.xml',
            'typo3conf/ext/toujou_api/Tests/Acceptance/Fixtures/pages.xml',
            'typo3conf/ext/toujou_api/Tests/Acceptance/Fixtures/tt_content.xml',
        ],
        'pathsToLinkInTestInstance' => [
            'typo3conf/ext/toujou_api/Tests/Acceptance/_data/sites' => 'typo3conf/sites',
        ],
    ];
}
